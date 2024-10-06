<?php

namespace App\User\Tests\e2e;

use App\Shared\Domain\VO\Id;
use App\User\Domain\Enums\UserStatusEnum;
use App\User\Infrastructure\Mails\EmailCodeVerificationMail;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendRecoverPasswordCodeActionTest extends TestCase
{
    use RefreshDatabase;

    const SEND_RECOVER_PASSWORD_CODE = 'api/users/send-recover-password-code';
    private User $user;
    private string $token;
    public function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
        $this->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value().'@gmail.com',
            'name' => 'lekene',
            'password' => bcrypt('lekene@5144'),
            'profession_id' => (Profession::factory()->create())->id,
            'status' => UserStatusEnum::ACTIVE->value,
        ]);
        $this->token = $this->user->createToken('my_nkap_token')->plainTextToken;
    }

    public function test_can_send_recover_password_code()
    {
        Mail::fake();

        $data = [
            'email' => $this->user->email,
        ];

        $this->postJson(self::SEND_RECOVER_PASSWORD_CODE, $data);

        Mail::assertSent(EmailCodeVerificationMail::class, function ($mail) use ($data) {
            // Assert that the email is sent to the correct user
            return $mail->hasTo($data['email']);
        });
    }
}
