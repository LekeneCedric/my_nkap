<?php

namespace App\Operation\Infrastructure\Repository;

use App\Account\Infrastructure\Model\Account;
use App\category\Infrastructure\Models\Category;
use App\Operation\Domain\Operation;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\OperationEventStateEnum;
use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use Exception;
use Illuminate\Support\Facades\DB;
use PDOException;

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
        $sql = "
            SELECT
                id as id,
                uuid as Id,
                balance as Balance,
                total_expenses as totalExpenses,
                total_incomes as totalIncomes
            FROM accounts
            WHERE uuid=:accountId
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute([
            'accountId' => $operationAccountId->value(),
        ]);

        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }
        $account = operationAccount::create(
            balance: new AmountVO($result['Balance']),
            totalIncomes: new AmountVO($result['totalIncomes']),
            totalExpenses: new AmountVO($result['totalExpenses']),
            accountId: new Id($result['Id']),
        );
        $accountOperations = $this->getAccountOperations($result['id']);
        $account->loadOperations($accountOperations);
        return $account;
    }

    /**
     * @param operationAccount $operationAccount
     * @return void
     */
    public function saveOperation(operationAccount $operationAccount): void
    {
        try {
            $this->updateOperationAccount($operationAccount);
            $this->saveCurrentOperation($operationAccount);
        } catch (PDOException|Exception $e) {
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
        $currentOperation = $account->currentOperation();
        $data = array_merge($currentOperation->toArray(), $this->getForeignIds(
            accountId: $account->id()->value(),
            categoryId: $currentOperation->categoryId()->value(),
        ));
        $sql = "
            INSERT INTO operations
            (
             uuid,
             account_id,
             type,
             amount,
             category_id,
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
                :category_id,
                :details,
                :date,
                :is_deleted,
                :created_at
            )
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * @param operationAccount $account
     * @return void
     * @throws Exception
     */
    private function updateOperation(operationAccount $account): void
    {
        $currentOperation = $account->currentOperation();
        $data = array_merge($currentOperation->toArray(), $this->getForeignIds(
            accountId: $account->id()->value(),
            categoryId: $currentOperation->categoryId()->value(),
        ));
        $sql = "
            UPDATE operations
            SET
             account_id=:account_id,
             type=:type,
             amount=:amount,
             category_id=:category_id,
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
     * @param string $categoryId
     * @return array
     */
    private function getForeignIds(string $accountId, string $categoryId): array
    {
        return [
            'account_id' => Account::whereUuid($accountId)->whereIsDeleted(false)->first()->id,
            'category_id' => Category::whereUuid($categoryId)->first()->id,
        ];
    }

    /**
     * @param int $account_id
     * @return Operation[]
     */
    private function getAccountOperations(int $account_id): array
    {
        $sql = "
            SELECT
                o.uuid AS Id,
                o.amount,
                o.type,
                c.uuid AS categoryId,
                o.details,
                o.date
            FROM operations o
            INNER JOIN categories c ON o.category_id = c.id
            WHERE account_id=:account_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute([
            'account_id' => $account_id,
        ]);

        $results = $stmt->fetchAll();
        if (!$results) return [];

        $operations = [];
        foreach ($results as $result) {
            $operations[$result['Id']] = Operation::create(
                amount: new AmountVO($result['amount']),
                type: OperationTypeEnum::from($result['type']),
                categoryId: new Id($result['categoryId']),
                detail: new StringVO($result['details']),
                date: new DateVO($result['date']),
                id: new Id($result['Id'])
            );
        }

        return $operations;
    }
}
