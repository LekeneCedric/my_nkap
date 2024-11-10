<?php

namespace App\Operation\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\category\Infrastructure\Models\Category;
use App\Shared\Domain\VO\Id;
use App\User\Domain\Enums\UserTokenEnum;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MakeAIOperationActionTest extends TestCase
{
    use RefreshDatabase;
    const MAKE_AI_OPERATION = 'api/operation/ai/add';
    private User $user;
    private string $token;

    public function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value() . '@gmail.com',
            'name' => 'lekene',
            'password' => bcrypt('lekene@5144'),
            'profession_id' => (Profession::factory()->create())->id
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
    }

    public function test_can_make_ai_operation(): void
    {
        $initData = $this->buildSUT();

        $data = [
            'userId' => $this->user->uuid,
            'categories' => $initData['categories'],
            'accounts' => $initData['accounts'],
            'currentDate' => '2002-03-20',
            'message' => 'Today i have spent 2000 on food and 5000 on clothes and save 4000 for my families future.',
            'language' => 'en'
        ];

        $response = $this->postJson(self::MAKE_AI_OPERATION, $data, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $user = User::where('id', $this->user->id)->first();
        $operations = $response['operations'];
        $response->assertOk();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['operationIsOk']);
        $this->assertNotNull($response['operations']);
        $this->assertCount(3, $operations);
        // TODO: Should make test to verify if corresponding accountId is returned
        $this->assertNotEmpty($response['consumedToken']);
        $this->assertEquals($user->token, UserTokenEnum::DEFAULT_TOKEN - $response['consumedToken']);
    }

    private function buildSUT(): array
    {
        $account1 = Account::factory()->create([
            'name' => 'Cash',
            'user_id' => $this->user->id,
        ]);

        $account2 = Account::factory()->create([
            'name' => 'Saving',
            'user_id' => $this->user->id,
        ]);

        $foodCategory = Category::factory()->create([
            'name' => 'Food',
            'user_id' => $this->user->id,
            'uuid' => (new Id())->value(),
        ]);
        $clothesCategory = Category::factory()->create([
            'name' => 'Clothes',
            'user_id' => $this->user->id,
            'uuid' => (new Id())->value(),
        ]);

        return [
            'categories' => [
                [
                    'id' => $foodCategory->id,
                    'label' => $foodCategory->name
                ],
                [
                    'id' => $clothesCategory->id,
                    'label' => $clothesCategory->name
                ]
            ],
            'accounts' => [
                [
                    'id' => $account1->id,
                    'label' => $account1->name
                ],
                [
                    'id' => $account2->id,
                    'label' => $account2->name
                ]
            ]
        ];
    }
}
