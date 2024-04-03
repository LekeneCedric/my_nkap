<?php

namespace App\User\Application\Command;

use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use App\User\Domain\Exceptions\AlreadyUserExistWithSameEmailException;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\CheckIfAlreadyUserExistWithSameEmailByEmailService;
use App\User\Domain\User;

class RegisterUserHandler
{
    public function __construct(
        private UserRepository $repository,
        private CheckIfAlreadyUserExistWithSameEmailByEmailService $checkIfAlreadyUserExistWithSameEmailByEmailService,
    )
    {
    }

    /**
     * @param RegisterUserCommand $command
     * @return RegisterUserResponse
     * @throws AlreadyUserExistWithSameEmailException
     * @throws ErrorOnSaveUserException
     */
    public function handle(RegisterUserCommand $command): RegisterUserResponse
    {
        $this->checkIfAlreadyUserExistWithSameEmailOrThrowException($command);

        $response = new RegisterUserResponse();

        $user = User::create(
            name: new StringVO($command->username),
            email: new StringVO($command->email),
            password: new StringVO($command->password),
            professionId: new Id($command->professionId)
        );

        $this->repository->save($user);

        $response->isCreated = true;
        $response->message = 'Utilisateur créer avec succès !';
        $response->userId = $user->id()->value();

        return $response;
    }

    /**
     * @param RegisterUserCommand $command
     * @return void
     * @throws AlreadyUserExistWithSameEmailException
     */
    private function checkIfAlreadyUserExistWithSameEmailOrThrowException(RegisterUserCommand $command): void
    {
        $userAlreadyExistWithSameEmail = $this->checkIfAlreadyUserExistWithSameEmailByEmailService->execute(new StringVO($command->email));
        if ($userAlreadyExistWithSameEmail) {
            throw new AlreadyUserExistWithSameEmailException('Un compte à déjà été créer avec cette adresse email !');
        }
    }
}
