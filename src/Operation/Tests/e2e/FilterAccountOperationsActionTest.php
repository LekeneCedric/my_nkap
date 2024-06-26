<?php

namespace App\Operation\Tests\e2e;

use App\Account\Tests\e2e\AccountSUT;
use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterAccountOperationsActionTest extends TestCase
{
    use RefreshDatabase;

    const FILTER_ACCOUNT_OPERATION = 'api/operation/filter';
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

    public function test_can_get_account_operations()
    {
        $initData = AccountSUT::asSUT()
            ->withExistingAccounts(count: 2)
            ->withExistingOperationsPerAccounts(count: 10)
            ->build();

        $data = [
            'accountId' => $initData->accounts[0]->getAttributeValue('uuid'),
        ];

        $response = $this->postJson(self::FILTER_ACCOUNT_OPERATION, $data, [
            'Authorization' => 'Bearer '.$this->token
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(10, $response['operations']);
    }
}
