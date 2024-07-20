<?php

namespace App\category\Tests\e2e;

use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetAllCategoriesActionTest extends TestCase
{
    use RefreshDatabase;

    const GET_ALL_CATEGORIES_ROUTE = 'api/category/all';
    private User $user;
    private string $token;

    public function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value() . '@gmail.com',
            'name' => 'lek_',
            'password' => bcrypt('lek_@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
    }

    public function test_can_get_all_categories()
    {
        CategorySUT::asSUT()
            ->withExistingCategory(
                user_id: $this->user->id,
                icon: 'car',
                name: 'transport',
                description: 'Transport fees'
            )->withExistingCategory(
                user_id: $this->user->id,
                icon: 'music',
                name: 'music',
                description: 'music equipments fees'
            )
            ->build();

        $response = $this->getJson(self::GET_ALL_CATEGORIES_ROUTE.'/'.$this->user->uuid, ['Authorization' => 'Bearer '.$this->token]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(2, $response['categories']);
    }
}
