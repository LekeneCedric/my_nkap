<?php

namespace App\FinancialGoal\Application\Query\All;

use Illuminate\Support\Facades\DB;
use PDO;

class GetAllFinancialGoalHandler
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    /**
     * @param string $userId
     * @return GetAllFinancialGoalResponse
     */
    public function handle(string $userId): GetAllFinancialGoalResponse
    {
        $response = new GetAllFinancialGoalResponse();

        $sql = "
            SELECT
                fg.uuid as id,
                ac.uuid as accountId,
                fg.start_date as startDate,
                fg.end_date as endDate,
                fg.details as title,
                fg.current_amount as currentAmount,
                fg.desired_amount as desiredAmount,
                fg.is_complete as isComplete
            FROM financial_goals fg
            INNER JOIN users AS u ON fg.user_id = u.id
            INNER JOIN accounts AS ac ON fg.account_id = ac.id
            WHERE fg.is_deleted = false AND u.uuid=:userId
        ";
        $st = $this->pdo->prepare($sql);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $st->execute([
            'userId' => $userId,
        ]);

        $response->financialGoals = $st->fetchAll();
        return $response;
    }
}
