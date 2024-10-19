<?php

namespace App\Operation\Tests\Units;

use App\category\Domain\Exceptions\EmptyCategoriesException;
use App\Operation\Application\Command\MakeAIOperation\MakeAIOperationCommand;
use App\Operation\Application\Command\MakeAIOperation\MakeAIOperationHandler;
use App\Operation\Application\Command\MakeAIOperation\MakeAIOperationResponse;
use App\Operation\Domain\Exceptions\AIOperationEmptyMessageException;
use App\Operation\Domain\OperationUser;
use App\Operation\Domain\Services\AIService;
use App\Operation\Domain\Services\GetOperationUserService;
use App\Operation\Tests\Units\Services\InMemoryAIService;
use App\Operation\Tests\Units\Services\InMemoryGetOperationUserService;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Repository\UserRepository;
use App\User\Tests\Units\Repository\InMemoryUserRepository;
use Tests\TestCase;

class MakeAIOperationsTest extends TestCase
{
    private AIService $AIService;
    private GetOperationUserService $getOperationUserService;
    private UserRepository $userRepository;
    public function setUp(): void
    {
        parent::setUp();
        $this->AIService = new InMemoryAIService();
        $this->getOperationUserService = new InMemoryGetOperationUserService();
        $this->userRepository = new InMemoryUserRepository();
    }

    /**
     * @throws AIOperationEmptyMessageException
     * @throws EmptyCategoriesException
     * @throws NotFoundUserException
     */
    public function test_can_make_ai_operation()
    {
        $initSUT = $this->buildSUT();
        $command = new MakeAIOperationCommand(
            userId: $initSUT['userId'],
            categories: [
                [
                    'id' => (new Id())->value(),
                    'label' => 'Category 1'
                ],
                [
                    'id' => (new Id())->value(),
                    'label' => 'Category 2'
                ],
            ],
            currentDate: '2021-01-01',
            message: 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s',
            language: 'en',
        );

        $response = $this->makeAIOperation($command);

        $user = $this->getOperationUserService->users[$initSUT['userId']];
        $this->assertTrue($response->operationOk);
        $this->assertNotEmpty($response->consumedToken);
        $this->assertNotNull($response->operations);
        $this->assertEquals(0, $user->token());
    }

    /**
     * @throws EmptyCategoriesException
     * @throws AIOperationEmptyMessageException
     * @throws NotFoundUserException
     */
    private function makeAIOperation(MakeAIOperationCommand $command): MakeAIOperationResponse
    {
        $handler = new MakeAIOperationHandler(
            AIService: $this->AIService,
            getOperationUserService: $this->getOperationUserService,
            userRepository: $this->userRepository,
        );
        return $handler->handle($command);
    }

    /**
     * @return array
     */
    private function buildSUT(): array
    {
        $userId = new Id();
        $this->getOperationUserService->users[$userId->value()] = OperationUser::create(
            id: $userId,
            token: 100,
            updatedTokenDate: new DateVO(),
        );
        return [
            'userId' => $userId->value(),
        ];
    }
}
