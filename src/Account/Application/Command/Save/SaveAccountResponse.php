<?php

namespace App\Account\Application\Command\Save;

class SaveAccountResponse
{
    public bool $isSaved = false;
    public bool $status = false;
    public string $accountId = '';
}
