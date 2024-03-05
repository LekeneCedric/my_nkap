<?php

namespace App\FinancialGoal\Tests\Units;

use App\Account\Tests\Units\AccountSUT;
use App\FinancialGoal\Application\Command\Delete\DeleteFinancialGoalCommand;
use App\FinancialGoal\Application\Command\Delete\DeleteFinancialGoalHandler;
use App\FinancialGoal\Application\Command\Delete\DeleteFinancialGoalResponse;
use App\FinancialGoal\Domain\Exception\NotFoundFinancialGoalException;
use App\FinancialGoal\Domain\FinancialGoal;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\FinancialGoal\Tests\Units\Builder\DeleteFinancialGoalCommandBuilder;
use App\FinancialGoal\Tests\Units\Repository\InMemoryFinancialGoalRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Tests\TestCase;

class DeleteFinancialGoalTest extends TestCase
{
    private FinancialGoalRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryFinancialGoalRepository();
    }

    public function test_can_delete_financial_goal()
    {
        $initSUT = $this->buildSUT();

        $command = DeleteFinancialGoalCommandBuilder::asCommand()
            ->withFinancialGoalId($initSUT['financialGoalId'])
            ->build();

        $response = $this->deleteFinancialGoal($command);

        $this->assertTrue($response->status);
        $this->assertTrue($response->isDeleted);
        $this->assertTrue($this->repository->financialsGoal[$initSUT['financialGoalId']]->isDeleted());
    }

    // test can throw not found financial goal exception

    /**
     * @return void
     * @throws NotFoundFinancialGoalException
     */
    public function test_can_throw_not_found_financial_goal()
    {
        $command = DeleteFinancialGoalCommandBuilder::asCommand()
            ->withFinancialGoalId('wong_financial_goal')
            ->build();

        $this->expectException(NotFoundFinancialGoalException::class);

        $this->deleteFinancialGoal($command);
    }
    /**
     * @param DeleteFinancialGoalCommand $command
     * @return DeleteFinancialGoalResponse
     * @throws NotFoundFinancialGoalException
     */
    private function deleteFinancialGoal(DeleteFinancialGoalCommand $command): DeleteFinancialGoalResponse
    {
        $handler = new DeleteFinancialGoalHandler(
            repository: $this->repository,
        );

        return $handler->handle($command);
    }

    private function buildSUT(): array
    {
        $accountSUT = AccountSUT::asSUT()
            ->withExistingAccount();

        $account = $accountSUT->account;

        $financialGoal = FinancialGoal::create(
            accountId: new Id($account->id()->value()),
            startDate: new DateVO(),
            enDate: new DateVO('2025-09-30 08:00:00'),
            desiredAmount: new AmountVO(100000),
            details: new StringVO('Epargner 100 000 F avant septembre')
        );

        $this->repository->financialsGoal[$financialGoal->id()->value()] = $financialGoal;

        return [
            'financialGoalId' => $financialGoal->id()->value(),
        ];
    }
}
