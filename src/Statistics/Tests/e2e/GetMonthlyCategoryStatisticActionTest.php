<?php

namespace App\Statistics\Tests\e2e;

use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetMonthlyCategoryStatisticActionTest extends TestCase
{
    use RefreshDatabase;

    const GET_MONTHLY_CATEGORY_STATISTIC = 'api/statistics/monthly-category-statistics';
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

    public function test_can_get_monthly_category_statistic()
    {
        StatisticsSUT::asSUT()
            ->withExistingMonthlyCategoryStatistics(year: 2024, month: 12, userId: $this->user->uuid)
            ->build();

        $url = self::GET_MONTHLY_CATEGORY_STATISTIC.'/all'.'?userId='.$this->user->uuid.'&year=2024&month=12';

        $response = $this->getJson($url, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertNotNull($response['data']);
    }
}
