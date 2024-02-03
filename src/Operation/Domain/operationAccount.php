<?php

namespace App\Operation\Domain;

use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Exception;

class operationAccount
{
    /**
     * @var Operation[]
     */
    private array $operations = [];
    private Operation $currentOperation;
    private ?DateVO $updatedAt = null;

    public function __construct(
        private readonly Id $accountId,
        private AmountVO    $balance,
        private AmountVO    $totalIncomes,
        private AmountVO    $totalExpenses
    )
    {
    }

    /**
     * @param AmountVO $amount
     * @param OperationTypeEnum $type
     * @param StringVO $category
     * @param StringVO $detail
     * @param DateVO $date
     * @return void
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function makeOperation(
        AmountVO          $amount,
        OperationTypeEnum $type,
        StringVO          $category,
        StringVO          $detail,
        DateVO            $date
    ): void
    {
        $this->checkIfOperationAmoutGreaterThantAccountBalancecapacity(
            operationType: $type,
            amount: $amount,
        );
        $operation = Operation::create(
            amount: $amount,
            type: $type,
            category: $category,
            detail: $detail,
            date: $date
        );
        $this->operations[] = $operation;
        $this->currentOperation = $operation;
        $this->applyNewOperationSideEffects(
            type: $operation->type(),
            amount: $operation->amount()
        );
    }

    /**
     * @param OperationTypeEnum $operationType
     * @param AmountVO $amount
     * @return void
     * @throws OperationGreaterThanAccountBalanceException
     */
    private function checkIfOperationAmoutGreaterThantAccountBalancecapacity(
        OperationTypeEnum $operationType,
        AmountVO          $amount
    ): void
    {
        if ($operationType === OperationTypeEnum::EXPENSE) {
            if ($amount->value() > $this->balance->value()) {
                throw new OperationGreaterThanAccountBalanceException("
                    Le solde du compte est insuffisant pour cette transaction !
                ");
            }
        }
    }

    /**
     * @param Id $accountId
     * @param AmountVO $balance
     * @param AmountVO $totalIncomes
     * @param AmountVO $totalExpenses
     * @return operationAccount
     */
    public static function create(
        Id       $accountId,
        AmountVO $balance,
        AmountVO $totalIncomes,
        AmountVO $totalExpenses,
    ): operationAccount
    {
        return new self(
            accountId: $accountId,
            balance: $balance,
            totalIncomes: $totalIncomes,
            totalExpenses: $totalExpenses,
        );
    }

    private function applyNewOperationSideEffects(
        OperationTypeEnum $type,
        AmountVO          $amount
    ): void
    {
        $balanceValue = $this->balance->value();
        $totalIncomesValue = $this->totalIncomes->value();
        $totalExpensesValue = $this->totalExpenses->value();
        $amountValue = $amount->value();
        if ($type === OperationTypeEnum::INCOME) {
            $this->balance = new AmountVO($balanceValue + $amountValue);
            $this->totalIncomes = new AmountVO($totalIncomesValue + $amountValue);
        } else {
            $this->balance = new AmountVO($balanceValue - $amountValue);
            $this->totalExpenses = new AmountVO($totalExpensesValue + $amountValue);
        }
        $this->updatedAt = new DateVO();
    }

    /**
     * @return AmountVO
     */
    public function totalExpenses(): AmountVO
    {
        return $this->totalExpenses;
    }

    /**
     * @return AmountVO
     */
    public function totalIncomes(): AmountVO
    {
        return $this->totalIncomes;
    }

    public function currentOperation(): Operation
    {
        return $this->currentOperation;
    }

    /**
     * @return AmountVO
     */
    public function balance(): AmountVO
    {
        return $this->balance;
    }

    /**
     * @param Id $operationId
     * @return void
     * @throws NotFoundOperationException
     */
    public function deleteOperation(Id $operationId): void
    {
        $operation = $this->deleteAndRetrieveOperation($operationId);
        if (!$operation) {
            throw new NotFoundOperationException("L'operation sélectionné n'existe plus dans le système !");
        }
        $this->currentOperation = $operation;
        $this->applyDeleteOperationSideEffects();
    }

    private function deleteAndRetrieveOperation(Id $operationId): ?Operation
    {
        for ($i = 0; $i < count($this->operations); $i++) {
            if ($this->operations[$i]->id()->value() === $operationId->value()) {
                $this->operations[$i]->delete();
                return $this->operations[$i];
            }
        }
        return null;
    }

    public function id(): Id
    {
        return $this->accountId;
    }

    /**
     * @return void
     */
    private function applyDeleteOperationSideEffects(): void
    {
        $newBalanceValue = $this->balance->value();
        $totalExpensesValue = $this->totalExpenses->value();
        $totalIncomesValue = $this->totalIncomes->value();
        $currentOperationAmountValue = $this->currentOperation->amount()->value();
        if ($this->currentOperation->type() === OperationTypeEnum::EXPENSE) {
            $newBalanceValue += $currentOperationAmountValue;
            $newTotalExpenses = $totalExpensesValue - $currentOperationAmountValue;
            $this->totalExpenses = new AmountVO($newTotalExpenses);
        } else {
            $newBalanceValue -= $currentOperationAmountValue;
            $newtotalIncomes = $totalIncomesValue - $currentOperationAmountValue;
            $this->totalIncomes = new AmountVO($newtotalIncomes);
        }
        $this->balance = new AmountVO($newBalanceValue);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->accountId->value(),
            'balance' => $this->balance->value(),
            'total_incomes' => $this->totalIncomes->value(),
            'total_expenses' => $this->totalExpenses->value(),
            'updated_at' => $this->updatedAt->formatYMDHIS(),
        ];
    }
}
