<?php

namespace App\Operation\Application\Command\MakeOperation;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;

class MakeOperationHandler
{
    public function __construct(
        private OperationAccountRepository $repository,
    )
    {
    }

    /**
     * @param MakeOperationCommand $command
     * @return makeOperationResponse
     * @throws NotFoundAccountException
     * @throws OperationGreaterThanAccountBalanceException
     */
    public function handle(MakeOperationCommand $command): makeOperationResponse
    {
        $response = new makeOperationResponse();

        $operationAccount = $this->getOperationAccountOrThrowNotFoundException($command->accountId);
        if ($command->operationId) {
            $response->previousOperationAmount = $operationAccount->operation($command->operationId)->amount()->value();
            $operationAccount->updateOperation(
                operationId: new Id($command->operationId),
                amount: new AmountVO($command->amount),
                type: $command->type,
                categoryId: new Id($command->categoryId),
                detail: new StringVO($command->detail),
                date: new DateVO($command->date),
            );
        }
        if (!$command->operationId) {
            $operationAccount->makeOperation(
                amount: new AmountVO($command->amount),
                type: $command->type,
                categoryId: new Id($command->categoryId),
                detail: new StringVO($command->detail),
                date: new DateVO($command->date)
            );
        }

        $this->repository->saveOperation($operationAccount);

        $currentOperation = $operationAccount->currentOperation();

        $response->operationSaved = true;
        $response->operationId = $currentOperation->id()->value();

        return $response;
    }

    /**
     * @param string $accountId
     * @return operationAccount|null
     * @throws NotFoundAccountException
     */
    private function getOperationAccountOrThrowNotFoundException(string $accountId): ?operationAccount
    {
        $account = $this->repository->byId(new Id($accountId));
        if (!$account) {
            throw new NotFoundAccountException("Le compte sélectionné n'existe pas !");
        }
        return $account;
    }
}
