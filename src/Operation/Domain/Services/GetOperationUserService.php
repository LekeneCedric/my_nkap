<?php

namespace App\Operation\Domain\Services;

use App\Operation\Domain\OperationUser;

interface GetOperationUserService
{
    /**
     * @param string $userId
     * @return OperationUser|null
     */
    public function execute(string $userId): ?OperationUser;
}
