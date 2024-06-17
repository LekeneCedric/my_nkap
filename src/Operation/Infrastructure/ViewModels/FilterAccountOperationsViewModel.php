<?php

namespace App\Operation\Infrastructure\ViewModels;

use App\Operation\Application\Queries\Filter\FilterAccountOperationsResponse;

class FilterAccountOperationsViewModel
{
    public function __construct(
        private FilterAccountOperationsResponse $response,
    )
    {
    }

    public function toArray(): array
    {
        $operations = $this->response->operations;

        return [];
    }
}
