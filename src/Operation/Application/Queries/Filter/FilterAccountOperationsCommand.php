<?php

namespace App\Operation\Application\Queries\Filter;

class FilterAccountOperationsCommand
{
    public ?string $userId;
    /**
     *
     */
    public function __construct(
        public int $page,
        public int $limit,
    )
    {
        $this->accountId = null;
        $this->userId = null;
    }
}
