<?php

namespace App\Account\Application\Command\Save;

use App\Account\Domain\Account;
use App\Account\Domain\Enums\AccountMessagesEnum;
use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Domain\Repository\AccountRepository;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\Subscription\Domain\Services\SubscriptionService;

class SaveAccountHandler
{
    public function __construct(
        private readonly AccountRepository $repository,
        private readonly SubscriptionService $subscriptionService,
    )
    {
    }

    /**
     * @param SaveAccountCommand $command
     * @return SaveAccountResponse
     * @throws NotFoundAccountException
     * @throws ErrorOnSaveAccountException
     */
    public function handle(SaveAccountCommand $command): SaveAccountResponse
    {
        $response = new SaveAccountResponse();

        $account = $this->getAccountOrThrowExceptionIfNotExist($command);
        if($command->accountId) {
            $account->update(
                name: new StringVO($command->name),
                type: new StringVO($command->type),
                icon: new StringVO($command->icon),
                color: new StringVO($command->color),
                balance: new AmountVO($command->balance),
                isIncludeInTotalBalance: $command->isIncludeInTotalBalance,
            );
        }
        if (!$command->accountId) {
            $this->subscriptionService->updateNbAccounts(userId: $command->userId, count: -1);
        }
        $this->repository->save($account);

        $response->status = true;
        $response->isSaved = true;
        $response->accountId = $account->id()->value();
        $response->message = AccountMessagesEnum::CREATED;
        if ($command->accountId) {
            $response->message = AccountMessagesEnum::UPDATED;
        }
        return $response;
    }

    /**
     * @param SaveAccountCommand $command
     * @return Account
     * @throws NotFoundAccountException
     */
    private function getAccountOrThrowExceptionIfNotExist(SaveAccountCommand $command): Account
    {
        if ($command->accountId) {
           $account = $this->repository->byId(new Id($command->accountId));
           if (!$account) {
               throw new NotFoundAccountException(AccountMessagesEnum::NOT_FOUND);
           }
           return $account;
        }
        return $this->createAccount($command);
    }

    private function createAccount(SaveAccountCommand $command): Account
    {
        return Account::create(
            userId: new Id($command->userId),
            name: new StringVO($command->name),
            type: new StringVO($command->type),
            icon: new StringVO($command->icon),
            color: new StringVO($command->color),
            balance: new AmountVO($command->balance),
            isIncludeInTotalBalance: $command->isIncludeInTotalBalance,
        );
    }
}
