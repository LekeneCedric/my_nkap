<?php

namespace App\Operation\Application\Command\MakeManyOperations;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Application\Command\MakeOperation\MakeOperationCommand;
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

class MakeManyOperationsHandler implements CommandHandler
{
    use StatisticsComposedIdBuilderTrait;

    public function __construct(
        private OperationAccountRepository $repository,
        private UserRepository $userRepository,
    )
    {
    }

    public function handle(MakeManyOperationsCommand|Command $command): mixed
    {
        $response = new MakeManyOperationResponse();
        $operationIds = [];
        try {
            foreach ($command->operations as $operationCommand) {
                $operationCommand->previousAmount = 0;
                $operationAccount = $this->getOperationAccountOrThrowNotFoundException($operationCommand->accountId);
                $operationAccount->makeOperation(
                    amount: new AmountVO($operationCommand->amount),
                    type: $operationCommand->type,
                    categoryId: new Id($operationCommand->categoryId),
                    detail: new StringVO($operationCommand->detail),
                    date: new DateVO($operationCommand->date),
                );

                $this->repository->saveOperation($operationAccount);
                $this->completCommandWithAdditionalInformation($operationCommand);
                $operationAccount->publishOperationSaved($operationCommand);
                $operationIds[] = $operationAccount->currentOperation()->id()->value();
            }
            $response->operationsSaved = true;
            $response->operationIds = $operationIds;
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
