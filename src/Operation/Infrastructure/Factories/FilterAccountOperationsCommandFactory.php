<?php

namespace App\Operation\Infrastructure\Factories;

use App\Operation\Application\Queries\Filter\FilterAccountOperationsCommand;
use Illuminate\Http\Request;
use InvalidArgumentException;

class FilterAccountOperationsCommandFactory
{
    /**
     * @param Request $request
     * @return FilterAccountOperationsCommand
     */
    public static function buildFromRequest(Request $request): FilterAccountOperationsCommand
    {
        self::validate($request);
        $userId = $request->get('userId');
        $accountId = $request->get('accountId');
        $date = $request->get('date');
        $categoryId = $request->get('categoryId');
        $operationType = $request->get('operationType');
        $month = $request->get('month');
        $year = $request->get('year');
        $command = new FilterAccountOperationsCommand(
            page: $request->get('page'),
            limit: $request->get('limit'),
        );
        $command->userId = $userId;
        $command->accountId = $accountId;
        $command->date = $date;
        $command->categoryId = $categoryId;
        $command->operationType = $operationType;
        $command->month = $month;
        $command->year = $year;

        return $command;
    }

    private static function validate(Request $request): void
    {
        if (
            (empty($request->get('userId')) && empty($request->get('accountId'))) ||
            empty($request->get('page')) ||
            empty($request->get('limit'))
        ) {
            throw new InvalidArgumentException('Commande invalide !');
        }
    }
}
