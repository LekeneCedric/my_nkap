<?php

namespace App\User\Application\Command\VerificationAccount;

use App\Subscription\Domain\Services\SubscriptionService;
use App\User\Domain\Enums\UserMessagesEnum;
use App\User\Domain\Enums\UserStatusEnum;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Exceptions\UnknownVerificationCodeException;
use App\User\Domain\Exceptions\VerificationCodeNotMatchException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\User;
use App\User\Infrastructure\Models\User as UserModel;
class VerificationAccountHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private SubscriptionService $subscriptionService,
    )
    {
    }

    /**
     * @throws ErrorOnSaveUserException
     * @throws VerificationCodeNotMatchException
     * @throws UnknownVerificationCodeException
     * @throws NotFoundUserException
     */
    public function handle(VerificationAccountCommand $command): VerificationAccountResponse
    {
        $response = new VerificationAccountResponse();
        $user = $this->getUserByEmailOrThrowNotFoundException($command->email);
        $user->checkIfCodeIsCorrectOrThrowException($command->code);
        $user->activateAccount();

        $this->userRepository->update($user);

        $user->publishUserVerified();

        $subscriptionData = $this->subscriptionService->getUserSubscriptionData(
            userId: $user->id()->value(),
        );

        $response->accountVerified = true;
        $response->message = UserMessagesEnum::ACCOUNT_VERIFIED;
        $response->userData = [
            ...$user->publicInfo(),
            ...[
                'subscriptionId' => $subscriptionData['subscriptionId'],
                'subscriptionStatedAt' => $subscriptionData['start_date'],
                'subscriptionEndAt' => $subscriptionData['end_date'],
                'nbTokens' => $subscriptionData['nb_token'],
                'nbOperations' => $subscriptionData['nb_operations'],
                'nbAccounts' => $subscriptionData['nb_accounts'],
                'nbTokensUpdatedAt' => $subscriptionData['nb_token_updated_at'],
                'nbOperationsUpdatedAt' => $subscriptionData['nb_operations_updated_at'],
            ]
        ];
        $response->countUsers = UserModel::where('status', UserStatusEnum::ACTIVE)
            ->where('is_deleted', 0)
            ->count();
        return $response;
    }

    /**
     * @throws NotFoundUserException
     */
    private function getUserByEmailOrThrowNotFoundException(string $email): User
    {
        $user = $this->userRepository->of(email: $email, status: UserStatusEnum::PENDING);
        if (!$user) {
            throw new NotFoundUserException();
        }
        return $user;
    }
}
