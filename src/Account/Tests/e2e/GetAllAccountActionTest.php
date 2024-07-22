<?php

namespace App\Account\Tests\e2e;

use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetAllAccountActionTest extends TestCase
{
    use RefreshDatabase;

    const GET_ALL_ACCOUNT_ROUTE = 'api/accounts/all';
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

    public function test_can_get_all_accounts()
    {
        $numberOfAccount = 3;
        $initSUT = AccountSUT::asSUT()
            ->withExistingAccounts(count: $numberOfAccount)
            ->withExistingAccounts(count: $numberOfAccount, userId: $this->user->id)
            ->build();

        $response = $this->getJson(self::GET_ALL_ACCOUNT_ROUTE.'/'.$initSUT->user->uuid, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        // +2 because the creation of user include creation of 2 default accouns
        $this->assertCount($numberOfAccount + 2, $response['accounts']);
    }
}
