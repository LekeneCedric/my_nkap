<?php

namespace App\FinancialGoal\Infrastructure\Model;

use App\FinancialGoal\Infrastructure\database\Factory\FinancialGoalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialGoal extends Model
{
    use HasFactory;

    protected $table = 'financial_goals';

    protected static function newFactory(): FinancialGoalFactory
    {
        return FinancialGoalFactory::new();
    }
}
