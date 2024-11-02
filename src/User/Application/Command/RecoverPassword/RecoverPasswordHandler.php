<?php

namespace App\User\Application\Command\RecoverPassword;

use App\User\Domain\Enums\UserMessagesEnum;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Exceptions\UnknownVerificationCodeException;
use App\User\Domain\Exceptions\VerificationCodeNotMatchException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\User;

class RecoverPasswordHandler
{
    public function __construct(
        private UserRepository $repository,
    )
    {
    }

    /**
     * @param RecoverPasswordCommand $command
     * @return RecoverPasswordResponse
     * @throws NotFoundUserException
     * @throws UnknownVerificationCodeException
     * @throws VerificationCodeNotMatchException
     * @throws ErrorOnSaveUserException
     */
    public function handle(RecoverPasswordCommand $command): RecoverPasswordResponse
    {
        $response = new RecoverPasswordResponse();

        $user = $this->getUserOrThrowNotFoundException($command->email);

        $user->checkIfCodeIsCorrectOrThrowException($command->code);
        $user->resetPassword($command->password);
        $this->repository->update($user);

        $response->message = UserMessagesEnum::PASSWORD_RESET;
        $response->passwordReset = true;
        return $response;
    }

    /**
     * @param string $email
     * @return User
     * @throws NotFoundUserException
     */
    private function getUserOrThrowNotFoundException(string $email): User
    {
        $user = $this->repository->ofEmail($email);
        if (!$user) {
            throw new NotFoundUserException();
        }
        return $user;
    }
}
