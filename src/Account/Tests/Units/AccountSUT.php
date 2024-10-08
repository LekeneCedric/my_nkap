<?php

namespace App\Account\Tests\Units;

use App\Account\Domain\Account;
use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Account\Domain\Repository\AccountRepository;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;

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
            userId: new Id(),
            name: new StringVO("epargne"),
            type: new StringVO('epargne-maison'),
            icon: new StringVO('icon_name'),
            color: new StringVO('color_name'),
            balance: new AmountVO(5000),
            isIncludeInTotalBalance: true
        );
        return $this;
    }

    /**
     * @param AccountRepository $repository
     * @return $this
     * @throws ErrorOnSaveAccountException
     */
    public function build(AccountRepository $repository): static
    {
        $repository->save($this->account);
        return $this;
    }


}
