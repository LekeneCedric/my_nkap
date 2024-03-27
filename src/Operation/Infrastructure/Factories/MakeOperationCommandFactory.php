<?php

namespace App\Operation\Infrastructure\Factories;

use App\Operation\Application\Command\MakeOperation\MakeOperationCommand;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Http\Requests\MakeOperationRequest;

class MakeOperationCommandFactory
{

    public static function buildFromRequest(MakeOperationRequest $request): MakeOperationCommand
    {
        $accountId = $request->get('accountId');
        $operationId = $request->get('operationId');
        $type = $request->get('type');
        $amount = $request->get('amount');
        $category = $request->get('category');
        $detail = $request->get('detail');
        $date = $request->get('date');

        $command = new MakeOperationCommand(
          accountId: $accountId,
          type: OperationTypeEnum::from($type),
          amount: $amount,
          category: $category,
          detail: $detail,
          date: $date
        );
        $command->operationId = $operationId;
        return $command;
    }
}
