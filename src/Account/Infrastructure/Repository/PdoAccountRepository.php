<?php

namespace App\Account\Infrastructure\Repository;

use App\Account\Domain\Account;
use App\Account\Domain\Enums\AccountEventStateEnum;
use App\Account\Domain\Repository\AccountRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Exception;
use Illuminate\Support\Facades\DB;
use Psy\Util\Str;

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
        if ($account->eventState() === AccountEventStateEnum::onDelete) {
            $this->deleteAccount($account);
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

    /**
     * @throws Exception
     */
    private function deleteAccount(Account $account): void
    {
        $data = [
            'uuid' => $account->id()->value(),
            'deleted_at' => $account->deletedAt()->formatYMDHIS(),
        ];

        $sql = "
            UPDATE accounts
            SET is_deleted = 1,
                deleted_at = :deleted_at
            WHERE uuid=:uuid
        ";
        $stmt = $this->pdo->prepare($sql);
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
        $sql = "
            SELECT
                uuid as Id,
                user_id as userId,
                name,
                type,
                icon,
                color,
                balance,
                is_include_in_total_balance as isIncludeInTotalBalance
            FROM accounts
            WHERE uuid = :uuid AND
                  is_deleted = false
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute([
            'uuid' => $accountId->value(),
        ]);
        $result = $stmt->fetch();
        if (!$result) {
            return null;
        }
        return $this->toAccountDomain($result);
    }

    private function toAccountDomain(array $result): Account
    {
        return Account::create(
            userId: new Id($result['userId']),
            name: new StringVO($result['name']),
            type: new StringVO($result['type']),
            icon: new StringVO($result['icon']),
            color: new StringVO($result['color']),
            balance: new AmountVO($result['balance']),
            isIncludeInTotalBalance: $result['isIncludeInTotalBalance'],
            accountId: new Id(($result['Id']))
        );
    }
}
