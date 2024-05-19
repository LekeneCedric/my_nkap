<?php

namespace App\Account\Infrastructure\Model;

use App\Account\Infrastructure\database\factories\AccountFactory;
use App\Operation\Infrastructure\Model\Operation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';
    protected $guarded = [];
    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }
}
