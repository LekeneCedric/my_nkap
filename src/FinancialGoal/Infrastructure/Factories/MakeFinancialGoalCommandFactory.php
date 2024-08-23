<?php

namespace App\FinancialGoal\Infrastructure\Factories;

use App\FinancialGoal\Application\Command\Make\MakeFinancialGoalCommand;
use Illuminate\Http\Request;

class MakeFinancialGoalCommandFactory
{

    public static function buildFromRequest(Request $request): MakeFinancialGoalCommand
    {
        self::validate($request);
        $command = new MakeFinancialGoalCommand(
            userId: $request->get('userId'),
            accountId: $request->get('accountId'),
            startDate: $request->get('startDate'). ' 00:00:00',
            endDate: $request->get('endDate'). ' 00:00:00',
            desiredAmount: $request->get('desiredAmount'),
            details: $request->get('details')
        );
        $command->financialGoalId = $request->get('financialGoalId');
        return $command;
    }

    private static function validate(Request $request): void
    {
        if (
            empty($request->get('userId')) ||
            empty($request->get('accountId')) ||
            empty($request->get('startDate')) ||
            empty($request->get('endDate')) ||
            (empty($request->get('desiredAmount')) || (!is_numeric($request->get('desiredAmount')))) ||
            empty($request->get('details'))
        ) {
            throw new \InvalidArgumentException("Informations invalides pour soumettre la requête !");
        }
    }
}
