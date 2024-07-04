<?php

namespace App\category\Domain\Exceptions;

use Exception;

class NotFoundUserCategoryException extends Exception
{
    protected $message = 'L\'utilisateur sélectionné n\'existe pas !';
}
