<?php

namespace App\User\Domain\Service;

use App\Shared\Domain\VO\StringVO;

interface CheckIfAlreadyUserExistWithSameEmailByEmailService
{
    /**
     * @param StringVO $email
     * @return bool
     */
    public function execute(StringVO $email): bool;
}
