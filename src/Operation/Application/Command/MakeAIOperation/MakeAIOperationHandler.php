<?php

namespace App\Operation\Application\Command\MakeAIOperation;

use App\category\Domain\Exceptions\EmptyCategoriesException;
use App\Operation\Domain\Exceptions\AIOperationEmptyMessageException;
use App\Operation\Domain\OperationsMessagesEnum;
use App\Operation\Domain\OperationUser;
use App\Operation\Domain\Services\AIService;
use App\Operation\Domain\Services\GetOperationUserService;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Repository\UserRepository;

class MakeAIOperationHandler
{
    public function __construct(
        private AIService $AIService,
        private GetOperationUserService $getOperationUserService,
        private UserRepository $userRepository,
    )
    {
    }

    /**
     * @param MakeAIOperationCommand $command
     * @return MakeAIOperationResponse
     * @throws AIOperationEmptyMessageException
     * @throws EmptyCategoriesException
     * @throws NotFoundUserException
     */
    public function handle(MakeAIOperationCommand $command): MakeAIOperationResponse
    {
        $response = new MakeAIOperationResponse();
        $user = $this->getUserOrThrowNotFoundException($command->userId);
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
            $user->retrievedConsumedToken($makeAIOperationResponse->consumedToken());
            $this->userRepository->updateToken($user);
            $response->operations = $makeAIOperationResponse->operations();
            $response->consumedToken = $makeAIOperationResponse->consumedToken();
        }
        if (!$response->operationOk) {
            $response->message = OperationsMessagesEnum::AIOperationFailed;
        }
        return $response;
    }

    /**
     * @param string $userId
     * @return OperationUser
     * @throws NotFoundUserException
     */
    private function getUserOrThrowNotFoundException(string $userId): OperationUser
    {
        $user = $this->getOperationUserService->execute($userId);
        if (!$user) {
            throw new NotFoundUserException();
        }
        return $user;
    }
}
