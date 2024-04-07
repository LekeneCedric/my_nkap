<?php

namespace App\Account\Application\Command\Delete;

use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Domain\Repository\AccountRepository;
use App\Shared\VO\Id;

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
            throw new NotFoundAccountException("Impossible de supprimer ce compte , reessayez plus-tard !");
        }
        $account->delete();

        $this->repository->save($account);

        $response->isDeleted = true;

        return $response;
    }
}
