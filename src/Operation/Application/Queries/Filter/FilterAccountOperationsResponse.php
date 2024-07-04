<?php

namespace App\Operation\Application\Queries\Filter;

class FilterAccountOperationsResponse
{

    public function __construct(
        public bool $status,
        public array $operations,
        public int $total,
        public int $numberOfPages
    )
    {
    }
}
