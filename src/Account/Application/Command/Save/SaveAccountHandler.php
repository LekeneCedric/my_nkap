<?php

namespace App\Account\Application\Command\Save;

use App\Account\Domain\Account;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Domain\Repository\AccountRepository;
use App\Shared\VO\AmountVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;

class SaveAccountHandler
{
    public function __construct(
        private readonly AccountRepository $repository,
    )
    {
    }

    /**
     * @param SaveAccountCommand $command
     * @return SaveAccountResponse
     * @throws NotFoundAccountException
     */
    public function handle(SaveAccountCommand $command): SaveAccountResponse
    {
        $response = new SaveAccountResponse();

        $account = $this->getAccountOrThrowExceptionIfNotExist($command);
        if($command->accountId) {
            $account->update(
                name: new StringVO($command->name),
                type: new StringVO($command->type),
                icon: new StringVO($command->type),
                color: new StringVO($command->color),
                balance: new AmountVO($command->balance),
                isIncludeInTotalBalance: $command->isIncludeInTotalBalance,
            );
        }
        $this->repository->save($account);

        $response->status = true;
        $response->isSaved = true;
        $response->accountId = $account->id()->value();
        $response->message = 'Compte créer avec succès !';
        if ($command->accountId) {
            $response->message = 'Informations compte modifiés avec succès !';
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
               throw new NotFoundAccountException("Le compte sélectionné n'existe pas !");
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
