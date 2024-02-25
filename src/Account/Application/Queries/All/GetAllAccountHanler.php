<?php

namespace App\Account\Application\Queries\All;

use Illuminate\Support\Facades\DB;

class GetAllAccountHanler
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    public function handle(): GetAllAccountResponse
    {
        $sql = "
            SELECT
                c.name as accountName,
                c.type as accountType,
                c.total_incomes as totalIncomes,
                c.total_expenses as totalExpenses,
                c.balance as accountBalance,
                c.is_include_in_total_balance as isIncludeInTotalBalance,
                c.color as accountColor,
                c.icon as accountIcon
            FROM accounts AS c
            WHERE c.is_deleted = false
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();

        $accounts = $stmt->fetchAll();

        return new GetAllAccountResponse(
            status: true,
            accounts: $accounts,
        );
    }
}
