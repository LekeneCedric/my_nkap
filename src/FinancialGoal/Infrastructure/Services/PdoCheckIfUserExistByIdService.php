<?php

namespace App\FinancialGoal\Infrastructure\Services;

use App\FinancialGoal\Domain\Service\CheckIfUserExistByIdService;
use App\Shared\VO\Id;
use Illuminate\Support\Facades\DB;

class PdoCheckIfUserExistByIdService implements CheckIfUserExistByIdService
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    public function execute(Id $userId): bool
    {
      $st = $this->pdo->prepare("
        SELECT COUNT(*)
        FROM users
        WHERE uuid=:userId AND
              is_deleted=false AND
              is_active=true
      ");
      $st->setFetchMode(\PDO::FETCH_OBJ);
      $st->execute(['userId' => $userId->value()]);
      $count = $st->fetchColumn();
      return $count > 0;
    }
}
