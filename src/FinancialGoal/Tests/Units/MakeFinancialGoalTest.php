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
use App\FinancialGoal\Domain\FinancialGoalUser;
use App\FinancialGoal\Domain\Service\CheckIfAccountExitByIdService;
use App\FinancialGoal\Domain\Service\CheckIfUserExistByIdService;
use App\FinancialGoal\Tests\Units\Builder\MakeFinancialGoalCommandBuilder;
use App\FinancialGoal\Tests\Units\Repository\InMemoryFinancialGoalRepository;
use App\FinancialGoal\Tests\Units\Service\InMemoryCheckIfAccountExitByIdService;
use App\FinancialGoal\Tests\Units\Service\InMemoryCheckIfUserExistByIdService;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Tests\TestCase;

class MakeFinancialGoalTest extends TestCase
{
    private CheckIfAccountExitByIdService $checkIfAccountExitByIdService;
    private CheckIfUserExistByIdService $checkIfUserExistByIdService;
    private FinancialGoalRepository $repository;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->repository = new InMemoryFinancialGoalRepository();
        $this->checkIfAccountExitByIdService = new InMemoryCheckIfAccountExitByIdService();
        $this->checkIfUserExistByIdService = new InMemoryCheckIfUserExistByIdService();
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
            ->withUserId($initSUT['userId'])
            ->withAccountId($initSUT['accountId'])
            ->withStartDate((new DateVO())->formatYMDHIS())
            ->withEndDate((new DateVO('2023-09-30 00:00:00'))->formatYMDHIS())
            ->withDesiredAmount(1000000)
            ->withDetail('Save 1000000 XCFA avant la fin du mois')
            ->build();

        $response = $this->makeFinancialGoal($command);

        $savedFinancialGoal = $this->repository->financialsGoals[$response->financialGoalId];
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
            ->withUserId($initSUT['userId'])
            ->withAccountId($initSUT['accountId'])
            ->withStartDate((new DateVO('2022-09-20 00:00:00'))->formatYMDHIS())
            ->withEndDate((new DateVO('2025-09-20 00:00:00'))->formatYMDHIS())
            ->withDesiredAmount(50000)
            ->withDetail('save 50 000 XCFA before 2025')
            ->build();

        $response = $this->makeFinancialGoal($command);

        $updatedFinancialGoal = $this->repository->financialsGoals[$initSUT['financialGoalId']];

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
        $initSUT = $this->buildSUT();

        $command = MakeFinancialGoalCommandBuilder::asCommand()
            ->withAccountId('wrong_account_id')
            ->withUserId($initSUT['userId'])
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
        $userId = new Id();
        $financialGoal = null;
        $account = $accountSUT->account;

        $this->checkIfAccountExitByIdService->accounts[$account->id()->value()] = $account;
        $this->checkIfUserExistByIdService->users[$userId->value()] = $userId;

        if ($withExistingFinancialGoal) {
          $financialGoal = FinancialGoal::create(
              userId: $userId,
              accountId: $account->id(),
              startDate: new DateVO(),
              enDate: new DateVO('2024-09-30 00:00:00'),
              desiredAmount: new AmountVO(100000),
              details: new StringVO('save 100 000 CFA before september 2024'),
              currentAmount: new AmountVO(0)
          );
          $this->repository->financialsGoals[$financialGoal->id()->value()] = $financialGoal;
        }
        return [
            'userId' => $userId->value(),
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
            checkIfAccountExistByIdService: $this->checkIfAccountExitByIdService,
            checkIfUserExistByIdService: $this->checkIfUserExistByIdService,
        );

        return $handler->handle($command);
    }
}
