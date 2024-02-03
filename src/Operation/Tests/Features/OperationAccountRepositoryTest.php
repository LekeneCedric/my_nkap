<?php

namespace App\Operation\Tests\Features;

use App\Account\Infrastructure\Models\Account;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use App\Operation\Infrastructure\PdoOperationAccountRepository;
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
