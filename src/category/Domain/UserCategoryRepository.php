<?php

namespace App\category\Domain;

use App\Shared\VO\Id;

interface UserCategoryRepository
{
    /**
     * @param Id $id
     * @return UserCategory|null
     */
    public function ofId(Id $id): ?UserCategory;

    /**
     * @param UserCategory|null $user
     * @return void
     */
    public function save(?UserCategory $user): void;
}
