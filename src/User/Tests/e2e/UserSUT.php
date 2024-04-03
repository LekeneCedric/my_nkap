<?php

namespace App\User\Tests\e2e;

use App\Shared\VO\Id;
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

    public function withExistingUser(string $email): static
    {
        User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => $email,
            'name' => 'lekene',
            'password' => 'lekene@455',
            'profession_id' => $this->profession->id,
        ]);
        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}
