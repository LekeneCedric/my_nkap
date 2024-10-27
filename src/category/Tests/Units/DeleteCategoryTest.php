<?php

namespace App\category\Tests\Units;

use App\category\Application\Command\Delete\DeleteCategoryHandler;
use App\category\Application\Command\Delete\DeleteCategoryResponse;
use App\category\Domain\Enums\CategoryEventStateEnum;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\Exceptions\NotFoundUserCategoryException;
use App\category\Domain\UserCategoryRepository;
use App\category\Tests\Units\Builders\DeleteCategoryCommandBuilder;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    private UserCategoryRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryUserCategoryRepository();
    }

    /**
     * @return void
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    public function test_can_delete_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingUser()
            ->withExistingCategory(
                icon: 'car',
                name: 'Transport',
                color: 'green',
                description: 'Transport'
            )->build();
        $this->saveInitDataInMemory($initSUT);

        $userId = $initSUT->user->id->value();
        $categoryIdValue = $initSUT->user->categories[0]->categoryId->value();

        $command = DeleteCategoryCommandBuilder::asCommand()
            ->withUserId($userId)
            ->withCategoryId($categoryIdValue)
            ->build();

        $response = $this->deleteCategory($command);

        $deletedCategory = $this->repository->users[$userId]->categories[0];
        $this->assertTrue($response->isDeleted);
        $this->assertNotNull($response->message);
        $this->assertEquals(CategoryEventStateEnum::onDelete, $deletedCategory->eventState());
    }

    /**
     * @return void
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    public function test_can_throw_not_found_user_exception()
    {
        $command = DeleteCategoryCommandBuilder::asCommand()
            ->withUserId('not_existing_user_id')
            ->withCategoryId('category_id')
            ->build();

        $this->expectException(NotFoundUserCategoryException::class);
        $this->deleteCategory($command);
    }

    /**
     * @return void
     * @throws NotFoundUserCategoryException
     */
    public function test_can_throw_not_found_category_exception()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingUser()
            ->withExistingCategory(
                icon: 'car',
                name: 'Transport',
                color: 'green',
                description: 'Transport'
            )->build();
        $this->saveInitDataInMemory($initSUT);

        $userId = $initSUT->user->id->value();

        $command = DeleteCategoryCommandBuilder::asCommand()
            ->withUserId($userId)
            ->withCategoryId('category_id')
            ->build();

        $this->expectException(NotFoundCategoryException::class);
        $this->deleteCategory($command);
    }

    /**
     * @param $command
     * @return DeleteCategoryResponse
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    private function deleteCategory($command): DeleteCategoryResponse
    {
        $handler = new DeleteCategoryHandler(
            repository: $this->repository
        );
        return $handler->handle($command);
    }

    private function saveInitDataInMemory(CategorySUT $initSUT): void
    {
        $this->repository->users[$initSUT->user->id->value()] = $initSUT->user;
    }
}
