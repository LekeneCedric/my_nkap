<?php

namespace App\Shared\Domain\Command;

interface CommandHandler
{
    public function handle(Command $command): mixed;
}
