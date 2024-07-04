<?php

namespace App\category\Application\Command\Delete;

use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\Exceptions\NotFoundUserCategoryException;
use App\category\Domain\UserCategory;
use App\category\Domain\UserCategoryRepository;
use App\Shared\VO\Id;

class DeleteCategoryHandler
{
    public function __construct(
        private UserCategoryRepository $repository,
    )
    {
    }

    /**
     * @param DeleteCategoryCommand $command
     * @return DeleteCategoryResponse
     * @throws NotFoundUserCategoryException
     * @throws NotFoundCategoryException
     */
    public function handle(DeleteCategoryCommand $command): DeleteCategoryResponse
    {
        $response = new DeleteCategoryResponse();

        $user = $this->getUserOrThrowNotfoundException($command);
        $user->deleteCategory(new Id($command->categoryId));

        $this->repository->save($user);

        $response->isDeleted = true;
        $response->message = 'Catégorie supprimée avec succès !';
        return $response;
    }

    /**
     * @param DeleteCategoryCommand $command
     * @return UserCategory
     * @throws NotFoundUserCategoryException
     */
    private function getUserOrThrowNotfoundException(DeleteCategoryCommand $command): UserCategory
    {
        $userCategory = $this->repository->ofId(new Id($command->userId));
        if (!$userCategory) throw new NotFoundUserCategoryException();
        return $userCategory;
    }
}
