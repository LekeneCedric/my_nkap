<?php

namespace App\Operation\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\category\Infrastructure\Models\Category;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use App\Shared\Domain\VO\Id;
use App\Statistics\Infrastructure\Model\MonthlyCategoryStatistic;
use App\Statistics\Infrastructure\Model\MonthlyStatistic;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeleteOperationActionTest extends TestCase
{
    use StatisticsComposedIdBuilderTrait;

    use RefreshDatabase;
    const DELETE_OPERATION = 'api/operation/delete';
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

    public function test_can_delete_operation_account(): void
    {
        $initSUT = $this->buildSUT();

        $data = [
            'accountId' => $initSUT['accountId'],
            'operationId' => $initSUT['operationId'],
        ];

        $response = $this->postJson(self::DELETE_OPERATION, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $updatedAccount = Account::whereUuid($initSUT['accountId'])->whereIsDeleted(false)->first();
        $deletedOperation = Operation::whereUuid($initSUT['operationId'])->whereIsDeleted(true)->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isDeleted']);
        $this->assertEquals(0, $updatedAccount->balance);
        $this->assertNotNull($deletedOperation);
    }

    public function test_can_update_stats_after_delete_operation()
    {
        $initSUT = $this->buildSUT();

        $data = [
            'accountId' => $initSUT['accountId'],
            'operationId' => $initSUT['operationId'],
        ];

        $response = $this->postJson(self::DELETE_OPERATION, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $response->assertOk();
        $this->assertEquals(0, MonthlyCategoryStatistic::whereComposedId($initSUT['monthlyCategoryStatisticId'])->first()->total_income);
        $this->assertEquals(0, MonthlyStatistic::whereComposedId($initSUT['monthlyStatisticId'])->first()->total_income);
    }
    private function buildSUT(): array
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 2500000
        ]);
        $category = Category::factory()->create();
        $operation = Operation::factory()->create([
            'account_id' => $account->getAttribute('id'),
            'amount' => 2500000,
            'type' => OperationTypeEnum::INCOME,
            'date' => '2024-09-30',
            'category_id' => $category->id
        ]);
        $monthlyStatisticComposedId = $this->buildMonthlyStatisticsComposedId(9, 2024, $this->user->uuid);
        $monthlyCategoryStatisticComposedId = $this->buildMonthlyCategoryStatisticsComposedId(
            month: 9,
            year: 2024,
            userId: $this->user->uuid,
            categoryId: $category->uuid);
        MonthlyStatistic::factory()->create([
            'composed_id' => $monthlyStatisticComposedId,
            'user_id' => $this->user->uuid,
            'year' => 2024,
            'month' => 9,
            'total_income' => 2500000,
        ]);

        MonthlyCategoryStatistic::factory()->create([
            'composed_id' => $monthlyCategoryStatisticComposedId,
            'user_id' => $this->user->uuid,
            'year' => 2024,
            'month' => 9,
            'total_income' => 2500000,
            'category_id' => $category->uuid
        ]);
        return [
            'accountId' => $account->uuid,
            'operationId' => $operation->uuid,
            'monthlyStatisticId' => $monthlyStatisticComposedId,
            'monthlyCategoryStatisticId' => $monthlyCategoryStatisticComposedId
        ];
    }
}
