<?php

namespace App\Operation\Infrastructure;

use App\Account\Infrastructure\Models\Account;
use App\Operation\Domain\Operation;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\OperationEventStateEnum;
use App\Shared\VO\Id;
use Exception;
use Illuminate\Support\Facades\DB;

class PdoOperationAccountRepository implements OperationAccountRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    /**
     * @param Id $operationAccountId
     * @return operationAccount|null
     */
    public function byId(Id $operationAccountId): ?operationAccount
    {
        return null;
    }

    /**
     * @param operationAccount $operationAccount
     * @return void
     */
    public function saveOperation(operationAccount $operationAccount): void
    {
        try {
            $this->pdo->beginTransaction();
            $this->updateOperationAccount($operationAccount);
            $this->saveCurrentOperation($operationAccount);
            $this->pdo->commit();
        } catch (\PDOException|Exception) {
            $this->pdo->rollBack();
        }
    }

    /**
     * @param operationAccount $operationAccount
     * @return void
     * @throws Exception
     */
    private function updateOperationAccount(operationAccount $operationAccount): void
    {
        $data = $operationAccount->toArray();
        $sql = "
            UPDATE accounts
            SET balance=:balance,
                total_incomes=:total_incomes,
                total_expenses=:total_expenses,
                updated_at=:updated_at
            WHERE uuid=:uuid
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * @param operationAccount $account
     * @return void
     * @throws Exception
     */
    private function saveCurrentOperation(operationAccount $account): void
    {
        if ($account->currentOperation()->eventState() === OperationEventStateEnum::onCreate) {
            $this->createOperation($account);
        }
        if ($account->currentOperation()->eventState() === OperationEventStateEnum::onUpdate) {
            $this->updateOperation($account);
        }
        if ($account->currentOperation()->eventState() === OperationEventStateEnum::onDelete) {
            $this->deleteOperation($account->currentOperation());
        }

    }

    /**
     * @param operationAccount $account
     * @return void
     * @throws Exception
     */
    private function createOperation(operationAccount $account): void
    {
        $data = array_merge($account->currentOperation()->toArray(), $this->getForeignIds($account->id()->value()));
        $sql = "
            INSERT INTO operations
            (
             uuid,
             account_id,
             type,
             amount,
             category,
             details,
             date,
             is_deleted,
             created_at
             )
            VALUE (
                :uuid,
                :account_id,
                :type,
                :amount,
                :category,
                :details,
                :date,
                :is_deleted,
                :created_at
            )
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    private function updateOperation(operationAccount $account): void
    {
        $data = array_merge($account->currentOperation()->toArray(), $this->getForeignIds($account->id()->value()));
        $sql = "
            UPDATE operations
            SET
             account_id=:account_id,
             type=:type,
             amount=:amount,
             category=:category,
             details=:details,
             date=:date,
             updated_at=:updated_at,
             is_deleted=:is_deleted
            WHERE uuid=:uuid
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * @param Operation $operation
     * @return void
     * @throws Exception
     */
    private function deleteOperation(Operation $operation): void
    {
        $data = [
            'uuid' => $operation->id()->value(),
            'deleted_at' => $operation->deletedAt()->formatYMDHIS(),
        ];
        $sql = "
            UPDATE operations
            SET is_deleted = 1,
                deleted_at = :deleted_at
            WHERE uuid=:uuid
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * @param string $accountId
     * @return array
     */
    private function getForeignIds(string $accountId): array
    {
        return [
            'account_id' => Account::whereUuid($accountId)->whereIsDeleted(false)->first()->id,
        ];
    }
}
