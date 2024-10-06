<?php

namespace App\User\Infrastructure\Repository;

use App\User\Domain\Enums\UserStatusEnum;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\User;
use App\User\Infrastructure\Models\Profession;
use Exception;
use Illuminate\Support\Facades\DB;
use PDO;
use App\User\Infrastructure\Models\User as UserModel;

class PdoUserRepository implements UserRepository
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    /**
     * @param User $user
     * @return void
     * @throws ErrorOnSaveUserException
     * @throws Exception
     */
    public function update(User $user): void
    {
        $data = array_merge($user->toArray(), $this->getForeignIds($user));
        try {
            UserModel::whereUuid($user->id()->value())->update($data);
        } catch (\PDOException | Exception $e) {
            throw new ErrorOnSaveUserException($e->getMessage());
        }
    }

    /**
     * @param User $user
     * @return void
     * @throws ErrorOnSaveUserException
     * @throws Exception
     */
    public function create(User $user): void
    {
        $data = array_merge($user->toArray(), $this->getForeignIds($user));
        try {
            UserModel::create($data);
        } catch (\PDOException | Exception $e) {
            throw new ErrorOnSaveUserException($e->getMessage());
        }
    }

    /**
     * @param User $user
     * @return array
     */
    private function getForeignIds(User $user): array
    {
        return [
          'profession_id' => Profession::where('uuid', $user->professionId()->value())->first()?->id,
        ];
    }

    public function userId(): string
    {
        return auth()->user()->uuid;
    }

    public function ofEmail(string $email): ?User
    {
        return UserModel::where('email', $email)
            ->where('status', UserStatusEnum::ACTIVE->value)
            ->first()?->toDomain();
    }

    public function of(string $email, UserStatusEnum $status): ?User
    {
        return UserModel::where('email', $email)
            ->where('status', $status->value)
            ->first()?->toDomain();
    }
}
