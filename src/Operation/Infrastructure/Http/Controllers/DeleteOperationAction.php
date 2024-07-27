<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalCommand;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalHandler;
use App\Operation\Application\Command\DeleteOperation\DeleteOperationHandler;
use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Infrastructure\Factories\DeleteOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\DeleteOperationRequest;
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

class DeleteOperationAction
{
    use StatisticsComposedIdBuilderTrait;
    public function __invoke(
        DeleteOperationHandler                 $handler,
        UpdateFinancialGoalHandler             $updateFinancialGoalHandler,
        UpdateMonthlyStatisticsHandler         $updateMonthlyStatisticsHandler,
        UpdateMonthlyCategoryStatisticsHandler $updateMonthlyCategoryStatisticsHandler,
        DeleteOperationRequest                 $request,
        OperationsLogger                       $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false];

        try {
            $command = DeleteOperationCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);

            $updateFinancialGoalCommand = new UpdateFinancialGoalCommand(
                accountId: $command->accountId,
                previousAmount: $response->operationAmount,
                newAmount: $response->operationAmount,
                operationDate: $response->date,
                type: $response->operationType,
                isDelete: true,
            );
            $updateFinancialGoalHandler->handle($updateFinancialGoalCommand);

            $userId = auth()->user()->uuid;
            list($year, $month) = [(new DateVO($response->date))->year(), (new DateVO($response->date))->month()];
            $updateMonthlyStatisticsCommand = new UpdateMonthlyStatisticsCommand(
                composedId: $this->buildMonthlyStatisticsComposedId(month: $month, year: $year, userId: $userId),
                userId: $userId,
                year: $year,
                month: $month,
                previousAmount: $response->operationAmount,
                newAmount: 0,
                operationType: $response->operationType,
                toDelete: true
            );
            $updateMonthlyStatisticsHandler->handle($updateMonthlyStatisticsCommand);

            $updateMonthlyCategoryStatisticCommand = new UpdateMonthlyCategoryStatisticsCommand(
                composedId: $this->buildMonthlyCategoryStatisticsComposedId(
                    month: $month,
                    year: $year,
                    userId: $userId,
                    categoryId: $response->categoryId,
                ),
                userId: $userId,
                year: $year,
                month: $month,
                previousAmount: $response->operationAmount,
                newAmount: 0,
                operationType: $response->operationType,
                categoryId: $response->categoryId,
                toDelete: true,
            );
            $updateMonthlyCategoryStatisticsHandler->handle($updateMonthlyCategoryStatisticCommand);

            $httpJson = [
                'status' => true,
                'isDeleted' => $response->isDeleted,
                'message' => $response->message,
                'operationId' => $command->operationId,
            ];
        } catch (
        NotFoundAccountException|
        NotFoundOperationException $e
        ) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::WARNING,
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
