<?php

namespace App\Account\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeleteAccountActionTest extends TestCase
{
    use RefreshDatabase;

    const DELETE_ACCOUNT_ROUTE = 'api/accounts/delete';
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

    public function test_can_delete_account()
    {
        $accountId = (Account::factory()->create(['user_id' => $this->user->id, 'icon' => 'icon']))->uuid;

        $data = [
            'accountId' => $accountId,
        ];

        $response = $this->post(self::DELETE_ACCOUNT_ROUTE, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

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

        $response = $this->post(self::DELETE_ACCOUNT_ROUTE, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $this->assertFalse($response['status']);
        $this->assertFalse($response['isDeleted']);
    }
}
