<?php

namespace App\Operation\Tests\e2e;

use App\Account\Tests\e2e\AccountSUT;
use App\category\Infrastructure\Models\Category;
use App\Operation\Domain\OperationTypeEnum;
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

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value() . '@gmail.com',
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
            'page' => 1,
            'limit' => 10
        ];

        $response = $this->postJson(self::FILTER_ACCOUNT_OPERATION, $data, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(10, $response['operations']);
    }

    public function test_can_filter_accounts_operations()
    {
        AccountSUT::asSUT()
            ->withExistingAccounts(count: 2, userId: $this->user->id)
            ->withExistingOperationsPerAccounts(count: 10)
            ->build();

        $data = [
            'userId' => $this->user->uuid,
            'page' => 1,
            'limit' => 5,
        ];

        $response = $this->postJson(self::FILTER_ACCOUNT_OPERATION, $data, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(5, $response['operations']);
        $this->assertEquals(20, $response['total']);
        $this->assertEquals(4, $response['numberOfPages']);
    }

    public function test_can_filter_operations_by_date()
    {
        $date = '2002-09-30';

        $nbAccount = 2;
        AccountSUT::asSUT()
            ->withExistingAccounts(count: $nbAccount, userId: $this->user->id)
            ->withExistingOperationsPerAccounts(
                count: 10,
                date: $date,
            )->withExistingOperationsPerAccounts(
                count: 10,
                date: '2023-09-30'
            )
            ->build();

        $data = [
            'userId' => $this->user->uuid,
            'date' => $date,
            'page' => 1,
            'limit' => 5,
        ];

        $response = $this->postJson(self::FILTER_ACCOUNT_OPERATION, $data, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(5, $response['operations']);
        $this->assertEquals(10 * $nbAccount, $response['total']);
        $this->assertEquals(4, $response['numberOfPages']);
    }

    public function test_can_filter_operations_by_category()
    {
        $nbAccount = 2;
        $category = Category::factory()->create();
        AccountSUT::asSUT()
            ->withExistingAccounts(count: $nbAccount, userId: $this->user->id)
            ->withExistingOperationsPerAccounts(
                count: 10,
                category_id: $category->id,
            )->withExistingOperationsPerAccounts(
                count: 10,
                category_id: Category::factory()->create()->id,
            )
            ->build();

        $data = [
            'userId' => $this->user->uuid,
            'categoryId' => $category->uuid,
            'page' => 1,
            'limit' => 5,
        ];

        $response = $this->postJson(self::FILTER_ACCOUNT_OPERATION, $data, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(5, $response['operations']);
        $this->assertEquals(10 * $nbAccount, $response['total']);
        $this->assertEquals(4, $response['numberOfPages']);
    }

    public function test_can_filter_operation_by_type()
    {
        AccountSUT::asSUT()
            ->withExistingAccounts(count: 1, userId: $this->user->id)
            ->withExistingOperationsPerAccounts(
                count: 10,
                operationType: OperationTypeEnum::INCOME
            )->withExistingOperationsPerAccounts(
                count: 10,
                operationType: OperationTypeEnum::EXPENSE
            )
            ->build();

        $data = [
            'userId' => $this->user->uuid,
            'operationType' => OperationTypeEnum::INCOME->value,
            'page' => 1,
            'limit' => 5,
        ];

        $response = $this->postJson(self::FILTER_ACCOUNT_OPERATION, $data, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount(5, $response['operations']);
        $this->assertEquals(10, $response['total']);
        $this->assertEquals(2, $response['numberOfPages']);
    }

    public function test_can_filter_operations_by_month()
    {
        AccountSUT::asSUT()
            ->withExistingAccounts(count: 1, userId: $this->user->id)
            ->withExistingOperationsPerAccounts(
                count: 5,
                date: '2002-09-05',
            )
            ->withExistingOperationsPerAccounts(
                count: 5,
                date: '2002-09-10',
                operationType: OperationTypeEnum::INCOME
            )
            ->withExistingOperationsPerAccounts(
                count: 5,
                date: '2002-09-13'
            )
            ->build();
        $data = [
            'userId' => $this->user->uuid,
            'page' => 1,
            'limit' => 100,
            'year' => 2002,
            'month' => 9
        ];

        $response = $this->postJson(self::FILTER_ACCOUNT_OPERATION, $data, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertOk();
    }

}
