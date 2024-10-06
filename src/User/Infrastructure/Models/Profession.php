<?php

namespace App\User\Infrastructure\Models;

use App\User\Infrastructure\database\Factories\ProfessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $profession_id)
 */
class Profession extends Model
{
    use HasFactory;
    protected $table = 'professions';
    protected static function newFactory(): ProfessionFactory
    {
        return ProfessionFactory::new();
    }
}
