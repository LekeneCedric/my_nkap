<?php

namespace App\FinancialGoal\Infrastructure\Repository;

use App\Account\Infrastructure\Models\Account;
use App\FinancialGoal\Domain\Enum\FinancialGoalEventStateEnum;
use App\FinancialGoal\Domain\FinancialGoal;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Exception;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;

class PdoFinancialGoalRepository implements FinancialGoalRepository
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    public function save(FinancialGoal $financialGoal): void
    {
        $this->pdo->beginTransaction();
        try {
            $eventState = $financialGoal->eventState();
            if ($eventState === FinancialGoalEventStateEnum::onCreate) {
                $this->createFinancialGoal($financialGoal);
            }
            if ($eventState === FinancialGoalEventStateEnum::onDelete) {
                $this->deleteFinancialGoal($financialGoal);
            }
            if ($eventState === FinancialGoalEventStateEnum::onUpdate) {
                $this->updateFinancialGoal($financialGoal);
            }
            $this->pdo->commit();
        } catch (PDOException|Exception) {
            $this->pdo->rollBack();
        }
    }

    public function byId(Id $financialGoalId): ?FinancialGoal
    {
        $sql = "
            SELECT
                fg.uuid AS Id,
                ac.uuid AS accountId,
                start_date AS startDate,
                end_date AS endDate,
                desired_amount AS desiredAmount,
                details AS details,
                current_amount AS currentAmount,
                is_complete AS isComplete
            FROM financial_goals AS fg
            INNER JOIN accounts AS ac ON fg.account_id=ac.id
            WHERE fg.uuid=:uuid AND
                  fg.is_deleted=false AND
                  ac.is_deleted=false
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_OBJ);
        $stmt->execute([
            'uuid' => $financialGoalId->value(),
        ]);

        $result = $stmt->fetch();

        if (!$result) return null;

        return $this->toFinancialGoalDomain($result);
    }

    private function toFinancialGoalDomain(mixed $result): FinancialGoal
    {
        return FinancialGoal::create(
            accountId: new Id($result->accountId),
            startDate: new DateVO($result->startDate),
            enDate: new DateVO($result->endDate),
            desiredAmount: new AmountVO($result->desiredAmount),
            details: new StringVO($result->details),
            financialGoalId: new Id($result->Id),
            currentAmount: new AmountVO($result->currentAmount),
            isComplete: $result->isComplete
        );
    }

    /**
     * @throws Exception
     */
    private function updateFinancialGoal(FinancialGoal $financialGoal): void
    {
        $data = $financialGoal->toArray();
        $sql = "
            UPDATE financial_goals
            SET end_date=:end_date,
                details=:details,
                desired_amount=:desired_amount,
                updated_at=:updated_at
            WHERE uuid=:uuid
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * @param FinancialGoal $financialGoal
     * @return void
     * @throws Exception
     */
    private function createFinancialGoal(FinancialGoal $financialGoal): void
    {
        $data = array_merge($financialGoal->toArray(), $this->getForeignIds($financialGoal->accountId()->value()));
        $sql = $this->createFinancialGoalSql();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * @param FinancialGoal $financialGoal
     * @return void
     * @throws Exception
     */
    private function deleteFinancialGoal(FinancialGoal $financialGoal): void
    {
        $data = [
          'uuid' => $financialGoal->id()->value(),
          'is_deleted' => true,
          'deleted_at' => $financialGoal->deletedAt()->formatYMDHIS(),
        ];
        $sql = "
            UPDATE financial_goals
            SET is_deleted=:is_deleted,
                deleted_at=:deleted_at
            WHERE uuid=:uuid
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    private function createFinancialGoalSql(): string
    {
        return "
            INSERT INTO financial_goals
                (
                    uuid,
                    account_id,
                    start_date,
                    end_date,
                    details,
                    current_amount,
                    desired_amount,
                    created_at
                )
            VALUES (
                    :uuid,
                    :account_id,
                    :start_date,
                    :end_date,
                    :details,
                    :current_amount,
                    :desired_amount,
                    :created_at
                )
        ";
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
