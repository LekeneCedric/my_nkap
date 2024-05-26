<?php

namespace App\category\Infrastructure\Http\Controllers;

use App\category\Application\Command\Delete\DeleteCategoryCommand;
use App\category\Application\Command\Delete\DeleteCategoryHandler;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\Exceptions\NotFoundUserCategoryException;
use App\category\Infrastructure\Logs\CategoryLogger;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteCategoryAction
{
    public function __invoke(
        DeleteCategoryHandler $handler,
        Request               $request,
        CategoryLogger        $logger,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'isDeleted' => false,
        ];

        try {
            $command = new DeleteCategoryCommand(
                userId: $request->get('userId'),
                categoryId: $request->get('categoryId')
            );
            $response = $handler->handle($command);
            $httpJson = [
                'status' => true,
                'isDeleted' => $response->isDeleted,
                'message' => $response->message,
            ];
        } catch (
        NotFoundUserCategoryException|
        NotFoundCategoryException $e) {
            $httpJson['message'] = config('my-nkap.message.technical_error');
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ERROR,
                description: $e,
            );
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
