<?php

namespace App\category\Infrastructure\Repository;

use App\category\Domain\Category;
use App\category\Domain\Enums\EventState\CategoryEventStateEnum;
use App\category\Domain\Exceptions\ErrorOnSaveCategoryException;
use App\category\Domain\UserCategory;
use App\category\Domain\UserCategoryRepository;
use App\category\Infrastructure\Models\Category as CategoryModel;
use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class EloquentUserCategoryRepository implements UserCategoryRepository
{

    public function ofId(Id $id): ?UserCategory
    {
        $user = User::whereUuid($id->value())->first();
        if (!$user) return null;
        $categories = $user->categories
            ->map(fn(CategoryModel $category) => $category->toDomain())
            ->toArray();
        return new UserCategory(id: new Id($user->uuid), categories: $categories);
    }

    /**
     * @param UserCategory $user
     * @return void
     * @throws ErrorOnSaveCategoryException
     */
    public function save(UserCategory $user): void
    {
        try {
            DB::transaction(function () use ($user) {
                $user_id = User::whereUuid($user->id->value())->first()->id;
                $category = $user->currentCategory();
                match ($category->eventState()) {
                    CategoryEventStateEnum::onCreate => $this->createCategory($user_id, $category),
                    CategoryEventStateEnum::onUpdate => $this->updateCategory($category),
                    CategoryEventStateEnum::onDelete => $this->deleteCategory($category),
                };
            });
        } catch (Exception $e) {
            throw new ErrorOnSaveCategoryException($e->getMessage());
        }
    }

    private function createCategory(int $user_id, Category $category): void
    {
        $data = array_merge($category->toArray(), ['user_id' => $user_id]);
        CategoryModel::create($data);
    }

    private function updateCategory(Category $category): void
    {
        $categoryId = $category->categoryId->value();
        $data = $category->toArray();
        CategoryModel::whereUuid($categoryId)->first()->fill($data)->save();
    }

    private function deleteCategory(Category $category): void
    {
        CategoryModel::whereUuid($category->categoryId->value())
            ->delete();
    }
}
