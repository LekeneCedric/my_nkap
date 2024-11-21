<?php

namespace App\Subscription\Tests\E2E;

use App\Shared\Domain\VO\Id;
use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use App\Subscription\Infrastructure\Model\SubscriberSubscription;
use App\Subscription\Infrastructure\Model\Subscription;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetAllSubscriptionsActionTest extends TestCase
{
    use RefreshDatabase;

    const GET_ALL_SUBSCRIPTION_URL = 'api/subscriptions/all';

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

    public function test_can_get_all_subscriptions_for_user()
    {
        $this->buildSUT();

        $data = [
            'userId' => $this->user->uuid,
        ];

        $response = $this->postJson(self::GET_ALL_SUBSCRIPTION_URL, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->dd();
        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertNotEmpty($response['subscriptions']);
    }

    private function buildSUT(): array
    {
        $subscription = Subscription::factory()->create();
        Subscription::factory()->create([
            'name' => SubscriptionPlansEnum::STANDARD_PLAN->name
        ]);
        SubscriberSubscription::factory()->create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
        ]);

        return [
            'subscriptionId' => $subscription->uuid,
            'subscription_id' => $subscription->id,
        ];
    }
}
