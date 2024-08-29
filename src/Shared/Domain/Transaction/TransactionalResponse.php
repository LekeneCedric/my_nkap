<?php

namespace App\Shared\Domain\Transaction;

class TransactionalResponse
{
    public bool $status = false;
    public string $message = "Une erreur est survenue lors du traitement de votre requête";
}
