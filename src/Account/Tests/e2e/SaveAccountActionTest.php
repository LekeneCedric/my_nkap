<?php

namespace App\Account\Tests\e2e;

use App\Account\Infrastructure\Models\Account;
use App\Shared\VO\DateVO;
use Tests\TestCase;

class SaveAccountActionTest extends TestCase
{
    const SAVE_ACCOUNT_ROUTE = 'api/accounts/save';

    public function test_can_create_account()
    {
        $data = [
           'name' => 'compte epargne',
           'type' => 'epargne',
           'icon' => 'wallet_icon',
           'color' => 'green',
           'balance' => 20000,
           'isIncludeInTotalBalance' => true,
        ];

        $response = $this->post(self::SAVE_ACCOUNT_ROUTE, $data);

        $createdAccount = Account::whereUuid($response['accountId'])->whereIsDeleted(false)->first();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['isSaved']);
        $this->assertNotNull($response['accountId']);
        $this->assertNotNull($createdAccount);
    }

    /**
     * @throws \Exception
     */
    public function test_can_update_account()
    {
        $initData = $this->buildSUT();
        $accountId = $initData['accountId'];

        $updatedData = [
            'accountId' => $accountId,
            'name' => 'compte epargne',
            'type' => 'epargne',
            'icon' => 'wallet_icon',
            'color' => 'green',
            'balance' => 20000,
            'isIncludeInTotalBalance' => true,
        ];

        $response = $this->post(self::SAVE_ACCOUNT_ROUTE, $updatedData);
        $updatedAccount = Account::whereUuid($response['accountId'])->whereIsDeleted(false)->first();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['isSaved']);
        $this->assertNotNull($response['accountId']);
        $this->assertEquals((new DateVO())->formatYMDHIS(), $updatedAccount->updated_at);
    }

    public function test_can_throw_message_if_invalid_request_params_when_create_account()
    {
        $data = [
            'name' => 'compte epargne',
            'type' => 'epargne',
            'icon' => 'wallet_icon',
            'color' => 'green',
        ];

        $response = $this->post(self::SAVE_ACCOUNT_ROUTE, $data);

        $this->assertNotNull($response['balance']);
        $this->assertNotNull($response['isIncludeInTotalBalance']);
    }
    private function buildSUT(): array
    {
        $account = Account::factory()->create();

        return [
            'accountId' => $account->uuid,
        ];
    }
}
