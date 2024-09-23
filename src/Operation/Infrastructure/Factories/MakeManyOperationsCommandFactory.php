<?php

namespace App\Operation\Infrastructure\Factories;

use App\Operation\Application\Command\MakeManyOperations\MakeManyOperationsCommand;
use App\Operation\Application\Command\MakeOperation\MakeOperationCommand;
use App\Operation\Domain\OperationTypeEnum;
use Illuminate\Http\Request;

class MakeManyOperationsCommandFactory
{
    public static function buildFromRequest(Request $request): MakeManyOperationsCommand
    {
        $command = new MakeManyOperationsCommand();
        foreach ($request->get('operations') as $operation) {
            $command->operations[] = new MakeOperationCommand(
                accountId: $operation['accountId'],
                type: OperationTypeEnum::from($operation['type']),
                amount: $operation['amount'],
                categoryId: $operation['categoryId'],
                detail: $operation['detail'],
                date: $operation['date']
            );
        }
        return $command;
    }
}
