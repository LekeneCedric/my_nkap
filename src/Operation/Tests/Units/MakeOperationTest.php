<?php

namespace App\Operation\Tests\Units;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Application\Command\MakeOperation\MakeOperationCommand;
use App\Operation\Application\Command\MakeOperation\MakeOperationHandler;
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
use App\Shared\VO\StringVO;
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
            ->withCategoryId((new Id())->value())
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
     * @throws NotFoundAccountException
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function test_can_update_operation()
    {
        $initData = $this->buildSUT(withExistingOperation: true);
        $updatedOperationAmount = 30000;
        $command = makeOperationCommandBuilder::asCommand()
            ->withAccountId($initData['accountId'])
            ->withOperationId($initData['operationId'])
            ->withType(OperationTypeEnum::EXPENSE)
            ->withAmount($updatedOperationAmount)
            ->withCategoryId((new Id())->value())
            ->withDetail("Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry's standard dummy text ever since the 1500s")
            ->withDate((new DateVO())->formatYMDHIS())
            ->build();

        $response = $this->makeOperation($command);
        $updatedAccount = $this->repository->operationsAccounts[$initData['accountId']];

        $this->assertTrue($response->operationSaved);
        $this->assertEquals($initData['operationId'], $updatedAccount->currentOperation()->id()->value());
        $this->assertEquals(30000, $updatedAccount->currentOperation()->amount()->value());
        $this->assertEquals(OperationTypeEnum::EXPENSE, $updatedAccount->currentOperation()->type());
        $this->assertEquals(-30000, $updatedAccount->balance()->value());
        $this->assertEquals(30000, $updatedAccount->totalExpenses()->value());
    }

    /**
     * @return void
     * @throws NotFoundAccountException
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function test_can_throw_exception_if_expense_operation_amount_is_greater_than_balance()
    {
        $initData = $this->buildSUT();
        $operationAmount= 20000.00;
        $command = makeOperationCommandBuilder::asCommand()
            ->withAccountId($initData['accountId'])
            ->withType(OperationTypeEnum::EXPENSE)
            ->withAmount($operationAmount)
            ->withCategoryId((new Id())->value())
            ->withDetail("Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry's standard dummy text ever since the 1500s")
            ->withDate((new DateVO())->formatYMDHIS())
            ->build();

        $this->expectException(OperationGreaterThanAccountBalanceException::class);
        $this->makeOperation($command);
    }

    /**
     * @param bool $withExistingOperation
     * @return array
     * @throws OperationGreaterThanAccountBalanceException
     */
    private function buildSUT(bool $withExistingOperation = false): array
    {
        $account = operationAccount::create(
            balance: new AmountVO(0),
            totalIncomes: new AmountVO(0),
            totalExpenses: new AmountVO(0),
            accountId: new Id(),
        );

        if ($withExistingOperation) {
            $account->makeOperation(
                amount: new AmountVO(100000),
                type: OperationTypeEnum::INCOME,
                categoryId: new Id(),
                detail: new StringVO('Detail transaction'),
                date: new DateVO('2024-09-30 00:00:00')
            );
        }
        $this->repository->operationsAccounts[$account->id()->value()] = $account;
        $data = [
          'accountId' => $account->id()->value(),
        ];

        if ($withExistingOperation) {
            $data['operationId'] = $account->currentOperation()->id()->value();
        }
        return $data;
    }

    /**
     * @param MakeOperationCommand $command
     * @return makeOperationResponse
     * @throws NotFoundAccountException
     * @throws OperationGreaterThanAccountBalanceException
     */
    private function makeOperation(MakeOperationCommand $command): makeOperationResponse
    {
        $handler = new MakeOperationHandler(
            repository: $this->repository,
        );
        return $handler->handle($command);
    }
}
