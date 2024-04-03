<?php

namespace App\User\Infrastructure\Models;

use App\User\Infrastructure\database\Factories\ProfessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    use HasFactory;
    protected $table = 'professions';
    protected static function newFactory(): ProfessionFactory
    {
        return ProfessionFactory::new();
    }
}
