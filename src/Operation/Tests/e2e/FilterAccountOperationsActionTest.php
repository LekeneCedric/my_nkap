<?php

namespace App\Operation\Tests\e2e;

use App\Account\Tests\e2e\AccountSUT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterAccountOperationsActionTest extends TestCase
{
    use RefreshDatabase;

    const FILTER_ACCOUNT_OPERATION = 'api/operation/filter';

    public function test_can_get_account_operations()
    {
        $initData = AccountSUT::asSUT()
            ->withExistingAccounts(count: 2)
            ->withExistingOperationsPerAccounts(count: 10)
            ->build();

        $data = [
            'accountId' => $initData->accounts[0]->getAttributeValue('uuid'),
        ];

        $response = $this->postJson(self::FILTER_ACCOUNT_OPERATION, $data);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(10, $response['operations']);
    }
}
