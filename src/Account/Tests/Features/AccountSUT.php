<?php

namespace App\Account\Tests\Features;

use App\Account\Domain\Account;
use App\Shared\VO\AmountVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;

class AccountSUT
{
    public Account $account;
    public User $user;
    public static function asSUT(): AccountSUT
    {
        $self = new self();
        $self->user  = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value().'@gmail.com',
            'name' => 'leke',
            'password' => bcrypt('leke@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        return $self;
    }

    public function withExistingAccount(): static
    {
        $this->account = Account::create(
            userId: new Id($this->user->uuid),
            name: new StringVO("epargne"),
            type: new StringVO('epargne-maison'),
            icon: new StringVO('icon_name'),
            color: new StringVO('color_name'),
            balance: new AmountVO(5000),
            isIncludeInTotalBalance: true
        );
        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}
