<?php

namespace App\Account\Tests\e2e;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAllAccountActionTest extends TestCase
{
    use RefreshDatabase;
    const GET_ALL_ACCOUNT_ROUTE = 'api/accounts/all';

    public function test_can_get_all_accounts()
    {
        $numberOfAccount = 3;
        AccountSUT::asSUT()
            ->withExistingAccounts(count: $numberOfAccount)
            ->build();

        $response = $this->getJson(self::GET_ALL_ACCOUNT_ROUTE);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount($numberOfAccount, $response['accounts']);
    }
}
