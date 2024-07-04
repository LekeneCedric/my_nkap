<?php

namespace App\Account\Application\Queries\All;

use App\Account\Domain\Exceptions\ErrorOnGetAllAccountException;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class GetAllAccountHandler
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    /**
     * @param string $userId
     * @return GetAllAccountResponse
     * @throws ErrorOnGetAllAccountException
     */
    public function handle(string $userId): GetAllAccountResponse
    {
        $sql = "
            SELECT
                ac.uuid as accountId,
                ac.name as accountName,
                ac.type as accountType,
                ac.total_incomes as totalIncomes,
                ac.total_expenses as totalExpenses,
                ac.balance as accountBalance,
                ac.is_include_in_total_balance as isIncludeInTotalBalance,
                ac.color as accountColor,
                ac.icon as accountIcon
            FROM accounts AS ac
            INNER JOIN users AS u ON ac.user_id = u.id
            WHERE ac.is_deleted = false AND
                  u.is_deleted = false AND
                  u.is_active = true AND
                  u.uuid = :userId
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $stmt->execute([
                'userId' => $userId
            ]);

            $accounts = $stmt->fetchAll();

            return new GetAllAccountResponse(
                status: true,
                accounts: $accounts,
            );
        } catch (Exception $e) {
           throw new ErrorOnGetAllAccountException($e->getMessage());
        }
    }
}
