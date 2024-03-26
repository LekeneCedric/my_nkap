<?php

namespace App\FinancialGoal\Tests\e2e;

use App\FinancialGoal\Infrastructure\Model\FinancialGoal;
use App\Shared\VO\DateVO;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MakeFinancialGoalActionTest extends TestCase
{
    use RefreshDatabase;

    const MAKE_FINANCIAL_GOAL = 'api/financial-goals/save';

    protected function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_can_make_financial_goal()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withExistingAccount()
            ->build();

        $data = [
           'accountId' => $initSUT->account->uuid,
           'startDate' =>  (new DateVO())->formatYMDHIS(),
           'endDate' => (new DateVO())->formatYMDHIS(),
           'desiredAmount' => 200000,
           'details' => 'want to save --200000-- to buy my new home'
        ];

        $response = $this->postJson(self::MAKE_FINANCIAL_GOAL, $data);
        $createdFinancialGoal = FinancialGoal::whereUuid($response['financialGoalId'])
            ->whereIsDeleted(false)->first();

        $response->assertOk();
        $this->assertTrue($response['isMake']);
        $this->assertEquals($response['createdAt'], (new DateVO())->formatYMDHIS());
        $this->assertNotNull($response['financialGoalId']);
        $this->assertNotNull($createdFinancialGoal);
        $this->assertEquals($createdFinancialGoal->created_at, (new DateVO())->formatYMDHIS());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_can_update_financial_goal()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withExistingAccount()
            ->withExistingFinancialGoal()
            ->build();

        $data = [
            'financialGoalId' => $initSUT->financialGoal->uuid,
            'accountId' => $initSUT->account->uuid,
            'startDate' =>  (new DateVO())->formatYMDHIS(),
            'endDate' => (new DateVO())->formatYMDHIS(),
            'desiredAmount' => 500000,
            'details' => 'want to save --200000-- to buy my new home'
        ];

        $response = $this->postJson(self::MAKE_FINANCIAL_GOAL, $data);
        $updatedFinancialGoal = FinancialGoal::whereUuid($initSUT->financialGoal->uuid)
            ->whereIsDeleted(false)->first();

        $response->assertOk();
        $this->assertTrue($response['isMake']);
        $this->assertNotNull($updatedFinancialGoal);
        $this->assertEquals($updatedFinancialGoal->updated_at, (new DateVO())->formatYMDHIS());
    }

}
