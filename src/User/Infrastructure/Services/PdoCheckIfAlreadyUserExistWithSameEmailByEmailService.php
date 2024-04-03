<?php

namespace App\User\Infrastructure\Services;

use App\Shared\VO\StringVO;
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
            WHERE email=:email AND
                  is_deleted=false AND
                  is_active=1
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
