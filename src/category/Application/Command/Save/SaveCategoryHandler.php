<?php

namespace App\category\Application\Command\Save;

use App\category\Domain\Exceptions\AlreadyExistsCategoryException;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\Exceptions\NotFoundUserCategoryException;
use App\category\Domain\UserCategory;
use App\category\Domain\UserCategoryRepository;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;

class SaveCategoryHandler
{
    public function __construct(
        private UserCategoryRepository $repository,
    )
    {
    }

    /**
     * @param SaveCategoryCommand $command
     * @return SaveCategoryResponse
     * @throws AlreadyExistsCategoryException
     * @throws NotFoundUserCategoryException
     * @throws NotFoundCategoryException
     */
    public function handle(SaveCategoryCommand $command): SaveCategoryResponse
    {
        $response = new SaveCategoryResponse();

        $user = $this->getUserCategoryOrThrowNotFoundException($command);
        if (!$command->categoryId) {
            $user->addCategory(
                icon: new StringVO($command->categoryIcon),
                name: new StringVO($command->categoryName),
                color: new StringVO($command->categoryColor),
                description: new StringVO($command->categoryDescription),
            );
        }
        if ($command->categoryId) {
            $user->updateCategory(
                icon: new StringVO($command->categoryIcon),
                name: new StringVO($command->categoryName),
                color: new StringVO($command->categoryColor),
                description: new StringVO($command->categoryDescription),
                id: new Id($command->categoryId)
            );
        }


        $this->repository->save($user);

        $response->isSaved = true;
        $response->message = $command->categoryId ? 'category-updated' : 'category-created';
        $response->categoryId = $user->currentCategory()->categoryId->value();
        return $response;
    }

    /**
     * @param SaveCategoryCommand $command
     * @return UserCategory|null
     * @throws NotFoundUserCategoryException
     */
    public function getUserCategoryOrThrowNotFoundException(SaveCategoryCommand $command): ?UserCategory
    {
        $user = $this->repository->ofId(new Id($command->userId));
        if (!$user) throw new NotFoundUserCategoryException();
        return $user;
    }
}
