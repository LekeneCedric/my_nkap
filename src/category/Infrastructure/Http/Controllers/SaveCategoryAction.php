<?php

namespace App\category\Infrastructure\Http\Controllers;

use App\category\Application\Command\Save\SaveCategoryHandler;
use App\category\Domain\Exceptions\AlreadyExistsCategoryException;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\Exceptions\NotFoundUserCategoryException;
use App\category\Infrastructure\Factories\SaveCategoryCommandFactory;
use App\category\Infrastructure\Logs\CategoryLogger;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaveCategoryAction
{
    public function __invoke(
        SaveCategoryHandler $handler,
        Request             $request,
        CategoryLogger      $logger,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'isSaved' => false,
        ];

        try {
            $command = SaveCategoryCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $httpJson = [
                'status' => true,
                'isSaved' => $response->isSaved,
                'message' => $response->message,
                'categoryId' => $response->categoryId,
            ];
        } catch (AlreadyExistsCategoryException $e) {
            $httpJson['message'] = $e->getMessage();
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::INFO,
                description: $e,
            );
        } catch (
        NotFoundCategoryException
        |NotFoundUserCategoryException  $e) {
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
