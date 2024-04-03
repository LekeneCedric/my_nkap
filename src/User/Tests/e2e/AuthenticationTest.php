<?php

namespace App\User\Tests\e2e;

use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    const REGISTER_USER = 'api/users/register';
    protected function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
    }

    public function test_can_register_user()
    {
        $initSUT = UserSUT::asSUT()
            ->build();

        $data = [
          'email' => 'lekene@gmail.com',
          'password' => 'Password1234',
          'username' => 'Lekene Cedric',
          'birthday' => '30-09-2002',
          'professionId' => $initSUT->profession->uuid,
        ];

        $response = $this->postJson(self::REGISTER_USER, $data);
        $createdUser = User::whereEmail('lekene_@gmail.com')->whereIsDeleted(false)
            ->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isCreated']);
        $this->assertNotNull($response['token']);
        $this->assertNotNull($createdUser);
    }

    public function test_can_throw_message_if_already_exist_user_with_same_email()
    {
        $email = 'lekene@gmail.com';
        $initSUT = UserSUT::asSUT()
            ->withExistingUser(email: $email)
            ->build();
        $data = [
            'email' => $email,
            'password' => 'Password1234',
            'username' => 'Lekene Cedric',
            'birthday' => '30-09-2002',
            'professionId' => $initSUT->profession->uuid,
        ];

        $response = $this->postJson(self::REGISTER_USER, $data);

        $this->assertFalse($response['status']);
        $this->assertNotEmpty($response['message']);
    }
}
