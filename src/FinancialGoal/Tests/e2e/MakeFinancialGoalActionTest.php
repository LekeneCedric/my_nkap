<?php

namespace App\FinancialGoal\Tests\e2e;

use App\FinancialGoal\Infrastructure\Model\FinancialGoal;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MakeFinancialGoalActionTest extends TestCase
{
    use RefreshDatabase;

    const MAKE_FINANCIAL_GOAL = 'api/financial-goals/save';
    private User $user;
    private string $token;

    public function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value().'@gmail.com',
            'name' => 'lekene',
            'password' => bcrypt('lekene@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
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
           'userId' => $initSUT->user->uuid,
           'accountId' => $initSUT->account->uuid,
           'startDate' =>  (new DateVO())->formatYMD(),
           'endDate' => (new DateVO())->formatYMD(),
           'desiredAmount' => 200000,
           'details' => 'want to save --200000-- to buy my new home'
        ];

        $response = $this->postJson(self::MAKE_FINANCIAL_GOAL, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

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
            'userId' => $initSUT->user->uuid,
            'accountId' => $initSUT->account->uuid,
            'startDate' =>  (new DateVO())->formatYMD(),
            'endDate' => (new DateVO())->formatYMD(),
            'desiredAmount' => 500000,
            'details' => 'want to save --200000-- to buy my new home'
        ];

        $response = $this->postJson(self::MAKE_FINANCIAL_GOAL, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $updatedFinancialGoal = FinancialGoal::whereUuid($initSUT->financialGoal->uuid)
            ->whereIsDeleted(false)->first();

        $response->assertOk();
        $this->assertTrue($response['isMake']);
        $this->assertNotNull($updatedFinancialGoal);
        $this->assertEquals($updatedFinancialGoal->updated_at, (new DateVO())->formatYMDHIS());
    }

    /**
     * @throws Exception
     */
    public function test_can_throw_error_message_when_not_found_account()
    {
        $data = [
            'userId' => 'wrong_user_id',
            'accountId' => 'wrong_account_id',
            'startDate' =>  (new DateVO())->formatYMD(),
            'endDate' => (new DateVO())->formatYMD(),
            'desiredAmount' => 200000,
            'details' => 'want to save --200000-- to buy my new home'
        ];

        $response = $this->postJson(self::MAKE_FINANCIAL_GOAL, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk();
        $this->assertArrayNotHasKey('isMake', $response);
        $this->assertArrayNotHasKey('createdAt', $response);
        $this->assertArrayNotHasKey('financialGoalId', $response);
        $this->assertNotNull($response['message']);
    }
}
