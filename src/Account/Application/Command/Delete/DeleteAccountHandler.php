<?php

namespace App\Account\Application\Command\Delete;

use App\Account\Domain\Enums\AccountMessagesEnum;
use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Domain\Repository\AccountRepository;
use App\Shared\Domain\VO\Id;

class DeleteAccountHandler
{

    public function __construct(
        private readonly AccountRepository $repository,
    )
    {
    }

    /**
     * @param string $accountToDeleteId
     * @return DeleteAccountResponse
     * @throws NotFoundAccountException
     * @throws ErrorOnSaveAccountException
     */
    public function handle(string $accountToDeleteId): DeleteAccountResponse
    {
        $response = new DeleteAccountResponse();

        $account = $this->repository->byId(new Id($accountToDeleteId));
        if (!$account) {
            throw new NotFoundAccountException(AccountMessagesEnum::NOT_FOUND);
        }
        $account->delete();
        $this->repository->save($account);
        $response->message = AccountMessagesEnum::DELETED;
        $response->isDeleted = true;

        return $response;
    }
}
