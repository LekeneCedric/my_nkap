<?php

namespace App\Operation\Application\Command\DeleteOperation;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Shared\Domain\VO\Id;

class DeleteOperationHandler
{
    public function __construct(
        private OperationAccountRepository $repository,
    )
    {
    }

    /**
     * @param DeleteOperationCommand $command
     * @return DeleteOperationResponse
     * @throws NotFoundAccountException
     * @throws NotFoundOperationException
     * @throws \Exception
     */
    public function handle(DeleteOperationCommand $command): DeleteOperationResponse
    {
        $operationAccount = $this->getOperationAccountOrThrowException($command);
        $operationAccount->deleteOperation(new Id($command->operationId));

        $this->repository->saveOperation($operationAccount);

        return new DeleteOperationResponse(
            message: 'Operation supprimée avec succès !',
            isDeleted: true,
            operationAmount: $operationAccount->currentOperation()->amount()->value(),
            date: $operationAccount->currentOperation()->date()->formatYMDHIS(),
            operationType: $operationAccount->currentOperation()->type(),
        );
    }

    /**
     * @param DeleteOperationCommand $command
     * @return operationAccount
     * @throws NotFoundAccountException
     */
    private function getOperationAccountOrThrowException(DeleteOperationCommand $command): operationAccount
    {
        $accountId = $command->accountId;
        $account = $this->repository->byId(new Id($accountId));
        if (!$account) {
            throw new NotFoundAccountException("Le compte sélectionné n'existe pas !");
        }
        return $account;
    }
}
