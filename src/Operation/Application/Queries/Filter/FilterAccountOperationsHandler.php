<?php

namespace App\Operation\Application\Queries\Filter;

use Illuminate\Support\Facades\DB;

class FilterAccountOperationsHandler
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    public function handle(FilterAccountOperationsCommand $command): FilterAccountOperationsResponse
    {
        $accountId = $command->accountId;

        $sql = "
            SELECT
                op.type as operationType,
                op.uuid as operationId,
                op.date as operationDate,
                op.details as operationDetails,
                op.category as operationCategory,
                op.amount as operationAmount
            FROM operations AS op
            INNER JOIN accounts AS ac ON op.account_id = ac.id
            WHERE ac.is_deleted = false AND
                  op.is_deleted = false AND
                  ac.uuid = :accountId
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute([
            'accountId' => $accountId,
        ]);

        $operations = $stmt->fetchAll();
        return new FilterAccountOperationsResponse(
            status: true,
            operations: $operations
        );
    }
}
