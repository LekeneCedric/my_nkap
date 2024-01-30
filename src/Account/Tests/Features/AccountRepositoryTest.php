<?php

namespace App\Account\Tests\Features;

use App\Account\Domain\Repository\AccountRepository;
use App\Account\Infrastructure\Models\AccountModel;
use App\Account\Infrastructure\Repository\PdoAccountRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\StringVO;
use Exception;
use Tests\TestCase;

class AccountRepositoryTest extends TestCase
{
    private AccountRepository $repository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PdoAccountRepository();
    }

    /**
     * @throws Exception
     */
    public function test_can_create_account()
    {
        $initData = AccountSUT::asSUT()
            ->withExistingAccount();

        $account =  $initData->account;

        $this->repository->save($account);

        $createdAccountDb = AccountModel::whereUuid($account->id()->value())->whereIsDeleted(false)->first();

        $this->assertNotNull($createdAccountDb);
    }

    public function test_can_update_account()
    {
        $initData = AccountSUT::asSUT()
            ->withExistingAccount();

        $account =  $initData->account;

        $this->repository->save($account);

        $account->update(
            name: new StringVO('gains'),
            type: new StringVO('epargne'),
            icon: new StringVO('gain_icon'),
            color: new StringVO('color_name'),
            balance: new AmountVO(1000000),
            isIncludeInTotalBalance: true
        );

        $this->repository->save($account);

        $updatedAccountDb = AccountModel::whereUuid($account->id()->value())->whereIsDeleted(false)->first();

        $this->assertNotNull($updatedAccountDb);
        $this->assertEquals(1000000, $updatedAccountDb['balance']);
    }
}
