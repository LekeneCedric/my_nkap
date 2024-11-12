<?php

namespace App\Subscription\Application\Command\SubscribeNewUser;

class SubscribeNewUserCommand
{
    public function __construct(
        public string $userId,
    )
    {
    }
}
