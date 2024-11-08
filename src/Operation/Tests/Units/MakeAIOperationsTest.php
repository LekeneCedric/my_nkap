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
use App\Subscription\Domain\Services\SubscriptionService;
use App\Subscription\Tests\Units\Services\InMemorySubscriptionService;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Repository\UserRepository;
use App\User\Tests\Units\Repository\InMemoryUserRepository;
use Tests\TestCase;

class MakeAIOperationsTest extends TestCase
{
    private AIService $AIService;
    private SubscriptionService $subscriptionService;
    public function setUp(): void
    {
        parent::setUp();
        $this->AIService = new InMemoryAIService();
        $this->subscriptionService = new InMemorySubscriptionService();
    }

    /**
     * @return void
     * @throws AIOperationEmptyMessageException
     * @throws EmptyCategoriesException
     */
    public function test_can_make_ai_operation()
    {
        $initSUT = $this->buildSUT();
        $command = new MakeAIOperationCommand(
            userId: $initSUT['userId'],
            accounts: [
                [
                    'id' => (new Id())->value(),
                    'label' => 'Account 1',
                ],
                [
                    'id' => (new Id())->value(),
                    'label' => 'Account 2',
                ],
            ],
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

        $this->assertTrue($response->operationOk);
        $this->assertNotEmpty($response->consumedToken);
        $this->assertNotNull($response->operations);
    }
//#TODO: SHOULD TEST SUBSCRIPTION SERVICE RETRIEVE TOKEN

    /**
     * @param MakeAIOperationCommand $command
     * @return MakeAIOperationResponse
     * @throws AIOperationEmptyMessageException
     * @throws EmptyCategoriesException
     */
    private function makeAIOperation(MakeAIOperationCommand $command): MakeAIOperationResponse
    {
        $handler = new MakeAIOperationHandler(
            AIService: $this->AIService,
            subscriptionService: $this->subscriptionService,
        );
        return $handler->handle($command);
    }

    /**
     * @return array
     */
    private function buildSUT(): array
    {
        $userId = new Id();
        return [
            'userId' => $userId->value(),
        ];
    }
}
