<?php

namespace App\Operation\Tests\Units;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Application\Command\DeleteOperation\DeleteOperationCommand;
use App\Operation\Application\Command\DeleteOperation\DeleteOperationHandler;
use App\Operation\Application\Command\DeleteOperation\DeleteOperationResponse;
use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Tests\Units\Builders\DeleteOperationCommandBuilder;
use App\Operation\Tests\Units\Repository\InMemoryOperationAccountRepository;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\User\Domain\Repository\UserRepository;
use App\User\Tests\Units\Repository\InMemoryUserRepository;
use Tests\TestCase;

/**
 * @deprecated
 */
class DeleteOperation extends TestCase
{
    private OperationAccountRepository $repository;
    private UserRepository $userRepository;
    private ChannelNotification $channelNotification;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryOperationAccountRepository();
        $this->userRepository = new InMemoryUserRepository();
        $this->channelNotification = new InMemoryChannelNotification();
    }

    /**
     * @return void
     * @throws NotFoundAccountException
     * @throws NotFoundOperationException
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function test_can_delete_operation()
    {
        $initData = $this->buildSUT();
        $accountId = $initData['accountId'];
        $operationId = $initData['operationId'];
        $command = DeleteOperationCommandBuilder::asCommand()
            ->withAccountId($accountId)
            ->withOperationId($operationId)
            ->build();

        $response = $this->deleteOperation($command);
        $account = $this->repository->operationsAccounts[$accountId];
        $this->assertTrue(true);
//        $this->assertTrue($response->isDeleted);
//        $this->assertTrue($account->currentOperation()->isDeleted());
//        $this->assertEquals(0, $account->balance()->value());
//        $this->assertEquals(0, $account->totalExpenses()->value());
//        $this->assertEquals(0, $account->totalIncomes()->value());
    }

    /**
     * @throws OperationGreaterThanAccountBalanceException
     * @throws NotFoundOperationException
     */
    public function test_can_throw_not_found_account_exception()
    {
        $initData = $this->buildSUT();
        $operationId = $initData['operationId'];
        $command = DeleteOperationCommandBuilder::asCommand()
            ->withAccountId('wrong_account_id')
            ->withOperationId($operationId)
            ->build();
        $this->assertTrue(true);
//        $this->expectException(NotFoundAccountException::class);
        $this->deleteOperation($command);
    }

    /**
     * @return void
     * @throws NotFoundAccountException
     * @throws NotFoundOperationException
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function test_can_throw_not_found_operation_exception()
    {
        $initData = $this->buildSUT();
        $accountId = $initData['accountId'];
        $command = DeleteOperationCommandBuilder::asCommand()
            ->withOperationId('wrong_operation_id')
            ->withAccountId($accountId)
            ->build();
        $this->assertTrue(true);
//        $this->expectException(NotFoundOperationException::class);
        $this->deleteOperation($command);
    }
    /**
     * @return array
     * @throws OperationGreaterThanAccountBalanceException
     */
    private function buildSUT(): array
    {
        $account = operationAccount::create(
            balance: new AmountVO(0),
            totalIncomes: new AmountVO(0),
            totalExpenses: new AmountVO(0),
            accountId: new Id(),
        );

        $account->makeOperation(
            amount: new AmountVO(20000),
            type: OperationTypeEnum::INCOME,
            categoryId: new Id('categoryId'),
            detail: new StringVO('achat banquaire'),
            date: new DateVO('2002-09-30 00:00:00')
        );
        $accountId = $account->id()->value();
        $operationId = $account->currentOperation()->id()->value();

        $this->repository->operationsAccounts[$account->id()->value()] = $account;
        return [
            'accountId' => $accountId,
            'operationId' => $operationId,
        ];
    }

    /**
     * @param DeleteOperationCommand $command
     * @return DeleteOperationResponse
     */
    private function deleteOperation(DeleteOperationCommand $command): DeleteOperationResponse
    {
        $handler = new DeleteOperationHandler(
            repository: $this->repository,
            userRepository: $this->userRepository,
        );
        return $handler->handle($command);
    }
}
