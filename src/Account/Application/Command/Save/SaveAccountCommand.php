<?php

namespace App\Account\Application\Command\Save;

class SaveAccountCommand
{
    public ?string $accountId = null;

    public function __construct(
        public string $name,
        public string $type,
        public string $icon,
        public string $color,
        public float $balance,
        public bool $isIncludeInTotalBalance,
    )
    {
    }
}
