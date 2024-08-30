<?php

namespace App\Operation\Application\Command\DeleteOperation;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\Command\Command;
use App\Shared\Domain\Command\CommandHandler;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use Exception;

class DeleteOperationHandler implements CommandHandler
{
    use StatisticsComposedIdBuilderTrait;

    public function __construct(
        private OperationAccountRepository $repository,
    )
    {
    }

    /**
     * @param DeleteOperationCommand|Command $command
     * @return DeleteOperationResponse
     */
    public function handle(DeleteOperationCommand|Command $command): DeleteOperationResponse
    {
        $response = new DeleteOperationResponse();
        try {
            $operationAccount = $this->getOperationAccountOrThrowException($command);
            $operationAccount->deleteOperation(new Id($command->operationId));

            $this->repository->saveOperation($operationAccount);

            $this->completeCommandWithAdditionalInformations(
                $command,
                $operationAccount->currentOperation()->amount()->value(),
                $operationAccount->currentOperation()->date()->formatYMDHIS(),
                $operationAccount->currentOperation()->type(),
                $operationAccount->currentOperation()->categoryId()->value(),
            );
            $operationAccount->publishOperationDeleted($command);

            $response->message = 'Operation supprimée avec succès !';
            $response->isDeleted = true;
        } catch (
        NotFoundAccountException|
        NotFoundOperationException $e
        ) {
            $response->message = $e->getMessage();
        } catch (Exception) {
            $response->message = 'Une erreur est survenue lors de la suppression de l\'opération';
        }
        return $response;
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

    private function completeCommandWithAdditionalInformations(
        DeleteOperationCommand|Command &$command,
        float $amount,
        string $date,
        OperationTypeEnum $type,
        string $categoryId
    ): void
    {
        list($year, $month) = [(new DateVO($command->date))->year(), (new DateVO($command->date))->month()];
        $userId = auth()->user()->uuid;
        $command->userId = $userId;
        $command->year = $year;
        $command->month = $month;
        $command->categoryId = $categoryId;
        $command->previousAmount = $amount;
        $command->newAmount = $amount;
        $command->date = $date;
        $command->type = $type;
        $command->isDeleted = true;
        $command->monthlyStatisticsComposedId = $this->buildMonthlyStatisticsComposedId(month: $month, year: $year, userId: $userId);
        $command->monthlyStatisticsByCategoryComposedId = $this->buildMonthlyCategoryStatisticsComposedId(month: $month, year: $year, userId: $userId, categoryId: $command->categoryId);
    }
}
