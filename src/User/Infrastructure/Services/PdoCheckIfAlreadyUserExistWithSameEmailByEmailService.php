<?php

namespace App\User\Infrastructure\Services;

use App\Shared\Domain\VO\StringVO;
use App\User\Domain\Service\CheckIfAlreadyUserExistWithSameEmailByEmailService;
use Illuminate\Support\Facades\DB;
use PDO;

class PdoCheckIfAlreadyUserExistWithSameEmailByEmailService implements CheckIfAlreadyUserExistWithSameEmailByEmailService
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    public function execute(StringVO $email): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM users
            WHERE LOWER(email) = LOWER(:email) AND
                  is_deleted=false AND
                  status = 'active'
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_OBJ);
        $stmt->execute([
            'email' => $email->value(),
        ]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }
}
