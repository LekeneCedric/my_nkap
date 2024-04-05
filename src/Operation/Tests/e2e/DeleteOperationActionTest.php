<?php

namespace App\Operation\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Tests\TestCase;

class DeleteOperationActionTest extends TestCase
{

    const DELETE_OPERATION = 'api/operation/delete';
    private User $user;
    private string $token;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value().'@gmail.com',
            'name' => 'lekene',
            'password' => bcrypt('lekene@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
    }

    public function test_can_delete_operation_account(): void
    {
        $initSUT = $this->buildSUT();

        $data = [
            'accountId' => $initSUT['accountId'],
            'operationId' => $initSUT['operationId'],
        ];

        $response = $this->postJson(self::DELETE_OPERATION, $data, [
            'Authorization' => 'Bearer '.$this->token,
        ]);
        $updatedAccount = Account::whereUuid($initSUT['accountId'])->whereIsDeleted(false)->first();
        $deletedOperation = Operation::whereUuid($initSUT['operationId'])->whereIsDeleted(true)->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isDeleted']);
        $this->assertEquals(0, $updatedAccount->balance);
        $this->assertNotNull($deletedOperation);
    }

    private function buildSUT(): array
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 2500000
        ]);
        $operation = Operation::factory()->create([
            'account_id' => $account->getAttribute('id'),
            'amount' => 2500000,
            'type' => OperationTypeEnum::INCOME
        ]);

        return [
            'accountId' => $account->getAttribute('uuid'),
            'operationId' => $operation->getAttribute('uuid')
        ];
    }
}
