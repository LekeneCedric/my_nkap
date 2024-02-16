<?php

namespace App\Operation\Infrastructure\Http\Factories;

use App\Operation\Application\Command\DeleteOperation\DeleteOperationCommand;
use App\Operation\Infrastructure\Http\Requests\DeleteOperationRequest;

class DeleteOperationCommandFactory
{
    public static function buildFromRequest(DeleteOperationRequest $request): DeleteOperationCommand
    {
        $accountId = $request->get('accountId');
        $operationId = $request->get('operationId');

        return new DeleteOperationCommand(
            accountId: $accountId,
            operationId: $operationId,
        );
    }
}
