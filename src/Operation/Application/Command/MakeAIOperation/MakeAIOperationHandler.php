<?php

namespace App\Operation\Application\Command\MakeAIOperation;

use App\category\Domain\Exceptions\EmptyCategoriesException;
use App\Operation\Domain\Exceptions\AIOperationEmptyMessageException;
use App\Operation\Domain\OperationsMessagesEnum;
use App\Operation\Domain\OperationUser;
use App\Operation\Domain\Services\AIService;
use App\Operation\Domain\Services\GetOperationUserService;
use App\Subscription\Domain\Services\SubscriptionService;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Repository\UserRepository;

class MakeAIOperationHandler
{
    public function __construct(
        private AIService $AIService,
        private SubscriptionService $subscriptionService,
    )
    {
    }

    /**
     * @param MakeAIOperationCommand $command
     * @return MakeAIOperationResponse
     * @throws AIOperationEmptyMessageException
     * @throws EmptyCategoriesException
     */
    public function handle(MakeAIOperationCommand $command): MakeAIOperationResponse
    {
        $response = new MakeAIOperationResponse();
        if (empty($command->categories)) {
            throw new EmptyCategoriesException();
        }
        if (empty($command->message)) {
            throw new AIOperationEmptyMessageException();
        }
        $makeAIOperationResponse = $this->AIService->makeOperation(
            accounts: $command->accounts,
            categories: $command->categories,
            message: $command->message,
            currentDate: $command->currentDate,
            language: $command->language
        );
        $response->operationOk = $makeAIOperationResponse->operationIsOk();
        if ($response->operationOk) {
            $this->subscriptionService->retrieveUserToken(
                userId: $command->userId,
                consumedToken: $makeAIOperationResponse->consumedToken()
            );
            $response->operations = $makeAIOperationResponse->operations();
            $response->consumedToken = $makeAIOperationResponse->consumedToken();
        }
        if (!$response->operationOk) {
            $response->message = OperationsMessagesEnum::AIOperationFailed;
        }
        return $response;
    }
}
