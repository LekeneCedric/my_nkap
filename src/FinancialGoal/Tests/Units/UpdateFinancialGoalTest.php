<?php

namespace App\FinancialGoal\Tests\Units;

use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalCommand;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalHandler;
use App\FinancialGoal\Domain\FinancialGoal;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\FinancialGoal\Tests\Units\Repository\InMemoryFinancialGoalRepository;
use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use Exception;
use Tests\TestCase;

class UpdateFinancialGoalTest extends TestCase
{
    private FinancialGoalRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryFinancialGoalRepository();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_can_update_financial_goal_after_create_operation()
    {
        $initSUT = $this->buildSUT(currentAmount: 1000);
        $command = new UpdateFinancialGoalCommand(
            accountId: $initSUT['accountId'],
            previousAmount: 0,
            newAmount: 4000,
            operationDate: '2024-07-20 00:00:00',
            type: OperationTypeEnum::INCOME
        );
        $this->updateFinancial($command);
        $updateFinancialGoal = $this->repository->financialsGoals[$initSUT['financialGoalId']];
        $this->assertEquals(5000, $updateFinancialGoal->toDto()->currentAmount);
        $this->assertTrue($updateFinancialGoal->toDto()->isComplete);
    }

    /**
     * @throws Exception
     */
    public function test_can_update_financial_goal_after_update_operation()
    {
        $initSUT = $this->buildSUT(currentAmount: 2000);
        $command = new UpdateFinancialGoalCommand(
            accountId: $initSUT['accountId'],
            previousAmount: 1000,
            newAmount: 2000,
            operationDate: '2024-07-20 00:00:00',
            type: OperationTypeEnum::EXPENSE
        );
        $this->updateFinancial($command);
        $updateFinancialGoal = $this->repository->financialsGoals[$initSUT['financialGoalId']];
        $this->assertEquals(1000, $updateFinancialGoal->toDto()->currentAmount);
        $this->assertFalse($updateFinancialGoal->toDto()->isComplete);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_can_update_financial_goal_after_delete_operation()
    {
        $initSUT = $this->buildSUT(currentAmount: 2000);
        $command = new UpdateFinancialGoalCommand(
            accountId: $initSUT['accountId'],
            previousAmount: 1000,
            newAmount: 1000,
            operationDate: '2024-07-20 00:00:00',
            type: OperationTypeEnum::INCOME,
            isDelete: true,
        );
        $this->updateFinancial($command);
        $updateFinancialGoal = $this->repository->financialsGoals[$initSUT['financialGoalId']];
        $this->assertEquals(1000, $updateFinancialGoal->toDto()->currentAmount);
        $this->assertFalse($updateFinancialGoal->toDto()->isComplete);
    }

    /**
     * @param UpdateFinancialGoalCommand $command
     * @return void
     * @throws Exception
     */
    private function updateFinancial(UpdateFinancialGoalCommand $command): void
    {
        $handler = new UpdateFinancialGoalHandler(
            repository: $this->repository,
        );
        $handler->handle($command);
    }

    private function buildSUT(int $currentAmount = 0): array
    {
        $accountId = (new Id())->value();
        $financialGoal = FinancialGoal::create(
            userId: new Id(),
            accountId: new Id($accountId),
            startDate: new DateVO('2024-07-10 00:00:00'),
            enDate: new DateVO('2024-07-30 00:00:00'),
            desiredAmount: new AmountVO(5000),
            details: new StringVO('Acheter voiture familiale'),
            currentAmount: new AmountVO($currentAmount),
        );
        $this->saveInitDataInMemory($financialGoal);
        return [
            'accountId' => $accountId,
            'financialGoalId' => $financialGoal->toDto()->id,
        ];
    }

    private function saveInitDataInMemory(FinancialGoal $financialGoal): void
    {
        $this->repository->financialsGoals[$financialGoal->toDto()->id] = $financialGoal;
    }
}
