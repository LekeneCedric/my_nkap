<?php

namespace App\Account\Application\Queries\All;

class GetAllAccountResponse
{

    /**
     * @param bool $status
     * @param array $accounts
     */
    public function __construct(
        public bool $status,
        public array $accounts
    )
    {
    }
}
