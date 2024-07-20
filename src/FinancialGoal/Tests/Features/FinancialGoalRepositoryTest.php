<?php

namespace App\FinancialGoal\Tests\Features;

use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\FinancialGoal\Infrastructure\Repository\PdoFinancialGoalRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinancialGoalRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private FinancialGoalRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new PdoFinancialGoalRepository();
        DB::rollBack();
    }

    public function test_can_create_financial_goal()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withFinancialGoal()
            ->build();

        $this->repository->save($initSUT->financialGoal);

        $createdFinancialGoalDb = DB::table('financial_goals')->where('uuid', $initSUT->financialGoal->id()->value())
            ->where('is_deleted', false)
            ->first();

        $this->assertNotNull($createdFinancialGoalDb);
        $this->assertEquals(200000, $createdFinancialGoalDb->desired_amount);
    }

    public function test_can_update_financial_goal()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withFinancialGoal()
            ->build();

        $financialGoal = $initSUT->financialGoal;

        $this->repository->save($financialGoal);

        $financialGoal->changeDesiredAmount(new AmountVO(3000000000));
        $financialGoal->changeDetails(new StringVO('i want to save 300 000 000 XCFA per month'));

        $this->repository->save($financialGoal);

        $updatedFinancialGoalDb = DB::table('financial_goals')->where('uuid', $financialGoal->id()->value())
            ->where('is_deleted', false)
            ->whereNotNull('updated_at')
            ->first();

        $this->assertNotNull($updatedFinancialGoalDb);
        $this->assertEquals((new DateVO())->formatYMDHIS(), $updatedFinancialGoalDb->updated_at);
    }
    public function test_can_get_financial_goal_by_id()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withFinancialGoal()
            ->build();

        $this->repository->save($initSUT->financialGoal);

        $financialGoal = $this->repository->byId(new Id($initSUT->financialGoal->id()->value()));

        $this->assertNotNull($financialGoal);
        $this->assertEquals(200000, $financialGoal->desiredAmount()->value());
    }
    public function test_can_delete_financial_goal()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withFinancialGoal()
            ->build();

        $financialGoal = $initSUT->financialGoal;

        $this->repository->save($financialGoal);

        $financialGoal->delete();

        $this->repository->save($financialGoal);

        $deletedFinancialGoalDb = DB::table('financial_goals')->where('uuid', $financialGoal->id()->value())
            ->where('is_deleted', true)
            ->first();

        $this->assertNotNull($deletedFinancialGoalDb);
    }
}
