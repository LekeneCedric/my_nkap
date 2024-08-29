<?php

namespace App\Shared\Domain\Transaction;

use Exception;
use Throwable;

class TransactionExecutionErrorException extends Exception
{
    protected $message = 'Transaction Exception';
    protected $code = 500;
    protected string $file = '';
    protected int $line = 0;
    protected array $trace = [];

    public function __construct(
        string     $message = "",
        int        $code = 0,
        string     $file = '',
        int        $line = 0,
        array      $trace = [],
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
        $this->file = $file;
        $this->line = $line;
    }
}
