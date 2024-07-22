<?php

namespace App\Statistics\Infrastructure\Model;

use App\Statistics\Infrastructure\database\factories\MonthlyStatisticFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyStatistic extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'id',
        'user_id',
        'month',
        'year',
        'total_income',
        'total_expense',
        'created_at',
        'updated_at'
    ];
    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'total_income' => 'float',
        'total_expense' => 'float',
    ];
    protected static function newFactory(): MonthlyStatisticFactory
    {
        return MonthlyStatisticFactory::new();
    }
}
