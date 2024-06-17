<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Operation\Application\Queries\Filter\FilterAccountOperationsHandler;
use App\Operation\Infrastructure\Factories\FilterAccountOperationsCommandFactory;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Operation\Infrastructure\ViewModels\FilterAccountOperationsViewModel;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

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
//            $operations = (new FilterAccountOperationsViewModel($response))->toArray();
            $httpJson = [
              'status' => $response->status,
              'operations' => $response->operations,
              'total' => $response->total,
              'numberOfPages' => $response->numberOfPages
            ];
            $logger->Log(
                message: 'filter account',
                level: LogLevelEnum::INFO,
                description: 'filter ',
            );
        } catch (InvalidArgumentException $e) {
            dd($e);
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
            dd($e);
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
