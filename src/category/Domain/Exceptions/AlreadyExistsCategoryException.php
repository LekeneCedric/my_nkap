<?php

namespace App\category\Domain\Exceptions;

use Exception;

class AlreadyExistsCategoryException extends Exception
{
    protected $message = 'Une catégorie de ce nom existe déjà !';
}
