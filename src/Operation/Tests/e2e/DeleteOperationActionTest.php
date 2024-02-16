<?php

namespace App\Operation\Tests\e2e;

use App\Account\Infrastructure\Models\Account;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use Tests\TestCase;

class DeleteOperationActionTest extends TestCase
{

    const DELETE_OPERATION = 'api/operation/delete';

    public function test_can_delete_operation_account(): void
    {
        $initSUT = $this->buildSUT();

        $data = [
            'accountId' => $initSUT['accountId'],
            'operationId' => $initSUT['operationId'],
        ];

        $response = $this->postJson(self::DELETE_OPERATION, $data);
        $updatedAccount = Account::whereUuid($initSUT['accountId'])->whereIsDeleted(false)->first();
        $deletedOperation = Operation::whereUuid($initSUT['operationId'])->whereIsDeleted(true)->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isDeleted']);
        $this->assertEquals(0, $updatedAccount->balance);
        $this->assertNotNull($deletedOperation);
    }

    private function buildSUT(): array
    {
        $account = Account::factory()->create([
            'balance' => 2500000
        ]);
        $operation = Operation::factory()->create([
            'account_id' => $account->getAttribute('id'),
            'amount' => 2500000,
            'type' => OperationTypeEnum::INCOME
        ]);

        return [
            'accountId' => $account->getAttribute('uuid'),
            'operationId' => $operation->getAttribute('uuid')
        ];
    }
}
