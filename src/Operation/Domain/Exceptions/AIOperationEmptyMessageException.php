<?php

namespace App\Operation\Domain\Exceptions;

use App\Operation\Domain\OperationsMessagesEnum;
use Exception;

class AIOperationEmptyMessageException extends Exception
{
    protected $message = OperationsMessagesEnum::emptyAIMessage;
}
