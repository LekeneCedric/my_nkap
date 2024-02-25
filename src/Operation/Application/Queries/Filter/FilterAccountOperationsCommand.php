<?php

namespace App\Operation\Application\Queries\Filter;

class FilterAccountOperationsCommand
{
    /**
     * @param string $accountId
     */
    public function __construct(
        public string $accountId,
    )
    {
    }
}
