<?php

namespace App\Operation\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\category\Infrastructure\Models\Category;
use App\FinancialGoal\Infrastructure\Model\FinancialGoal;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MakeOperationActionTest extends TestCase
{
    use RefreshDatabase;

    const SAVE_OPERATION_ROUTE = 'api/operation/add';
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

    public function test_can_make_operation()
    {
        $initData = $this->buildSUT();

        $data = [
            'accountId' => $initData['accountId'],
            'type' => OperationTypeEnum::INCOME,
            'amount' => 20000,
            'categoryId' => Category::factory()->create()->uuid,
            'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
            'date' => '2023-09-30 15:00:00'
        ];

        $response = $this->postJson(self::SAVE_OPERATION_ROUTE, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $createdAccount = Account::whereUuid($initData['accountId'])->whereIsDeleted(false)->first();
        $createdOperation = Operation::whereAccountId($createdAccount->id)->whereIsDeleted(false)->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['operationSaved']);
        $this->assertEquals(20000, $createdAccount->balance);
        $this->assertEquals(20000, $createdOperation->amount);
    }

    public function test_can_update_operation()
    {
        $initData = $this->buildSUT(withExistingOperation: true);

        $data = [
          'accountId' => $initData['accountId'],
          'operationId' => $initData['operationId'],
            'type' => OperationTypeEnum::INCOME,
            'amount' => 30000,
            'categoryId' => Category::factory()->create()->uuid,
            'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
            'date' => '2023-09-30 15:00:00'
        ];

        $response = $this->postJson(self::SAVE_OPERATION_ROUTE, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $updatedAccount = Account::whereUuid($initData['accountId'])->whereIsDeleted(false)->first();
        $updatedOperation = Operation::whereUuid($initData['operationId'])->whereIsDeleted(false)->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['operationSaved']);
        $this->assertEquals(30000, $updatedOperation->amount);
        $this->assertEquals(30000, $updatedAccount->balance);
    }

    public function test_can_update_financial_goal_after_create_operation()
    {
        $initData = $this->buildSUT(
            withExistingFinancialGoal: true
        );

        $data = [
            'accountId' => $initData['accountId'],
            'type' => OperationTypeEnum::INCOME,
            'amount' => 30000,
            'categoryId' => Category::factory()->create()->uuid,
            'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
            'date' => '2024-09-30 15:00:00'
        ];

        $response = $this->postJson(self::SAVE_OPERATION_ROUTE, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $updatedAccount = Account::whereUuid($initData['accountId'])->whereIsDeleted(false)->first();
        $updatedFinancialGoal = FinancialGoal::whereUuid($initData['financialGoalId'])->whereIsDeleted(false)->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['operationSaved']);
        $this->assertEquals(30000, $updatedAccount->balance);
        $this->assertEquals(30000, $updatedFinancialGoal->current_amount);
    }

    public function test_can_update_financial_goal_after_update_operation()
    {
        $initData = $this->buildSUT(
            withExistingOperation: true,
            withExistingFinancialGoal: true
        );

        $data = [
            'accountId' => $initData['accountId'],
            'operationId' => $initData['operationId'],
            'type' => OperationTypeEnum::INCOME,
            'amount' => 30000,
            'categoryId' => Category::factory()->create()->uuid,
            'detail' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.
             Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
            'date' => '2024-09-30 15:00:00'
        ];

        $response = $this->postJson(self::SAVE_OPERATION_ROUTE, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $updatedAccount = Account::whereUuid($initData['accountId'])->whereIsDeleted(false)->first();
        $updatedOperation = Operation::whereUuid($initData['operationId'])->whereIsDeleted(false)->first();
        $updatedFinancialGoal = FinancialGoal::whereUuid($initData['financialGoalId'])->whereIsDeleted(false)->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['operationSaved']);
        $this->assertEquals(30000, $updatedOperation->amount);
        $this->assertEquals(30000, $updatedAccount->balance);
        $this->assertEquals(10000, $updatedFinancialGoal->current_amount);
    }
    private function buildSUT(
        bool $withExistingOperation = false,
        bool $withExistingFinancialGoal = false,
    ): array
    {
        $operationAmount = 20000;
        $result = [];
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => $withExistingOperation ? $operationAmount : 0
        ]);
        $result['accountId'] = $account->getAttribute('uuid');

        if ($withExistingOperation) {
            $operation = Operation::factory()->create([
                'account_id' => $account->id,
                'type' => OperationTypeEnum::INCOME,
                'amount' => 20000,
                'date' => '2024-09-30 00:00:00'
            ]);
            $result['operationId'] = $operation->getAttribute('uuid');
        }
        if ($withExistingFinancialGoal) {
            $financialGoal = FinancialGoal::factory()->create([
                'account_id' => $account->id,
                'user_id' => $this->user->id,
                'current_amount' => 0,
                'desired_amount' => 100000,
                'is_complete' => false,
                'start_date' => '2024-09-10 00:00:00',
                'end_date' =>  '2024-10-10 00:00:00'
            ]);
            $result['financialGoalId'] = $financialGoal->uuid;
        }

        return $result;
    }
}
