<?php

namespace App\Account\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use App\Subscription\Infrastructure\Model\SubscriberSubscription;
use App\Subscription\Infrastructure\Model\Subscription;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SaveAccountActionTest extends TestCase
{
    use RefreshDatabase;

    const SAVE_ACCOUNT_ROUTE = 'api/accounts/save';
    const LOGIN = 'api/users/login';
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

    public function buildSUT(int $nbAccounts = 2): void
    {
        $subscription = Subscription::factory()->create([
            'name' => SubscriptionPlansEnum::FREE_PLAN->value,
            'nb_accounts' => $nbAccounts,
        ]);
        SubscriberSubscription::factory()->create([
           'user_id' => $this->user->id,
           'subscription_id' => $subscription->id,
           'nb_accounts' => $nbAccounts,
        ]);
    }
    public function test_can_create_account()
    {
        $this->buildSUT();
        $data = [
           'userId' => $this->user->uuid,
           'name' => 'compte epargne',
           'type' => 'epargne',
           'icon' => 'wallet_icon',
           'color' => 'green',
           'balance' => 20000,
           'isIncludeInTotalBalance' => true,
        ];

        $response = $this->postJson(self::SAVE_ACCOUNT_ROUTE, $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $createdAccount = Account::whereUuid($response['accountId'])->whereIsDeleted(false)->first();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['isSaved']);
        $this->assertNotNull($response['accountId']);
        $this->assertNotNull($createdAccount);
    }

    /**
     * @throws Exception
     */
    public function test_can_update_account()
    {
        $this->buildSUT();
        $initSUT = AccountSUT::asSUT()->withExistingAccounts(1)->build();
        $accountId = $initSUT->accounts[0]->uuid;

        $updatedData = [
            'userId' => $this->user->uuid,
            'accountId' => $accountId,
            'name' => 'compte epargne',
            'type' => 'epargne',
            'icon' => 'wallet_icon',
            'color' => 'green',
            'balance' => 20000,
            'isIncludeInTotalBalance' => true,
        ];

        $response = $this->post(self::SAVE_ACCOUNT_ROUTE, $updatedData,[
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $updatedAccount = Account::whereUuid($response['accountId'])->whereIsDeleted(false)->first();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isSaved']);
        $this->assertNotNull($response['accountId']);
        $this->assertEquals((new DateVO())->formatYMDHIS(), $updatedAccount->updated_at);
    }

    public function test_can_throw_message_if_invalid_request_params_when_create_account()
    {
        $this->buildSUT();
        $data = [
            'name' => 'compte epargne',
            'type' => 'epargne',
            'icon' => 'wallet_icon',
            'color' => 'green',
        ];

        $response = $this->post(self::SAVE_ACCOUNT_ROUTE, $data,[
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $this->assertFalse($response['status']);
        $this->assertNotNull($response['message']);
    }

    public function test_can_update_subscription_nb_accounts_when_create_account()
    {
        $this->buildSUT();
        $data = [
            'userId' => $this->user->uuid,
            'name' => 'compte epargne',
            'type' => 'epargne',
            'icon' => 'wallet_icon',
            'color' => 'green',
            'balance' => 20000,
            'isIncludeInTotalBalance' => true,
        ];

        $this->postJson(self::SAVE_ACCOUNT_ROUTE, $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $this->assertDatabaseHas('subscriber_subscriptions', [
            'user_id' => $this->user->id,
            'nb_accounts' => 1,
        ]);
    }

    public function test_cannot_save_account_when_current_subscription_not_permit()
    {
        $this->buildSUT(nbAccounts: 0);
        $data = [
            'userId' => $this->user->uuid,
            'name' => 'compte epargne',
            'type' => 'epargne',
            'icon' => 'wallet_icon',
            'color' => 'green',
            'balance' => 20000,
            'isIncludeInTotalBalance' => true,
        ];

        $response = $this->postJson(self::SAVE_ACCOUNT_ROUTE, $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $this->assertFalse($response['status']);
        $this->assertFalse($response['isSaved']);
    }
}
