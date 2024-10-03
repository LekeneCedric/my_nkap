<?php

namespace App\User\Tests\e2e;

use App\Shared\Domain\VO\Id;
use App\User\Domain\VO\VerificationCodeVO;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecoverPasswordActionTest extends TestCase
{
    use RefreshDatabase;

    const RECOVER_PASSWORD = 'api/users/recover-password';
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
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
    }

    public function test_can_recover_password()
    {
        $code = new VerificationCodeVO();
        $this->buildSUT($code);
        $data = [
            'code' => $code->verificationCode(),
            'email' => $this->user->email,
            'password' => 'new-passsowrd@237'
        ];

        $response = $this->postJson(self::RECOVER_PASSWORD, $data);

        $this->assertTrue($response['status']);
        $this->assertTrue($response['passwordReset']);
    }

    private function buildSUT(VerificationCodeVO $code)
    {
        User::where('email', $this->user->email)->update([
            'verification_code' => $code->verificationCode(),
            'verification_code_exp' => $code->expirationTime(),
        ]);
    }

}
