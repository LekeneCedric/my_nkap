<?php

namespace App\User\Tests\e2e;

use App\Shared\Domain\VO\Id;
use App\User\Domain\Enums\UserStatusEnum;
use App\User\Domain\VO\VerificationCodeVO;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;

class UserSUT
{
    public Profession $profession;
    public static function asSUT(): UserSUT
    {
        $self = new self();
        $self->profession = Profession::factory()->create([
            'name' => 'Développeur'
        ]);
        return $self;
    }

    public function withExistingUser(
        string $email,
        string $password = 'lekene@5134',
        ?UserStatusEnum $status = UserStatusEnum::ACTIVE,
        VerificationCodeVO $verificationCode = null,
    ): static
    {
        $userData = [
            'uuid' => (new Id())->value(),
            'email' => $email,
            'name' => 'lekene',
            'password' => bcrypt($password),
            'profession_id' => $this->profession->id,
            'status' => $status->value,
        ];
        if ($verificationCode) {
            $userData['verification_code'] = $verificationCode->verificationCode();
            $userData['verification_code_exp'] = $verificationCode->expirationTime();
        }
        User::factory()->create($userData);
        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}
