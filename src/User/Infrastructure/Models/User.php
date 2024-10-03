<?php

namespace App\User\Infrastructure\Models;

use App\Account\Infrastructure\Model\Account;
use App\Bootstrap\Infrastructure\database\factories\UserFactory;
use App\category\Infrastructure\Models\Category;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\User\Infrastructure\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\User\Domain\User as UserDomain;
/**
 * @property mixed $uuid
 * @property mixed $id
 * @method static whereUuid(string|null $value)
 */
#[ObservedBy([UserObserver::class])]
class User extends Model
{
    use Notifiable, HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function newFactory(): UserFactory|Factory
    {
        return UserFactory::new();
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
    public function toDomain(): UserDomain
    {
        return new UserDomain(
            name: new StringVO($this->name),
            email: new StringVO($this->email),
            password: new StringVO($this->password),
            userId: new Id($this->uuid),
            professionId: new Id(Profession::where('id', $this->profession_id)->first()?->uuid),
        );
    }
}
