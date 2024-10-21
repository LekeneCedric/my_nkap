<?php

namespace App\User\Tests\e2e;

use App\User\Domain\Enums\UserStatusEnum;
use App\User\Domain\VO\VerificationCodeVO;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    const VERIFICATION_ACCOUNT = 'api/users/verification-account';

    const REGISTER_USER = 'api/users/register';

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
        $createdUser = User::whereEmail('lekene@gmail.com')->whereIsDeleted(false)
            ->first();

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isCreated']);
        $this->assertNotNull($response['token']);
        $this->assertNotNull($response['user']);
        $this->assertEquals(UserStatusEnum::PENDING->value, $createdUser->status);
        $this->assertNotNull($createdUser->verification_code);
        $this->assertNotNull($createdUser);
        $this->assertEquals($response['aiToken'], $createdUser->token);
    }

    public function test_can_activate_account_after_registration()
    {
        $userEmail = 'lekene@gmail.com';
        $verificationCode = new VerificationCodeVO();
        UserSUT::asSUT()
            ->withExistingUser(
                email: $userEmail,
                status: UserStatusEnum::PENDING,
                verificationCode: $verificationCode,
            )
            ->build();

        $data = [
          'email' => $userEmail,
          'code' =>  $verificationCode->verificationCode(),
        ];

        $response = $this->postJson(self::VERIFICATION_ACCOUNT, $data);
        $updatedUser = User::where('email', $userEmail)->first();

        $response->assertOk();
        $this->assertEquals(UserStatusEnum::ACTIVE->value, $updatedUser->status);
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

    public function test_can_login_user()
    {
        $email = 'leken@gmail.com';
        $password = 'leken@1435';
        UserSUT::asSUT()
            ->withExistingUser(email: $email, password: $password)
            ->build();
        $data = [
            'email' => $email,
            'password' => $password
        ];

        $response = $this->postJson('api/users/login', $data);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertTrue($response['isLogged']);
        $this->assertNotNull($response['user']);
        $this->assertNotNull($response['token']);
    }

    public function test_can_throw_error_message_if_incorrect_credentials()
    {
        $email = 'leken@gmail.com';
        $password = 'leken@1435';
        UserSUT::asSUT()
            ->withExistingUser(email: $email, password: $password)
            ->build();
        $data = [
            'email' => $email,
            'password' => 'wrong_password'
        ];

        $response = $this->postJson('api/users/login', $data);

        $response->assertOk();
        $this->assertFalse($response['status']);
        $this->assertFalse($response['isLogged']);
        $this->assertNotNull($response['message']);
    }

    public function test_can_logout()
    {
        $email = 'leken@gmail.com';
        $password = 'leken@1435';
        UserSUT::asSUT()
            ->withExistingUser(email: $email, password: $password)
            ->build();

        $token =  $this->postJson('api/users/login', [
            'email' => $email,
            'password' => $password
        ])['token'];

       $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->postJson('api/users/logout');
       Artisan::call('cache:clear');
       $response = $this->withHeaders([
           'Authorization' => 'Bearer '.$token
       ])->postJson('api/users/logout');

       $this->assertNotNull($response['message']);
    }
}
