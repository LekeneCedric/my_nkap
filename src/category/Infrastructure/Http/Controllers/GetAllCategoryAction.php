<?php

namespace App\category\Infrastructure\Http\Controllers;

use App\category\Application\Query\all\GetAllCategoryHandler;
use App\category\Infrastructure\Logs\CategoryLogger;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllCategoryAction
{
    public function __invoke(
        GetAllCategoryHandler $handler,
        CategoryLogger $logger,
        string $userId,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'categories' => [],
        ];

        try {
            $response = $handler->handle(userId: $userId);
            $httpJson = [
                'status' => true,
                'categories' => $response->categories
            ];
        } catch (Exception $e) {
            $httpJson['message'] = config('my-nkap.message.critical_technical_error');
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: $e,
            );
        }
        return response()->json($httpJson);
    }
}
