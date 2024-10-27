<?php

namespace App\Operation\Domain\Exceptions;

use App\Operation\Domain\Enums\OperationsMessagesEnum;

class OperationGreaterThanAccountBalanceException extends \Exception
{
    protected $message = OperationsMessagesEnum::GREATER_THAN_ACCOUNT_BALANCE;
}
