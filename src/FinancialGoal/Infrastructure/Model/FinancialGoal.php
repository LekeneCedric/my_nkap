<?php

namespace App\FinancialGoal\Infrastructure\Model;

use App\Account\Infrastructure\Model\Account;
use App\FinancialGoal\Infrastructure\database\Factory\FinancialGoalFactory;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use App\User\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\FinancialGoal\Domain\FinancialGoal AS FinancialGoalDomain;
class FinancialGoal extends Model
{
    use HasFactory;

    protected $table = 'financial_goals';

    protected static function newFactory(): FinancialGoalFactory
    {
        return FinancialGoalFactory::new();
    }

    public function toDomain(): FinancialGoalDomain
    {
        $userId = User::whereId($this->user_id)->first()?->uuid;
        $accountId = Account::whereId($this->account_id)->first()?->uuid;
        return FinancialGoalDomain::create(
            userId: new Id($userId),
            accountId: new Id($accountId),
            startDate: new DateVO($this->start_date),
            enDate: new DateVO($this->end_date),
            desiredAmount: new AmountVO($this->desired_amount),
            details: new StringVO($this->details),
            financialGoalId: new Id($this->uuid),
            currentAmount: new AmountVO($this->current_amount),
            isComplete: $this->is_complete
        );
    }
}
