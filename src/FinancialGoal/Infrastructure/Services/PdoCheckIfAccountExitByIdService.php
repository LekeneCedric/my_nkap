<?php

namespace App\FinancialGoal\Infrastructure\Services;

use App\FinancialGoal\Domain\Service\CheckIfAccountExitByIdService;
use App\Shared\Domain\VO\Id;
use Illuminate\Support\Facades\DB;
use PDO;

class PdoCheckIfAccountExitByIdService implements CheckIfAccountExitByIdService
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    public function execute(Id $accountId): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM accounts
            WHERE uuid=:accountId AND
                  is_deleted = false
        ";

        $st = $this->pdo->prepare($sql);
        $st->setFetchMode(\PDO::FETCH_OBJ);
        $st->execute([
            'accountId' => $accountId->value()
        ]);
        $count = $st->fetchColumn();

        return $count > 0;
    }
}
