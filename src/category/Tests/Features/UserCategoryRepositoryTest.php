<?php

namespace App\category\Tests\Features;

use App\category\Domain\Exceptions\AlreadyExistsCategoryException;
use App\category\Domain\Exceptions\ErrorOnSaveCategoryException;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\UserCategoryRepository;
use App\category\Infrastructure\Repository\EloquentUserCategoryRepository;
use App\Shared\VO\Id;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;
    private UserCategoryRepository $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserCategoryRepository();
    }

    /**
     * @return void
     * @throws AlreadyExistsCategoryException
     * @throws ErrorOnSaveCategoryException
     * @throws NotFoundCategoryException
     */
    public function test_can_create_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withToAddCategory(
                icon: 'taxi',
                name: 'transport',
                description: 'Category for my transport expenses'
            )->build();

        $userCategory = $initSUT->userCategory;
        $this->repository->save($userCategory);

        $createdUserCategory = User::whereUuid($userCategory->id->value())->first();
        $categories = $createdUserCategory->categories;

        $this->assertCount(1, $categories);
        $this->assertEquals('taxi', $categories[0]->icon);
        $this->assertEquals('transport', $categories[0]->name);
    }

    /**
     * @return void
     * @throws AlreadyExistsCategoryException
     * @throws ErrorOnSaveCategoryException
     * @throws NotFoundCategoryException
     */
    public function test_can_update_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingCategory(
                icon: 'taxi',
                name: 'taxi',
                description: 'TransportExpense'
            )
            ->withToUpdateCategory(
                icon: 'taxi',
                name: 'transport',
                description: 'Category for my transport expenses'
            )->build();

        $userCategory = $initSUT->userCategory;
        $this->repository->save($userCategory);

        $updatedUserCategory = User::whereUuid($userCategory->id->value())->first();
        $categories = $updatedUserCategory->categories;

        $this->assertCount(1, $categories);
        $this->assertEquals('taxi', $categories[0]->icon);
        $this->assertEquals('transport', $categories[0]->name);
        $this->assertEquals('Category for my transport expenses', $categories[0]->description);
    }

    /**
     * @throws NotFoundCategoryException
     * @throws ErrorOnSaveCategoryException
     * @throws AlreadyExistsCategoryException
     */
    public function test_can_delete_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingCategory(
                icon: 'taxi',
                name: 'taxi',
                description: 'TransportExpense'
            )->build();
        $userCategory = $initSUT->userCategory;
        $userCategory->deleteCategory(categoryId: new Id($initSUT->existingCategory->uuid));
        $this->repository->save($userCategory);

        $updatedUserCategory = User::whereUuid($userCategory->id->value())->first();
        $categories = $updatedUserCategory->categories;

        $this->assertCount(0, $categories);
    }
}
