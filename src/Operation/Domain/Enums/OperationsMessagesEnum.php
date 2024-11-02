<?php

namespace App\Operation\Domain\Enums;

enum OperationsMessagesEnum
{
    const DELETED = 'operation_deleted';
    const NOT_FOUND = 'operation_not_found';
    const GREATER_THAN_ACCOUNT_BALANCE = 'operation_greater_than_account_balance';
}
