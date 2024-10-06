<?php

namespace App\User\Application\Command\Register;

use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\User\Domain\Exceptions\AlreadyUserExistWithSameEmailException;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\CheckIfAlreadyUserExistWithSameEmailByEmailService;
use App\User\Domain\User;
use Illuminate\Support\Facades\DB;

class RegisterUserHandler
{
    private \PDO $pdo;
    public function __construct(
        private UserRepository $repository,
        private CheckIfAlreadyUserExistWithSameEmailByEmailService $checkIfAlreadyUserExistWithSameEmailByEmailService,
    )
    {
        $this->pdo = DB::getPdo();
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
            password: new StringVO(bcrypt($command->password)),
            professionId: new Id($command->professionId)
        );

        $this->repository->create($user);

        $userId = $user->id()->value();
        $response->isCreated = true;
        $response->message = 'Utilisateur créer avec succès !';
        $response->userId = $userId;
        $response->user = $this->getUserById($userId);
        $response->code = $user->verificationCode();
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

    private function getUserById(string $userId)
    {
        $sql = "
            SELECT
                u.uuid as userId,
                u.name as username,
                u.email as email,
                pr.name as profession
            FROM users AS u
            INNER JOIN professions AS pr ON pr.id = u.profession_id
            WHERE u.uuid = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch();
    }
}
