<?php

namespace App\category\Infrastructure\Models;

use App\category\Infrastructure\database\Factories\CategoryFactory;
use App\User\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static whereUuid(string|null $value)
 * @method static create(array|int[] $data)
 * @property mixed $uuid
 */
class Category extends Model
{
    protected $guarded = [];

    public static function factory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
