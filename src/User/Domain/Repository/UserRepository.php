<?php

namespace App\User\Domain\Repository;

use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\User;

interface UserRepository
{
    /**
     * @param User $user
     * @return void
     * @throws ErrorOnSaveUserException
     */
    public function save(User $user): void;
}
