<?php

namespace App\Operation\Tests\Features;

use App\Account\Infrastructure\Models\Account;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use App\Operation\Infrastructure\Repository\PdoOperationAccountRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Tests\TestCase;

class OperationAccountRepositoryTest extends TestCase
{
    private OperationAccountRepository $repository;

    /**
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function test_can_save_operation()
    {
        $initSUT = $this->buildSUT(operationAmount: 20000);

        $operationId = $initSUT['operationId'];
        $operationAccount = $initSUT['account'];

        $this->repository->saveOperation($operationAccount);

        $updatedAccount = Account::whereUuid($operationAccount->id()->value())
            ->whereIsDeleted(false)
            ->first();
        $createdOperation = Operation::whereUuid($operationId)
            ->whereIsDeleted(false)
            ->first();

        $this->assertNotNull($createdOperation);
        $this->assertEquals(20000, $createdOperation->amount);
        $this->assertEquals(OperationTypeEnum::INCOME->value, $createdOperation->type);
        $this->assertEquals($updatedAccount->balance, $createdOperation->amount);
        $this->assertEquals($updatedAccount->total_incomes, $createdOperation->amount);
    }

    public function test_can_update_operation()
    {
        $initSUT = $this->buildSUT(operationAmount: 40000);
        $operationId = $initSUT['operationId'];
        $operationAccount = $initSUT['account'];

        $this->repository->saveOperation($operationAccount);

        $operationAccount->updateOperation(
            operationId: new Id($operationId),
            amount: new AmountVO(30000),
            type: OperationTypeEnum::EXPENSE,
            category: new StringVO('danse'),
            detail: new StringVO('$command->detail'),
            date: new DateVO('2002-09-30 00:00:00'),
        );

        $this->repository->saveOperation($operationAccount);

        $updatedAccount = Account::whereUuid($operationAccount->id()->value())
            ->whereIsDeleted(false)
            ->first();
        $createdOperation = Operation::whereUuid($operationId)
            ->whereIsDeleted(false)
            ->first();

        $this->assertNotNull($createdOperation);
        $this->assertEquals(30000, $createdOperation->amount);
        $this->assertEquals(OperationTypeEnum::EXPENSE->value, $createdOperation->type);
        $this->assertEquals(-30000, $updatedAccount->balance);
        $this->assertEquals(30000, $updatedAccount->total_expenses);
    }

    /**
     * @return void
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function test_can_delete_operation()
    {
        $initSUT = $this->buildSUT(operationAmount: 40000);
        $operationId = $initSUT['operationId'];
        $operationAccount = $initSUT['account'];

        $this->repository->saveOperation($operationAccount);

        $operationAccount->deleteOperation(new Id($operationId));

        $this->repository->saveOperation($operationAccount);

        $updatedAccount = Account::whereUuid($operationAccount->id()->value())
            ->whereIsDeleted(false)
            ->first();
        $createdOperation = Operation::whereUuid($operationId)
            ->whereIsDeleted(false)
            ->first();

        $this->assertNull($createdOperation);
        $this->assertEquals(0, $updatedAccount->balance);
        $this->assertEquals(0, $updatedAccount->total_incomes);
    }

    /**
     * @param int $operationAmount
     * @return array
     * @throws OperationGreaterThanAccountBalanceException
     */
    private function buildSUT(int $operationAmount): array
    {
        $account = operationAccount::create(
            accountId: new Id((Account::factory()->create())->uuid),
            balance: new AmountVO(0),
            totalIncomes: new AmountVO(0),
            totalExpenses: new AmountVO(0),
        );
        $account->makeOperation(
            amount: new AmountVO($operationAmount),
            type: OperationTypeEnum::INCOME,
            category: new StringVO('operation'),
            detail: new StringVO('Detail transaction'),
            date: new DateVO('2024-09-30 00:00:00')
        );

        return [
            'account' => $account,
            'operationId' => $account->currentOperation()->id()->value(),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PdoOperationAccountRepository();
    }
}
