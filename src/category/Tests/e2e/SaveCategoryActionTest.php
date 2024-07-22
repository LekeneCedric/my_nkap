<?php

namespace App\category\Tests\e2e;

use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SaveCategoryActionTest extends TestCase
{
    use RefreshDatabase;

    const SAVE_CATEGORY_ROUTE = 'api/category/save';
    private User $user;
    private string $token;

    public function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value().'@gmail.com',
            'name' => 'lek_',
            'password' => bcrypt('lek_@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
    }

    public function test_can_create_category()
    {
        $data = [
            'userId' => $this->user->uuid,
            'icon' => 'car',
            'name' => 'transport',
            'color' => 'green',
            'description' => 'Transport fees'
        ];

        $response = $this->postJson(self::SAVE_CATEGORY_ROUTE, $data,['Authorization' => 'Bearer '.$this->token]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isSaved']);
        $this->assertNotNull($response['message']);
        $this->assertNotNull($response['categoryId']);
    }

    public function test_can_update_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingCategory(
                user_id: $this->user->id,
                icon: 'car',
                name: 'transport',
                description: 'Transport fees',
            )->build();

        $data = [
            'userId' => $this->user->uuid,
            'categoryId' => $initSUT->category->uuid,
            'icon' => 'music',
            'name' => 'music',
            'color' => 'green',
            'description' => 'Music equipment fees'
        ];

        $response = $this->postJson(self::SAVE_CATEGORY_ROUTE, $data, ['Authorization' => 'Bearer '.$this->token]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isSaved']);
        $this->assertNotNull($response['message']);
        $this->assertNotNull($response['categoryId']);
        $this->assertEquals($response['categoryId'], $initSUT->category->uuid);
    }
}
