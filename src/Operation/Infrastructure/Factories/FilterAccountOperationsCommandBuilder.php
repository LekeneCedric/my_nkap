<?php

namespace App\Operation\Infrastructure\Factories;

use App\Operation\Application\Queries\Filter\FilterAccountOperationsCommand;
use Illuminate\Http\Request;

class FilterAccountOperationsCommandBuilder
{
    /**
     * @param Request $request
     * @return FilterAccountOperationsCommand
     */
    public static function buildFromRequest(Request $request): FilterAccountOperationsCommand
    {
        return new FilterAccountOperationsCommand(
          accountId: $request->get('accountId'),
        );
    }
}
