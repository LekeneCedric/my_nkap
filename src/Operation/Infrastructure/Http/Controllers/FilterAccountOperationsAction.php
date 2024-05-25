<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Operation\Application\Queries\Filter\FilterAccountOperationsHandler;
use App\Operation\Infrastructure\Factories\FilterAccountOperationsCommandFactory;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterAccountOperationsAction
{
    public function __invoke(
        FilterAccountOperationsHandler $handler,
        Request $request,
        OperationsLogger $logger,
    ): JsonResponse
    {

        $httpJson = [
            'status' => false,
            'operations' => [],
        ];

        try {
            $command = FilterAccountOperationsCommandFactory::buildFromRequest($request);

            $response = $handler->handle($command);

            $httpJson = [
              'status' => $response->status,
              'operations' => $response->operations,
              'total' => $response->total,
              'numberOfPages' => $response->numberOfPages
            ];
            $logger->Log(
                message: 'filter account',
                level: LogLevelEnum::ERROR,
                description: 'filter ',
            );
        } catch (\InvalidArgumentException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ALERT,
                description: [
                    'userId' => $request->get('userId'),
                    'page' => $request->get('page'),
                    'limit' => $request->get('limit'),
                    'accountId' => $request->get('accountId'),
                ],
            );
            $httpJson['message'] = $e->getMessage();
        }
        catch (Exception $e) {
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
