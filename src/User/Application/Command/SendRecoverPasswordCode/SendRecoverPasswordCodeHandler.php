<?php

namespace App\User\Application\Command\SendRecoverPasswordCode;

use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\User;

class SendRecoverPasswordCodeHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {
    }

    /**
     * @param SendRecoverPasswordCodeCommand $command
     * @return SendRecoverPasswordCodeResponse
     * @throws ErrorOnSaveUserException
     * @throws NotFoundUserException
     */
    public function handle(SendRecoverPasswordCodeCommand $command): SendRecoverPasswordCodeResponse
    {
        $response = new SendRecoverPasswordCodeResponse();
        $user = $this->getUserByEmailOrThrowNotFoundException($command->email);
        $user->assignVerificationCode();
        $this->userRepository->update($user);

        $response->code = $user->verificationCode();
        $response->email = $user->email()->value();
        return $response;
    }

    /**
     * @param string $email
     * @return User
     * @throws NotFoundUserException
     */
    private function getUserByEmailOrThrowNotFoundException(string $email): User
    {
        $user = $this->userRepository->ofEmail($email);
        if (!$user) {
            throw new NotFoundUserException();
        }
        return $user;
    }
}
