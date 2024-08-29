<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Operation\Application\Command\MakeOperation\MakeOperationHandler;
use App\Operation\Application\Command\MakeOperation\makeOperationResponse;
use App\Operation\Infrastructure\Factories\MakeOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\MakeOperationRequest;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Domain\Transaction\TransactionCommandHandler;
use App\Shared\Domain\Transaction\TransactionSession;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class MakeOperationAction
{
    use StatisticsComposedIdBuilderTrait;

    public function __invoke(
        MakeOperationHandler $handler,
        TransactionSession   $transactionSession,
        MakeOperationRequest $request,
        OperationsLogger     $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false, 'operationIsSaved' => false];

        try {
            $command = MakeOperationCommandFactory::buildFromRequest($request);
            list($year, $month) = [(new DateVO($command->date))->year(), (new DateVO($command->date))->month()];
            $userId = auth()->user()->uuid;
            $command->userId = $userId;
            $command->year = $year;
            $command->month = $month;
            $command->previousAmount = $request->get('previousAmount');
            $command->monthlyStatsComposedId = $this->buildMonthlyStatisticsComposedId(month: $month, year: $year, userId: $userId);
            $command->monthlyStatsByCategoryComposedId = $this->buildMonthlyCategoryStatisticsComposedId(month: $month, year: $year, userId: $userId, categoryId: $command->categoryId);
            $transactionCommandHandler = new TransactionCommandHandler($handler, $transactionSession);
            $response = $transactionCommandHandler->handle($command);

            if ($response instanceof makeOperationResponse) {
                $httpJson['status'] = $response->operationSaved;
                $httpJson['operationSaved'] = $response->operationSaved;
                $httpJson['operationId'] = $response->operationId;
            }
            $httpJson['message'] = $response->message;
        } catch (InvalidArgumentException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: $e,
            );
            $httpJson['message'] = $e->getMessage();
        }

        return response()->json($httpJson);
    }
}
