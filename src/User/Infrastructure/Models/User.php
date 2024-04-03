<?php

namespace App\User\Infrastructure\Models;

use App\Bootstrap\Infrastructure\database\factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use Notifiable, HasFactory, HasApiTokens;

    protected static function newFactory(): UserFactory|Factory
    {
        return UserFactory::new();
    }
}
