<?php

namespace App\Profession\Application\Queries\GetAll;

class GetAllProfessionResponse
{
    public function __construct(
        public array $professions,
    )
    {
    }
}
