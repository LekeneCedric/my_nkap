<?php

namespace App\Account\Infrastructure\Repository;

use App\Account\Domain\Account;
use App\Account\Domain\Enums\AccountEventStateEnum;
use App\Account\Domain\Repository\AccountRepository;
use App\Shared\VO\Id;
use Exception;
use Illuminate\Support\Facades\DB;

class PdoAccountRepository implements AccountRepository
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    /**
     * @throws Exception
     */
    public function save(Account $account): void
    {
        if ($account->eventState() === AccountEventStateEnum::onCreate) {
            $this->createAccount($account);
        }
        if ($account->eventState() === AccountEventStateEnum::onUpdate) {
            $this->updateAccount($account);
        }
    }

    /**
     * @throws Exception
     */
    private function createAccount(Account $account): void
    {
        $sql = $this->getCreateAccountSql();
        $stmt = $this->pdo->prepare($sql);
        $data = $account->toArray();
        $stmt->execute($data);
    }

    /**
     * @throws Exception
     */
    private function updateAccount(Account $account): void
    {
        $sql = $this->getUpdateAccountSql();
        $stmt = $this->pdo->prepare($sql);
        $data = $account->toArray();
        $stmt->execute($data);
    }

    private function getCreateAccountSql(): string
    {
        return "
            INSERT INTO
            accounts
            (
                uuid,
                name,
                type,
                icon,
                color,
                balance,
                is_include_in_total_balance,
                is_deleted,
                created_at
             )
             VALUE
             (
                 :uuid,
                 :name,
                 :type,
                 :icon,
                 :color,
                 :balance,
                 :is_include_in_total_balance,
                 :is_deleted,
                 :created_at
             )
        ";
    }

    private function getUpdateAccountSql(): string
    {
        return "
        UPDATE accounts
        SET name=:name,
            type=:type,
            icon=:icon,
            color=:color,
            balance=:balance,
            is_deleted=:is_deleted,
            is_include_in_total_balance=:is_include_in_total_balance,
            updated_at=:updated_at
        WHERE uuid=:uuid
        ";
    }

    public function byId(Id $accountId): ?Account
    {
        return null;
    }
}
