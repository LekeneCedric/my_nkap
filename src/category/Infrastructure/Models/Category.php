<?php

namespace App\category\Infrastructure\Models;

use App\category\Domain\Category as CategoryDomain;
use App\category\Infrastructure\database\Factories\CategoryFactory;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\User\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static whereUuid(string|null $value)
 * @method static create(array|int[] $data)
 * @property mixed $uuid
 * @property mixed $icon
 * @property mixed $name
 * @property mixed $description
 * @property mixed $color
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

    public function toDomain(): CategoryDomain
    {
        return new CategoryDomain(
            categoryId: new Id($this->uuid),
            icon: new StringVO($this->icon),
            name: new StringVO($this->name),
            color: new StringVO($this->color),
            description: new StringVO($this->description),
        );
    }
}
