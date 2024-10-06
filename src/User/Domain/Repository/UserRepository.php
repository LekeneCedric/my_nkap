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
    public function create(User $user): void;

    /**
     * @param User $user
     * @return void
     * @throws ErrorOnSaveUserException
     */
    public function update(User $user): void;

    /**
     * @return string
     */
    public function userId(): string;

    public function ofEmail(string $email): ?User;
}
