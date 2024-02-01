<?php

namespace App\Account\Infrastructure\Http\Factories;

use App\Account\Application\Command\Save\SaveAccountCommand;
use App\Account\Infrastructure\Http\Requests\SaveAccountRequest;

class SaveAccountCommandFactory
{

    public static function buildFromRequest(SaveAccountRequest $request): SaveAccountCommand
    {
        $requestsValues = $request->all();
        $command = new SaveAccountCommand(
            name: $requestsValues['name'],
            type: $requestsValues['type'],
            icon: $requestsValues['icon'],
            color: $requestsValues['color'],
            balance: $requestsValues['balance'],
            isIncludeInTotalBalance: $requestsValues['isIncludeInTotalBalance']
        );
        $command->accountId = $requestsValues['accountId'] ?? null;

        return $command;
    }
}
