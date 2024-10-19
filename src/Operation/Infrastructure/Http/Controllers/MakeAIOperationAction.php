<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\category\Domain\Exceptions\EmptyCategoriesException;
use App\Operation\Application\Command\MakeAIOperation\MakeAIOperationHandler;
use App\Operation\Domain\Exceptions\AIOperationEmptyMessageException;
use App\Operation\Infrastructure\Factories\MakeAIOperationCommandFactory;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use App\User\Domain\Exceptions\NotFoundUserException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class MakeAIOperationAction
{
    public function __invoke(
        MakeAIOperationHandler $handler,
        Request $request,
        OperationsLogger $logger,
    ): JsonResponse
    {
        $httpResponse = [
            'status' => false,
            'operationIsOk' => false,
            'message' => '',
            'consumedToken' => 0,
            'operations' => [],
        ];
        try {
            $command = MakeAIOperationCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $httpResponse = [
                'status' => true,
                'operationIsOk' => $response->operationOk,
                'message' => $response->message,
                'consumedToken' => $response->consumedToken,
                'operations' => $response->operations
            ];
        } catch (
            AIOperationEmptyMessageException
            |NotFoundUserException
            |EmptyCategoriesException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::WARNING,
                description: $e,
            );
            $httpResponse['message'] = $e->getMessage();
        } catch (InvalidArgumentException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ERROR,
                description: $e,
            );
            $httpResponse['message'] = $e->getMessage();
        } catch (Exception $e) {

            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: $e,
            );
            $httpResponse['message'] = $e->getMessage();
        }

        return response()->json($httpResponse);
    }
}
