<?php

namespace App\Account\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use Tests\TestCase;

class DeleteAccountActionTest extends TestCase
{
    const DELETE_ACCOUNT_ROUTE = 'api/accounts/delete';
    public function test_can_delete_account()
    {
        $accountId = (Account::factory()->create())->uuid;

        $data = [
            'accountId' => $accountId,
        ];

        $response = $this->post(self::DELETE_ACCOUNT_ROUTE, $data);

        $deletedAccount = Account::whereUuid($accountId)->whereIsDeleted(true)->first();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['isDeleted']);
        $this->assertNotNull($deletedAccount);
    }

    public function test_can_throw_message_if_not_existing_account()
    {
        $accountId = 'wrong_account_uuid';

        $data = [
            'accountId' => $accountId,
        ];

        $response = $this->post(self::DELETE_ACCOUNT_ROUTE, $data);

        $this->assertFalse($response['status']);
        $this->assertFalse($response['isDeleted']);
    }
}
