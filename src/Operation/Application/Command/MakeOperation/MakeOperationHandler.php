<?php

namespace App\Operation\Application\Command\MakeOperation;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Shared\Domain\Command\Command;
use App\Shared\Domain\Command\CommandHandler;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use App\User\Domain\Repository\UserRepository;
use Exception;

class MakeOperationHandler implements CommandHandler
{
    use StatisticsComposedIdBuilderTrait;

    public function __construct(
        private OperationAccountRepository $repository,
        private UserRepository $userRepository,
    )
    {
    }

    /**
     * @param MakeOperationCommand|Command $command
     * @return makeOperationResponse
     */
    public function handle(MakeOperationCommand|Command $command): makeOperationResponse
    {
        $response = new makeOperationResponse();

        try {
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
            $this->completCommandWithAdditionalInformation(
              $command
            );
            $operationAccount->publishOperationSaved($command);

            $response->operationSaved = true;
            $response->operationId = $operationAccount->currentOperation()->id()->value();
        } catch (NotFoundAccountException|Exception $e) {
            $response->message = $e->getMessage();
        }

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

    private function completCommandWithAdditionalInformation(MakeOperationCommand|Command &$command): void
    {
        list($year, $month) = [(new DateVO($command->date))->year(), (new DateVO($command->date))->month()];
        $userId = $this->userRepository->userId();
        $command->userId = $userId;
        $command->year = $year;
        $command->month = $month;
        $command->monthlyStatsComposedId = $this->buildMonthlyStatisticsComposedId(month: $month, year: $year, userId: $userId);
        $command->monthlyStatsByCategoryComposedId = $this->buildMonthlyCategoryStatisticsComposedId(month: $month, year: $year, userId: $userId, categoryId: $command->categoryId);
    }
}
