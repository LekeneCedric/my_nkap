<?php

namespace App\Operation\Tests\Units;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Application\Command\MakeOperation\makeOperationCommand;
use App\Operation\Application\Command\MakeOperation\makeOperationHandler;
use App\Operation\Application\Command\MakeOperation\makeOperationResponse;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Tests\Units\Builders\makeOperationCommandBuilder;
use App\Operation\Tests\Units\Repository\InMemoryOperationAccountRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use Exception;
use Tests\TestCase;

class MakeOperationTest extends TestCase
{
    private OperationAccountRepository $repository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryOperationAccountRepository();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_can_make_operation()
    {
        $initData = $this->buildSUT();
        $operationAmount= 20000.00;
        $command = makeOperationCommandBuilder::asCommand()
            ->withAccountId($initData['accountId'])
            ->withType(OperationTypeEnum::INCOME)
            ->withAmount($operationAmount)
            ->withCategory('salary')
            ->withDetail("Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry's standard dummy text ever since the 1500s")
            ->withDate((new DateVO())->formatYMDHIS())
            ->build();

        $response = $this->makeOperation($command);
        $createdAccount = $this->repository->operationsAccounts[$initData['accountId']];

        $this->assertTrue($response->operationSaved);
        $this->assertEquals($createdAccount->currentOperation()->id()->value(), $response->operationId);
        $this->assertEquals($createdAccount->balance()->value(), $operationAmount);
        $this->assertEquals($createdAccount->totalIncomes()->value(), $operationAmount);
        $this->assertEquals(0, $createdAccount->totalExpenses()->value());
    }

    /**
     * @return void
     * @throws NotFoundAccountException
     */
    public function test_can_throw_exception_if_expense_operation_amount_is_greater_than_balance()
    {
        $initData = $this->buildSUT();
        $operationAmount= 20000.00;
        $command = makeOperationCommandBuilder::asCommand()
            ->withAccountId($initData['accountId'])
            ->withType(OperationTypeEnum::EXPENSE)
            ->withAmount($operationAmount)
            ->withCategory('salary')
            ->withDetail("Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry's standard dummy text ever since the 1500s")
            ->withDate((new DateVO())->formatYMDHIS())
            ->build();

        $this->expectException(OperationGreaterThanAccountBalanceException::class);
        $this->makeOperation($command);
    }

    private function buildSUT(): array
    {
        $account = operationAccount::create(
            accountId: new Id(),
            balance: new AmountVO(0),
            totalIncomes: new AmountVO(0),
            totalExpenses: new AmountVO(0),
        );

        $this->repository->operationsAccounts[$account->id()->value()] = $account;
        return [
          'accountId' => $account->id()->value(),
        ];
    }

    /**
     * @param makeOperationCommand $command
     * @return makeOperationResponse
     * @throws NotFoundAccountException
     * @throws OperationGreaterThanAccountBalanceException
     */
    private function makeOperation(makeOperationCommand $command): makeOperationResponse
    {
        $handler = new makeOperationHandler(
            repository: $this->repository,
        );
        return $handler->handle($command);
    }
}
