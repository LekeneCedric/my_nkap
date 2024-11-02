<?php

namespace App\Operation\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\category\Infrastructure\Models\Category;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use App\Shared\Domain\VO\Id;
use App\Statistics\Infrastructure\Model\MonthlyCategoryStatistic;
use App\Statistics\Infrastructure\Model\MonthlyStatistic;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MakeManyOperationsActionTest extends TestCase
{
    use RefreshDatabase;

    const SAVE_MANY_OPERATOPMS_ROUTE = 'api/operation/add-many';
    private User $user;
    private string $token;
    public function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value() . '@gmail.com',
            'name' => 'lekene',
            'password' => bcrypt('lekene@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
    }

    public function test_can_make_many_operations()
    {
        $initData = $this->buildSUT();
        $data = [
            'operations' => [
                [
                    'accountId' => $initData['accountId'],
                    'type' => OperationTypeEnum::INCOME,
                    'amount' => 20000,
                    'categoryId' => Category::factory()->create()->uuid,
                    'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                     Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
                    'date' => '2023-09-30 15:00:00'
                ],
                [
                    'accountId' => $initData['accountId'],
                    'type' => OperationTypeEnum::EXPENSE,
                    'amount' => 10000,
                    'categoryId' => Category::factory()->create()->uuid,
                    'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                     Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
                    'date' => '2023-09-30 15:00:00'
                ]
            ]
        ];

        $response = $this->postJson(self::SAVE_MANY_OPERATOPMS_ROUTE, $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);
        $createdAccount = Account::whereUuid($initData['accountId'])->whereIsDeleted(false)->first();
        $createdOperations = Operation::with('account')->whereHas('account', function ($query) {
            $query->whereUserId($this->user->id);
        })->get();
        $monthlyStatistics = MonthlyStatistic::whereUserId($this->user->uuid)->get();
        $monthlyByCategoryStats = MonthlyCategoryStatistic::whereUserId($this->user->uuid)->get();
        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['operationsSaved']);
        $this->assertEquals(10000, $createdAccount->balance);
        $this->assertCount(2, $createdOperations);
        $this->assertNotEmpty($monthlyStatistics);
        $this->assertNotEmpty($monthlyByCategoryStats);
        $createdOperations->each(function ($operation) {
            $this->assertContains(intval($operation->amount), [20000, 10000]);
        });
    }

    public function test_can_check_if_error_in_one_operation_rollback_all_changes()
    {
        $initData = $this->buildSUT();
        $data = [
            'operations' => [
                [
                    'accountId' => $initData['accountId'],
                    'type' => OperationTypeEnum::INCOME,
                    'amount' => 20000,
                    'categoryId' => Category::factory()->create()->uuid,
                    'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                     Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
                    'date' => '2023-09-30 15:00:00'
                ],
                [
                    'accountId' => $initData['accountId'],
                    'type' => OperationTypeEnum::EXPENSE,
                    'amount' => 10000,
                    'categoryId' => Category::factory()->create()->uuid,
                    'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                     Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
                    'date' => '2023-09-30 15:00:00'
                ],
                [
                    'accountId' => 'unknow-account-id',
                    'type' => OperationTypeEnum::EXPENSE,
                    'amount' => 10000,
                    'categoryId' => Category::factory()->create()->uuid,
                    'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                     Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
                    'date' => '2023-09-30 15:00:00'
                ]
            ]
        ];

        $response = $this->postJson(self::SAVE_MANY_OPERATOPMS_ROUTE, $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);
        $createdOperations = Operation::with('account')->whereHas('account', function ($query) {
            $query->whereUserId($this->user->id);
        })->get();
        $monthlyStatistics = MonthlyStatistic::whereUserId($this->user->uuid)->get();
        $monthlyByCategoryStats = MonthlyCategoryStatistic::whereUserId($this->user->uuid)->get();

        $response->assertOk();
        $this->assertFalse($response['status']);
        $this->assertCount(0, $createdOperations);
        $this->assertEmpty($monthlyStatistics);
        $this->assertEmpty($monthlyByCategoryStats);
    }
    private function buildSUT(): array
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 0
        ]);

        return [
            'accountId' => $account->uuid,
        ];
    }
}
