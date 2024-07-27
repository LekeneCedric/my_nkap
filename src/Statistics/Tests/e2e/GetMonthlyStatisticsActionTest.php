<?php

namespace App\Statistics\Tests\e2e;

use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetMonthlyStatisticsActionTest extends TestCase
{
    use RefreshDatabase;

    const GET_MONTHLY_STATISTICS = 'api/statistics/monthly-statistics';
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

    public function test_can_get_monthly_statistics()
    {
        StatisticsSUT::asSUT()
            ->withExistingMonthlyStatistic(month: 12, userId: $this->user->uuid)
            ->withExistingMonthlyStatistic(month: 2, userId: $this->user->uuid)
            ->withExistingMonthlyStatistic(month: 1, userId: $this->user->uuid, totalIncome: 200000, totalExpense: 100000)
            ->build();

        $url = self::GET_MONTHLY_STATISTICS.'/all'.'?userId='.$this->user->uuid.'&year=2024&month=2';
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        ;
        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertNotNull($response['data']);
    }
}
