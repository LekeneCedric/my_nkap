<?php

namespace App\Operation\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Tests\TestCase;

class MakeOperationActionTest extends TestCase
{
    const SAVE_OPERATION_ROUTE = 'api/operation/add';
    private User $user;
    private string $token;
    protected function setUp(): void
    {
        parent::setUp();
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
            'category' => 'salary',
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
            'category' => 'salary',
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

    private function buildSUT(bool $withExistingOperation = false): array
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
            ]);
            $result['operationId'] = $operation->getAttribute('uuid');
        }

        return $result;
    }
}
