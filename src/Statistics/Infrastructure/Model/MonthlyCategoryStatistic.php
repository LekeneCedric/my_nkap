<?php

namespace App\Statistics\Infrastructure\Model;

use App\Statistics\Infrastructure\database\factories\MonthlyCategoryStatisticFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyCategoryStatistic extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'id',
        'user_id',
        'month',
        'year',
        'category_id',
        'category_icon',
        'category_label',
        'category_color',
        'percentage',
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

    public static function newFactory(): MonthlyCategoryStatisticFactory
    {
        return MonthlyCategoryStatisticFactory::new();
    }
}
