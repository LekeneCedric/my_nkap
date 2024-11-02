<?php

namespace App\Operation\Domain\Exceptions;

use App\Operation\Domain\Enums\OperationsMessagesEnum;

class NotFoundOperationException extends \Exception
{
    protected $message = OperationsMessagesEnum::NOT_FOUND;
}
