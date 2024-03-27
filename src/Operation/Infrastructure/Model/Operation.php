<?php

namespace App\Operation\Infrastructure\Model;

use App\Account\Infrastructure\Model\Account;
use App\Operation\Infrastructure\database\factories\OperationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Operation extends Model
{
    use HasFactory;

    protected $table = 'operations';

    protected static function newFactory(): OperationFactory
    {
        return OperationFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
