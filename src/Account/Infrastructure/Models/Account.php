<?php

namespace App\Account\Infrastructure\Models;

use App\Account\Infrastructure\database\factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }
}
