<?php

namespace App\Subscription\Tests\E2E;

use App\Shared\Domain\VO\Id;
use App\Subscription\Infrastructure\Model\SubscriberSubscription;
use App\Subscription\Infrastructure\Model\Subscription;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SubscriptionActionTest extends TestCase
{
    use RefreshDatabase;
    const SUBSCRIPTION_URL = 'api/subscriptions/subscribe';

    protected function setUp(): void
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

    public function test_can_subscribe()
    {
        $initSUT = $this->buildSUT();

        $data = [
            'userId' => $this->user->uuid,
            'subscriptionId' => Subscription::factory()->create()->uuid,
            'nbMonth' => 12,
        ];

        $response = $this->postJson(self::SUBSCRIPTION_URL, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk();
        $this->assertTrue($response['isSubscribed']);
        $this->assertDatabaseHas('subscriber_subscriptions', [
           'user_id' => $this->user->id,
           'subscription_id' =>  $initSUT['subscription_id'],
        ]);
    }

    private function buildSUT(): array
    {
        $subscription = Subscription::factory()->create();
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
