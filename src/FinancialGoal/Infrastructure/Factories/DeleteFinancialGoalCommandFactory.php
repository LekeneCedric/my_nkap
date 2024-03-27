<?php

namespace App\FinancialGoal\Infrastructure\Factories;

use App\FinancialGoal\Application\Command\Delete\DeleteFinancialGoalCommand;
use Illuminate\Http\Request;

class DeleteFinancialGoalCommandFactory
{

    public static function buildFromRequest(Request $request): DeleteFinancialGoalCommand
    {
        self::validate($request);
        return new DeleteFinancialGoalCommand(
            financialGoalId: $request->get('financialGoalId')
        );
    }

    private static function validate(Request $request): void
    {
        if (empty($request->get('financialGoalId'))) {
            throw new \InvalidArgumentException('Informations invalides pour soumettre la requête !');
        }
    }
}
