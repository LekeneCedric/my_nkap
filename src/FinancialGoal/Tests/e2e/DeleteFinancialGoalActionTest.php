<?php

namespace App\FinancialGoal\Tests\e2e;

use App\FinancialGoal\Infrastructure\Model\FinancialGoal;
use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeleteFinancialGoalActionTest extends TestCase
{
    use RefreshDatabase;

    const DELETE_FINANCIAL_GOAL = 'api/financial-goals/delete';
    private User $user;
    private string $token;
    protected function setUp(): void
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

    public function test_can_delete_financial_goal()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withExistingAccount()
            ->withExistingFinancialGoal()
            ->build();

        $data = [
            'financialGoalId' => $initSUT->financialGoal->uuid,
        ];

        $response = $this->postJson(self::DELETE_FINANCIAL_GOAL, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $deletedFinancailGoal = FinancialGoal::whereUuid($initSUT->financialGoal->uuid)
            ->whereIsDeleted(true)->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isDeleted']);
        $this->assertNotNull($deletedFinancailGoal);
    }

    public function test_can_throw_error_message_when_try_to_delete_not_found_financial_goal()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withExistingAccount()
            ->withExistingFinancialGoal()
            ->build();

        $data = [
            'financialGoalId' => 'wrong_financial_goal_id',
        ];

        $response = $this->postJson(self::DELETE_FINANCIAL_GOAL, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $deletedFinancailGoal = FinancialGoal::whereUuid($initSUT->financialGoal->uuid)
            ->whereIsDeleted(true)->first();

        $response->assertOk();
        $this->assertFalse($response['status']);
        $this->assertFalse($response['isDeleted']);
        $this->assertNull($deletedFinancailGoal);
    }
}
