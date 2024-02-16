<?php

namespace App\Operation\Tests\e2e;

use App\Account\Infrastructure\Models\Account;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use Tests\TestCase;

class MakeOperationActionTest extends TestCase
{
    const SAVE_OPERATION_ROUTE = 'api/operation/add';

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

        $response = $this->postJson(self::SAVE_OPERATION_ROUTE, $data);
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

        $response = $this->postJson(self::SAVE_OPERATION_ROUTE, $data);
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
