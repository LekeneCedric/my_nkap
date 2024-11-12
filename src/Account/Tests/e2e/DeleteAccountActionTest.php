<?php

namespace App\Account\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\Shared\Domain\VO\Id;
use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use App\Subscription\Infrastructure\Model\SubscriberSubscription;
use App\Subscription\Infrastructure\Model\Subscription;
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
        $this->buildSUT();
    }

    public function buildSUT(): void
    {
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value().'@gmail.com',
            'name' => 'lekene',
            'password' => bcrypt('lekene@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
        $subscription = Subscription::factory()->create([
            'name' => SubscriptionPlansEnum::FREE_PLAN->value,
            'nb_accounts' => 2,
        ]);
        SubscriberSubscription::factory()->create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'nb_accounts' => 1,
        ]);
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

    public function test_can_update_subscription_nb_accounts_after_delete_account()
    {
        $accountId = (Account::factory()->create(['user_id' => $this->user->id, 'icon' => 'icon']))->uuid;

        $data = [
            'accountId' => $accountId,
        ];

        $this->post(self::DELETE_ACCOUNT_ROUTE, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $this->assertDatabaseHas('subscriber_subscriptions', [
            'user_id' => $this->user->id,
            'nb_accounts' => 2,
        ]);
    }
}
