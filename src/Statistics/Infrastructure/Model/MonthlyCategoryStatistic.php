<?php

namespace App\Statistics\Infrastructure\Model;

use App\Statistics\Domain\MonthlyCategoryStatistic AS MonthlyCategoryStatisticDomain;
use App\Statistics\Infrastructure\database\factories\MonthlyCategoryStatisticFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static whereComposedId(string $composedId)
 * @method static create(array $array)
 */
class MonthlyCategoryStatistic extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'id',
        'composed_id',
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

    public function toDomain(): MonthlyCategoryStatisticDomain
    {
        return MonthlyCategoryStatisticDomain::createFromModel(
            id: $this->id,
            composedId: $this->composed_id,
            userId: $this->user_id,
            year: $this->year,
            month: $this->month,
            totalIncome: $this->total_income,
            totalExpense: $this->total_expense,
            categoryId: $this->category_id,
        );
    }
}
