<?php

namespace App\Operation\Domain;

use App\Operation\Application\Command\DeleteOperation\DeleteOperationCommand;
use App\Operation\Application\Command\MakeOperation\MakeOperationCommand;
use App\Operation\Domain\Events\OperationDeleted;
use App\Operation\Domain\Events\OperationSaved;
use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\Command\Command;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use Exception;

class operationAccount extends AggregateRoot
{
    /**
     * @var Operation[]
     */
    private array $operations = [];
    private Operation $currentOperation;
    private ?DateVO $updatedAt = null;
    private ?DateVO $createdAt = null;

    public function __construct(
        private readonly Id $accountId,
        private AmountVO    $balance,
        private AmountVO    $totalIncomes,
        private AmountVO    $totalExpenses
    )
    {
        parent::__construct();
    }

    /**
     * @param Id $operationId
     * @param AmountVO $amount
     * @param AmountVO $previousAmount
     * @param OperationTypeEnum $type
     * @param Id $categoryId
     * @param StringVO $detail
     * @param DateVO $date
     * @return void
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function updateOperation(
        Id                $operationId,
        AmountVO          $amount,
        AmountVO          $previousAmount,
        OperationTypeEnum $type,
        Id                $categoryId,
        StringVO          $detail,
        DateVO            $date
    ): void
    {
        $this->checkIfOperationAmountGreaterThantAccountBalanceCapacity(
            operationType: $type,
            amount: $amount,
            previousAmount: $previousAmount
        );
        $operation = $this->operations[$operationId->value()];
        $previousOperationType = $operation->type();
        $previousOperationAmount = $operation->amount();
        $operation->update(
            amount: $amount,
            type: $type,
            categoryId: $categoryId,
            detail: $detail,
            date: $date
        );
        $this->currentOperation = $operation;
        $this->applyUpdateOperationSideEffects(
            previousType: $previousOperationType,
            previousAmount: $previousOperationAmount,
            newType: $operation->type(),
            newAmount: $operation->amount()
        );
    }

    /**
     * @param OperationTypeEnum $operationType
     * @param AmountVO $amount
     * @param AmountVO $previousAmount
     * @return void
     * @throws OperationGreaterThanAccountBalanceException
     */
    private function checkIfOperationAmountGreaterThantAccountBalanceCapacity(
        OperationTypeEnum $operationType,
        AmountVO          $amount,
        AmountVO          $previousAmount,
    ): void
    {
        if ($operationType === OperationTypeEnum::EXPENSE) {
            $newBalanceAmount = $this->balance->value() + $previousAmount->value();
            if ($amount->value() > $newBalanceAmount) {
                throw new OperationGreaterThanAccountBalanceException();
            }
        }
    }

    /**
     * @param OperationTypeEnum $previousType
     * @param AmountVO $previousAmount
     * @param OperationTypeEnum $newType
     * @param AmountVO $newAmount
     * @return void
     */
    private function applyUpdateOperationSideEffects(
        OperationTypeEnum $previousType,
        AmountVO          $previousAmount,
        OperationTypeEnum $newType,
        AmountVO          $newAmount
    ): void
    {

        $this->applyDeleteOperationSideEffects(
            type: $previousType,
            amount: $previousAmount,
        );

        $this->applyNewOperationSideEffects(
            type: $newType,
            amount: $newAmount,
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
     * @param AmountVO $amount
     * @param OperationTypeEnum $type
     * @param Id $categoryId
     * @param StringVO $detail
     * @param DateVO $date
     * @return void
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function makeOperation(
        AmountVO          $amount,
        OperationTypeEnum $type,
        Id                $categoryId,
        StringVO          $detail,
        DateVO            $date
    ): void
    {
        $this->checkIfOperationAmountGreaterThantAccountBalanceCapacity(
            operationType: $type,
            amount: $amount,
            previousAmount: new AmountVO(0),
        );
        $operation = Operation::create(
            amount: $amount,
            type: $type,
            categoryId: $categoryId,
            detail: $detail,
            date: $date
        );
        $this->operations[$operation->id()->value()] = $operation;
        $this->currentOperation = $operation;
        $this->applyNewOperationSideEffects(
            type: $operation->type(),
            amount: $operation->amount()
        );
    }

    /**
     * @param AmountVO $balance
     * @param AmountVO $totalIncomes
     * @param AmountVO $totalExpenses
     * @param Id|null $accountId
     * @return operationAccount
     */
    public static function create(
        AmountVO $balance,
        AmountVO $totalIncomes,
        AmountVO $totalExpenses,
        ?Id      $accountId = null,
    ): operationAccount
    {
        $self = new self(
            accountId: $accountId ?? new Id(),
            balance: $balance,
            totalIncomes: $totalIncomes,
            totalExpenses: $totalExpenses,
        );
        if ($accountId) {
            $self->updatedAt = new DateVO();
        } else {
            $self->createdAt = new DateVO();
        }
        return $self;
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
            throw new NotFoundOperationException();
        }
        $this->currentOperation = $operation;
        $this->applyDeleteOperationSideEffects();
    }

    private function deleteAndRetrieveOperation(Id $operationId): ?Operation
    {
        foreach ($this->operations as &$operation) {
            if ($operation->id()->value() === $operationId->value()) {
                $operation->delete();
                return $operation;
            }
        }
        return null;
    }

    public function id(): Id
    {
        return $this->accountId;
    }

    /**
     * @param OperationTypeEnum|null $type
     * @param AmountVO|null $amount
     * @return void
     */
    private function applyDeleteOperationSideEffects(
        ?OperationTypeEnum $type = null,
        ?AmountVO          $amount = null,
    ): void
    {
        $newBalanceValue = $this->balance->value();
        $totalExpensesValue = $this->totalExpenses->value();
        $totalIncomesValue = $this->totalIncomes->value();
        $currentOperationAmountValue = $amount ? $amount->value() : $this->currentOperation->amount()->value();
        $currentOperationType = $type ?: $this->currentOperation->type();
        if ($currentOperationType === OperationTypeEnum::EXPENSE) {
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
     * @param array $accountOperations
     * @return void
     */
    public function loadOperations(array $accountOperations): void
    {
        $this->operations = $accountOperations;
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

    public function operation(string $operationId): Operation
    {
        return $this->operations[$operationId];
    }

    public function publishOperationSaved(MakeOperationCommand|Command $command): void
    {
        $this->domainEventPublisher->publish(
            new OperationSaved(
                accountId: $command->accountId,
                previousAmount: $command->previousAmount ?? 0,
                newAmount: $command->amount,
                operationDate: $command->date,
                type: $command->type,
                userId: $command->userId,
                year: $command->year,
                month: $command->month,
                categoryId: $command->categoryId,
                monthlyStatsComposedId: $command->monthlyStatsComposedId,
                monthlyStatsByCategoryComposedId: $command->monthlyStatsByCategoryComposedId,
            )
        );
    }

    public function publishOperationDeleted(DeleteOperationCommand|Command $command): void
    {
        $this->domainEventPublisher->publish(
            new OperationDeleted(
                accountId: $command->accountId,
                previousAmount: $command->previousAmount,
                newAmount: $command->newAmount,
                date: $command->date,
                type: $command->type,
                isDeleted: $command->isDeleted,
                monthlyStatisticsComposedId: $command->monthlyStatisticsComposedId,
                monthlyStatisticsByCategoryComposedId: $command->monthlyStatisticsByCategoryComposedId,
                userId: $command->userId,
                year: $command->year,
                month: $command->month,
                categoryId: $command->categoryId,
            )
        );
    }
}
