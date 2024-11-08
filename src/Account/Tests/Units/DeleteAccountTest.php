<?php

namespace App\Account\Tests\Units;

use App\Account\Application\Command\Delete\DeleteAccountHandler;
use App\Account\Application\Command\Delete\DeleteAccountResponse;
use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Domain\Repository\AccountRepository;
use App\Account\Tests\Units\Repositories\InMemoryAccountRepository;
use App\Subscription\Domain\Services\SubscriptionService;
use App\Subscription\Tests\Units\Services\InMemorySubscriptionService;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
{
    private AccountRepository $repository;
    private SubscriptionService $subscriptionService;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryAccountRepository();
        $this->subscriptionService = new InMemorySubscriptionService();
    }

    /**
     * @return void
     * @throws ErrorOnSaveAccountException
     * @throws NotFoundAccountException
     */
    public function test_can_delete_account()
    {
       $initData = AccountSUT::asSUT()
           ->withExistingAccount()
           ->build(
               $this->repository
           );
       $accountToDeleteId = $initData->account->id()->value();

       $response = $this->deleteAccount($accountToDeleteId);

       $this->assertTrue($response->isDeleted);
       $this->assertTrue($this->repository->account[$accountToDeleteId]->isDeleted());
    }

    /**
     * @return void
     * @throws ErrorOnSaveAccountException
     */
    public function test_can_throw_exception_if_want_to_delete_not_existing_account()
    {
        AccountSUT::asSUT()
            ->withExistingAccount()
            ->build(
                $this->repository
            );
        $accountToDeleteId = 'wrong_account_id';

        $this->expectException(NotFoundAccountException::class);

        $this->deleteAccount($accountToDeleteId);
    }

    /**
     * @param string $accountToDeleteId
     * @return DeleteAccountResponse
     * @throws ErrorOnSaveAccountException
     * @throws NotFoundAccountException
     */
    private function deleteAccount(string $accountToDeleteId): DeleteAccountResponse
    {
        $handler = new DeleteAccountHandler(
            repository: $this->repository,
            subscriptionService: $this->subscriptionService,
        );
        return $handler->handle($accountToDeleteId);
    }
}
