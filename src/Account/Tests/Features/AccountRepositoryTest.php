<?php

namespace App\Account\Tests\Features;

use App\Account\Domain\Repository\AccountRepository;
use App\Account\Infrastructure\Model\Account;
use App\Account\Infrastructure\Repository\PdoAccountRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\StringVO;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountRepositoryTest extends TestCase
{
    use RefreshDatabase;
    private AccountRepository $repository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PdoAccountRepository();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_can_create_account()
    {
        $initData = AccountSUT::asSUT()
            ->withExistingAccount();

        $account =  $initData->account;

        $this->repository->save($account);

        $createdAccountDb = Account::whereUuid($account->id()->value())->whereIsDeleted(false)->first();

        $this->assertNotNull($createdAccountDb);
    }

    /**
     * @return void
     * @throws Exception
     */
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

        $updatedAccountDb = Account::whereUuid($account->id()->value())->whereIsDeleted(false)->first();

        $this->assertNotNull($updatedAccountDb);
        $this->assertEquals(1000000, $updatedAccountDb['balance']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_can_delete_account()
    {
        $initData = AccountSUT::asSUT()
            ->withExistingAccount();
        $account = $initData->account;
        $accountId = $account->id()->value();
        $this->repository->save($account);

        $account->delete();

        $this->repository->save($account);

        $deletedAccount = Account::whereUuid($accountId)->whereIsDeleted(true)->first();

        $this->assertNotNull($deletedAccount);
        $this->assertEquals($deletedAccount->deleted_at, $account->deletedAt()->formatYMDHIS());
    }
}
