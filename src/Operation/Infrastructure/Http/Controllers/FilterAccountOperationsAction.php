<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Operation\Application\Queries\Filter\FilterAccountOperationsHandler;
use App\Operation\Infrastructure\Factories\FilterAccountOperationsCommandBuilder;
use Illuminate\Http\Request;

class FilterAccountOperationsAction
{
    public function __invoke(
        FilterAccountOperationsHandler $handler,
        Request $request,
    )
    {

        $httpJson = [
            'status' => false,
            'operations' => [],
        ];

        try {
            $command = FilterAccountOperationsCommandBuilder::buildFromRequest($request);

            $response = $handler->handle($command);

            $httpJson = [
              'status' => $response->status,
              'operations' => $response->operations
            ];
        } catch (\Exception) {
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête , veuillez réessayer ultérieurement !';
        }

        return response()->json($httpJson);
    }
}
