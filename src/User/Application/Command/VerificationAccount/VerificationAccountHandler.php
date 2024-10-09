<?php

namespace App\User\Application\Command\VerificationAccount;

use App\User\Domain\Enums\UserMessagesEnum;
use App\User\Domain\Enums\UserStatusEnum;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Exceptions\UnknownVerificationCodeException;
use App\User\Domain\Exceptions\VerificationCodeNotMatchException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\User;

class VerificationAccountHandler
{
    public function __construct(
        private UserRepository $userRepository,
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
        $response->accountVerified = true;
        $response->message = UserMessagesEnum::ACCOUNT_VERIFIED;
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
