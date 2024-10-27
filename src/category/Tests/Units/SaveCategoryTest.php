<?php

namespace App\category\Tests\Units;

use App\category\Application\Command\Save\SaveCategoryCommand;
use App\category\Application\Command\Save\SaveCategoryHandler;
use App\category\Application\Command\Save\SaveCategoryResponse;
use App\category\Domain\Enums\CategoryEventStateEnum;
use App\category\Domain\Exceptions\AlreadyExistsCategoryException;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\Exceptions\NotFoundUserCategoryException;
use App\category\Domain\UserCategoryRepository;
use App\category\Tests\Units\Builders\SaveCategoryCommandBuilder;
use Tests\TestCase;

class SaveCategoryTest extends TestCase
{
    private UserCategoryRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryUserCategoryRepository();
    }

    /**
     * @return void
     * @throws AlreadyExistsCategoryException
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    public function test_can_create_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingUser()
            ->build();
        $this->saveDataInMemory($initSUT);
        $userId = $initSUT->user->id->value();
        $command = SaveCategoryCommandBuilder::asCommand()
            ->withUserId($userId)
            ->withCategoryIcon('car')
            ->withCategoryName('transport')
            ->withCategoryColor('green')
            ->withCategoryDescription('This category is for transport')
            ->build();

        $response = $this->saveCategory($command);

        $user = $this->repository->users[$userId];
        $createdCategory = $user->currentCategory();

        $this->assertTrue($response->isSaved);
        $this->assertNotNull($response->message);
        $this->assertEquals($createdCategory->categoryId->value(), $response->categoryId);
        $this->assertEquals('transport', $createdCategory->name->value());
        $this->assertEquals(CategoryEventStateEnum::onCreate, $createdCategory->eventState());
    }

    /**
     * @return void
     * @throws AlreadyExistsCategoryException
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    public function test_can_update_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingUser()
            ->withExistingCategory(
                icon: 'ball',
                name: 'sport',
                color: 'green',
                description: 'This category is for my sport equipment !'
            )
            ->build();
        $this->saveDataInMemory($initSUT);
        $userId = $initSUT->user->id->value();
        $categoryId = $initSUT->user->categories[0]->categoryId->value();

        $command = SaveCategoryCommandBuilder::asCommand()
            ->withUserId($userId)
            ->withCategoryId($categoryId)
            ->withCategoryIcon('car')
            ->withCategoryColor('green')
            ->withCategoryName('transport')
            ->withCategoryDescription('This category is for transport')
            ->build();

        $response = $this->saveCategory($command);

        $user = $this->repository->users[$userId];
        $updatedCategory = $user->currentCategory();

        $this->assertTrue($response->isSaved);
        $this->assertNotNull($response->message);
        $this->assertEquals('transport', $updatedCategory->name->value());
        $this->assertEquals(CategoryEventStateEnum::onUpdate, $updatedCategory->eventState());
    }

    /**
     * @return void
     * @throws AlreadyExistsCategoryException
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    public function test_can_throw_already_exist_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingUser()
            ->withExistingCategory(
                icon: 'car',
                name: 'transport',
                color: 'green',
                description: 'This category is for transport'
            )->build();
        $this->saveDataInMemory($initSUT);
        $userId = $initSUT->user->id->value();

        $command = SaveCategoryCommandBuilder::asCommand()
            ->withUserId($userId)
            ->withCategoryIcon('car')
            ->withCategoryName('transport')
            ->withCategoryColor('green')
            ->withCategoryDescription('This category is for transport')
            ->build();

        $this->expectException(AlreadyExistsCategoryException::class);
        $this->saveCategory($command);
    }

    /**
     * @return void
     * @throws AlreadyExistsCategoryException
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    public function test_can_throw_not_found_user()
    {
        $command = SaveCategoryCommandBuilder::asCommand()
            ->withUserId('not_existing_user_id')
            ->withCategoryIcon('car')
            ->withCategoryName('transport')
            ->withCategoryColor('green')
            ->withCategoryDescription('This category is for transport')
            ->build();

        $this->expectException(NotFoundUserCategoryException::class);
        $this->saveCategory($command);
    }

    /**
     * @return void
     * @throws AlreadyExistsCategoryException
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    public function test_can_throw_not_found_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingUser()
            ->withExistingCategory(
                icon: 'car',
                name: 'transport',
                color: 'green',
                description: 'This category is for transport'
            )->build();
        $this->saveDataInMemory($initSUT);
        $userId = $initSUT->user->id->value();
        $categoryId = 'not_existing_category_id';

        $command = SaveCategoryCommandBuilder::asCommand()
            ->withUserId($userId)
            ->withCategoryId($categoryId)
            ->withCategoryColor('green')
            ->withCategoryIcon('music')
            ->withCategoryName('music')
            ->withCategoryDescription('For music equipments')
            ->build();

        $this->expectException(NotFoundCategoryException::class);
        $this->saveCategory($command);
    }

    /**
     * @param CategorySUT $initSUT
     * @return void
     */
    public function saveDataInMemory(CategorySUT $initSUT): void
    {
        $this->repository->users[$initSUT->user->id->value()] = $initSUT->user;
    }

    /**
     * @param SaveCategoryCommand $command
     * @return SaveCategoryResponse
     * @throws AlreadyExistsCategoryException
     * @throws NotFoundCategoryException
     * @throws NotFoundUserCategoryException
     */
    private function saveCategory(SaveCategoryCommand $command): SaveCategoryResponse
    {
        $handler = new SaveCategoryHandler(
            repository: $this->repository
        );
        return $handler->handle($command);
    }
}
