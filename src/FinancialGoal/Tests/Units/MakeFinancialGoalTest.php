<?php

namespace App\FinancialGoal\Tests\Units;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Tests\Units\AccountSUT;
use App\FinancialGoal\Application\Command\Make\MakeFinancialGoalCommand;
use App\FinancialGoal\Application\Command\Make\MakeFinancialGoalHandler;
use App\FinancialGoal\Application\Command\Make\MakeFinancialGoalResponse;
use App\FinancialGoal\Domain\Exceptions\ErrorOnSaveFinancialGoalException;
use App\FinancialGoal\Domain\FinancialGoal;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\FinancialGoal\Domain\Service\CheckIfAccountExitByIdService;
use App\FinancialGoal\Tests\Units\Builder\MakeFinancialGoalCommandBuilder;
use App\FinancialGoal\Tests\Units\Repository\InMemoryFinancialGoalRepository;
use App\FinancialGoal\Tests\Units\Service\InMemoryCheckIfAccountExitByIdService;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Exception;
use Tests\TestCase;

class MakeFinancialGoalTest extends TestCase
{
    private CheckIfAccountExitByIdService $getAccountByIdService;
    private FinancialGoalRepository $repository;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->repository = new InMemoryFinancialGoalRepository();
        $this->getAccountByIdService = new InMemoryCheckIfAccountExitByIdService();
    }

    /**
     * @return void
     * @throws ErrorOnSaveFinancialGoalException
     * @throws NotFoundAccountException
     */
    public function test_can_make_financial_goal()
    {
        $initSUT = $this->buildSUT();

        $command = MakeFinancialGoalCommandBuilder::asCommand()
            ->withAccountId($initSUT['accountId'])
            ->withStartDate((new DateVO())->formatYMDHIS())
            ->withEndDate((new DateVO('2023-09-30 00:00:00'))->formatYMDHIS())
            ->withDesiredAmount(1000000)
            ->withDetail('Save 1000000 XCFA avant la fin du mois')
            ->build();

        $response = $this->makeFinancialGoal($command);

        $savedFinancialGoal = $this->repository->financialsGoal[$response->financialGoalId];
        $this->assertTrue($response->isMake);
        $this->assertNotNull($response->financialGoalId);
        $this->assertNotNull($savedFinancialGoal);
        $this->assertEquals((new DateVO())->formatYMDHIS(), $savedFinancialGoal->createdAt()->formatYMDHIS());
    }

    /**
     * @return void
     * @throws ErrorOnSaveFinancialGoalException
     * @throws NotFoundAccountException
     */
    public function test_can_update_financial_goal()
    {
        $initSUT = $this->buildSUT(withExistingFinancialGoal: true);

        $command = MakeFinancialGoalCommandBuilder::asCommand()
            ->withFinancialGoalId($initSUT['financialGoalId'])
            ->withAccountId($initSUT['accountId'])
            ->withStartDate((new DateVO('2022-09-20 00:00:00'))->formatYMDHIS())
            ->withEndDate((new DateVO('2025-09-20 00:00:00'))->formatYMDHIS())
            ->withDesiredAmount(50000)
            ->withDetail('save 50 000 XCFA before 2025')
            ->build();

        $response = $this->makeFinancialGoal($command);

        $updatedFinancialGoal = $this->repository->financialsGoal[$initSUT['financialGoalId']];

        $this->assertTrue($response->isMake);
        $this->assertNull($response->financialGoalId);
        $this->assertNotNull($updatedFinancialGoal);
        $this->assertEquals((new DateVO())->formatYMDHIS(), $updatedFinancialGoal->updatedAt()->formatYMDHIS());
        $this->assertEquals(50000, $updatedFinancialGoal->desiredAmount()->value());
    }

    /**
     * @return void
     * @throws ErrorOnSaveFinancialGoalException
     * @throws NotFoundAccountException
     */
    public function test_can_throw_not_found_account_exception()
    {
        $command = MakeFinancialGoalCommandBuilder::asCommand()
            ->withAccountId('wrong_account_id')
            ->withStartDate((new DateVO())->formatYMDHIS())
            ->withEndDate((new DateVO('2023-09-30 00:00:00'))->formatYMDHIS())
            ->withDesiredAmount(1000000)
            ->withDetail('Save 1000000 XCFA avant la fin du mois')
            ->build();

        $this->expectException(NotFoundAccountException::class);
        $this->makeFinancialGoal($command);
    }

    private function buildSUT(
        bool $withExistingFinancialGoal = false,
    ): array
    {
        $accountSUT = AccountSUT::asSUT()
            ->withExistingAccount();

        $financialGoal = null;
        $account = $accountSUT->account;
        $this->getAccountByIdService->accounts[$account->id()->value()] = $account;

        if ($withExistingFinancialGoal) {
          $financialGoal = FinancialGoal::create(
              accountId: new Id($account->id()->value()),
              startDate: new DateVO(),
              enDate: new DateVO('2024-09-30 00:00:00'),
              desiredAmount: new AmountVO(100000),
              details: new StringVO('save 100 000 CFA before september 2024'),
              currentAmount: new AmountVO(0)
          );
          $this->repository->financialsGoal[$financialGoal->id()->value()] = $financialGoal;
        }
        return [
            'accountId' => $account->id()->value(),
            'financialGoalId' => $financialGoal?->id()->value(),
        ];
    }

    /**
     * @param MakeFinancialGoalCommand $command
     * @return MakeFinancialGoalResponse
     * @throws NotFoundAccountException
     * @throws ErrorOnSaveFinancialGoalException
     */
    private function makeFinancialGoal(MakeFinancialGoalCommand $command): MakeFinancialGoalResponse
    {
        $handler = new MakeFinancialGoalHandler(
            repository: $this->repository,
            checkIfAccountExistByIdService: $this->getAccountByIdService
        );

        return $handler->handle($command);
    }
}
