<?php

namespace App\Account\Tests\Features;

use App\Account\Domain\Account;
use App\Shared\VO\AmountVO;
use App\Shared\VO\StringVO;

class AccountSUT
{
    public Account $account;
    public static function asSUT(): AccountSUT
    {
        return new self();
    }

    public function withExistingAccount(): static
    {
        $this->account = Account::create(
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
