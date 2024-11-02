<?php

namespace App\Operation\Infrastructure\Factories;

use App\Operation\Application\Command\MakeAIOperation\MakeAIOperationCommand;
use Illuminate\Http\Request;

class MakeAIOperationCommandFactory
{
    /**
     * @param Request $request
     * @return MakeAIOperationCommand
     */
    public static function buildFromRequest(Request $request): MakeAIOperationCommand
    {
        return new MakeAIOperationCommand(
            userId: $request->get('userId'),
            accounts: $request->get('accounts'),
            categories: $request->get('categories'),
            currentDate: $request->get('currentDate'),
            message: $request->get('message'),
            language: $request->get('language'),
        );
    }
}
