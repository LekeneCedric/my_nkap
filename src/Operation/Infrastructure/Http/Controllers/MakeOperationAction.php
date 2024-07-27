<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalCommand;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalHandler;
use App\Operation\Application\Command\MakeOperation\MakeOperationHandler;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Infrastructure\Factories\MakeOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\MakeOperationRequest;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use App\Statistics\Application\Command\UpdateMonthlyCategoryStatistics\UpdateMonthlyCategoryStatisticsCommand;
use App\Statistics\Application\Command\UpdateMonthlyCategoryStatistics\UpdateMonthlyCategoryStatisticsHandler;
use App\Statistics\Application\Command\UpdateMonthlyStatistics\UpdateMonthlyStatisticsCommand;
use App\Statistics\Application\Command\UpdateMonthlyStatistics\UpdateMonthlyStatisticsHandler;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class MakeOperationAction
{
    use StatisticsComposedIdBuilderTrait;
    public function __invoke(
        MakeOperationHandler           $handler,
        UpdateFinancialGoalHandler     $updateFinancialGoalHandler,
        UpdateMonthlyStatisticsHandler $updateMonthlyStatisticsHandler,
        UpdateMonthlyCategoryStatisticsHandler $updateMonthlyCategoryStatisticsHandler,
        MakeOperationRequest           $request,
        OperationsLogger               $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false, 'operationIsSaved' => false];

        try {
            $command = MakeOperationCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);

            $updateFinancialGoalCommand = new UpdateFinancialGoalCommand(
                accountId: $command->accountId,
                previousAmount: $response->previousOperationAmount,
                newAmount: $command->amount,
                operationDate: $command->date,
                type: $command->type
            );
            $updateFinancialGoalHandler->handle($updateFinancialGoalCommand);

            $userId = auth()->user()->uuid;
            list($year, $month) = [(new DateVO($command->date))->year(), (new DateVO($command->date))->month()];
            $updateMonthlyStatisticsCommand = new UpdateMonthlyStatisticsCommand(
                composedId: $this->buildMonthlyStatisticsComposedId(month: $month, year: $year, userId: $userId),
                userId: $userId,
                year: $year,
                month: $month,
                previousAmount: $response->previousOperationAmount,
                newAmount: $command->amount,
                operationType: $command->type,
            );
            $updateMonthlyStatisticsHandler->handle($updateMonthlyStatisticsCommand);

            $updateMonthlyCategoryStatisticCommand = new UpdateMonthlyCategoryStatisticsCommand(
                composedId: $this->buildMonthlyCategoryStatisticsComposedId(
                    month: $month,
                    year: $year,
                    userId: $userId,
                    categoryId: $command->categoryId,
                ),
                userId: $userId,
                year: $year,
                month: $month,
                previousAmount: $response->previousOperationAmount,
                newAmount: $command->amount,
                operationType: $command->type,
                categoryId: $command->categoryId,
            );
            $updateMonthlyCategoryStatisticsHandler->handle($updateMonthlyCategoryStatisticCommand);

            $httpJson = [
                'status' => true,
                'operationSaved' => $response->operationSaved,
                'operationId' => $response->operationId,
            ];
        } catch (
            NotFoundAccountException|
            OperationGreaterThanAccountBalanceException $e
        ) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: $e,
            );
            $httpJson['message'] = $e->getMessage();
        } catch (Exception $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ERROR,
                description: $e,
            );
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête , veuillez réessayer ultérieurement !';
        }

        return response()->json($httpJson);
    }
}
