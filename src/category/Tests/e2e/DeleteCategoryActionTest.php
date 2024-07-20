<?php

namespace App\category\Tests\e2e;

use App\category\Infrastructure\Models\Category;
use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeleteCategoryActionTest extends TestCase
{
    use RefreshDatabase;

    const DELETE_CATEGORY_ROUTE = 'api/category/delete';
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

    public function test_can_delete_category()
    {
        $initSUT = CategorySUT::asSUT()
            ->withExistingCategory(
                user_id: $this->user->id,
                icon: 'football',
                name: 'football equipement',
                description: 'Equipement for football'
            )->build();

        $data = [
            'userId' => $this->user->uuid,
            'categoryId' => $initSUT->category->uuid,
        ];
        $response = $this->postJson(self::DELETE_CATEGORY_ROUTE, $data, ['Authorization' => 'Bearer '.$this->token]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isDeleted']);
        $this->assertEmpty(Category::whereUuid($initSUT->category->uuid)->first());
    }
}
