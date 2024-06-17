<?php

namespace App\Operation\Application\Queries\Filter;

class FilterAccountOperationsCommand
{
    public ?string $userId;
    public ?string $date;
    public ?string $categoryId;

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
        $this->date = null;
        $this->categoryId = null;
    }
}
