<?php

namespace App\User\Infrastructure\Repository;

use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\User;
use App\User\Infrastructure\Models\Profession;
use Exception;
use Illuminate\Support\Facades\DB;
use PDO;

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
    public function save(User $user): void
    {
        $data = array_merge($user->toArray(), $this->getForeignIds($user));
        try {
            $sql = "
                INSERT INTO users
                (uuid,  profession_id, name, email, password, created_at)
                VALUE
                (:uuid, :profession_id, :name, :email, :password, :created_at)
            ";
            $st = $this->pdo->prepare($sql);
            $st->execute($data);
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
}
