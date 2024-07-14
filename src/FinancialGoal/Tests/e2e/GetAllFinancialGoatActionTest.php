<?php

namespace App\FinancialGoal\Tests\e2e;

use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetAllFinancialGoatActionTest extends TestCase
{
    use RefreshDatabase;
    const GET_ALL_FINANCIAL_GOAL = 'api/financial-goals/all';
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

    public function test_can_get_all_user_financial_goals()
    {
        $initSUT = FinancialGoalSUT::asSUT()
            ->withExistingAccount()
            ->withExistingFinancialGoal(count: 5)
            ->build();

        $data = [
            'userId' => $initSUT->user->uuid,
        ];

        $response = $this->postJson(self::GET_ALL_FINANCIAL_GOAL, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(5, $response['financialGoals']);
    }
}
