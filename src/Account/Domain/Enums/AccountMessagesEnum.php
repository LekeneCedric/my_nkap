<?php

namespace App\Account\Domain\Enums;

enum AccountMessagesEnum
{
    const CREATED = 'account_created';
    const UPDATED = 'account_updated';
    const DELETED = 'account_deleted';
    const NOT_FOUND = 'account_not_found';
}
