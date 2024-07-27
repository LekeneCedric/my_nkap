<?php

namespace App\Statistics\Infrastructure\Model;

use App\Statistics\Domain\MonthlyStatistic AS MonthlyStatisticDomain;
use App\Statistics\Infrastructure\database\factories\MonthlyStatisticFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static whereComposedId(string $composedId)
 * @method static create(array $toCreateArray)
 * @method static whereUserId(string $userId)
 * @property mixed $id
 * @property mixed $composed_id
 * @property mixed $user_id
 * @property mixed $year
 * @property mixed $month
 * @property mixed $total_income
 * @property mixed $total_expense
 */
class MonthlyStatistic extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'id',
        'composed_id',
        'user_id',
        'month',
        'year',
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
    protected static function newFactory(): MonthlyStatisticFactory
    {
        return MonthlyStatisticFactory::new();
    }

    public function toDomain(): MonthlyStatisticDomain
    {
        return MonthlyStatisticDomain::createFromModel(
            id: $this->id,
            composedId: $this->composed_id,
            userId: $this->user_id,
            year: $this->year,
            month: $this->month,
            totalIncome: $this->total_income,
            totalExpense: $this->total_expense,
        );
    }
}
