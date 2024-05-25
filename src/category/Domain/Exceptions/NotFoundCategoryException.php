<?php

namespace App\category\Domain\Exceptions;

class NotFoundCategoryException extends \Exception
{
    protected $message = 'La catégorie n\'existe pas !';
}
