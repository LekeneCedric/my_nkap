<?php

namespace App\User\Domain\Repository;

use App\Operation\Domain\OperationUser;
use App\User\Domain\Enums\UserStatusEnum;
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

    /**
     * @param string $email
     * @return User|null
     */
    public function ofEmail(string $email): ?User;

    /**
     * @param string $email
     * @param UserStatusEnum $status
     * @return User|null
     */
    public function of(string $email, UserStatusEnum $status): ?User;

    /**
     * @param OperationUser $user
     * @return void
     */
    public function updateToken(OperationUser $user): void;
}
