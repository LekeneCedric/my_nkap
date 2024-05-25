<?php

namespace App\Operation\Application\Queries\Filter;

use App\Shared\Builder\WhereFilter;
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
        $userId = $command->userId;
        $page = $command->page;
        $limit = $command->limit;
        $accountId = $command->accountId;

        $whereFilter = WhereFilter::asFilter()
            ->withStringParameter('ac.uuid', $accountId)
            ->withStringParameter('u.uuid', $userId)
            ->build();

        $offset = $this->getCorrespondingOffset($page, $limit);
        list($total, $numberOfPages) = $this->count($limit, $whereFilter);

        $sql = "
            SELECT
                op.type as operationType,
                op.uuid as operationId,
                ac.uuid as accountId,
                op.date as operationDate,
                op.details as operationDetails,
                op.category as operationCategory,
                op.amount as operationAmount
            FROM operations AS op
            INNER JOIN accounts AS ac ON op.account_id = ac.id
            INNER JOIN users AS u ON ac.user_id = u.id
            WHERE ac.is_deleted = false AND
                  op.is_deleted = false AND
                  u.is_deleted = false AND
                  $whereFilter
            ORDER BY op.date DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute([
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $operations = $stmt->fetchAll();
        return new FilterAccountOperationsResponse(
            status: true,
            operations: $operations,
            total: $total,
            numberOfPages: $numberOfPages,
        );
    }

    /**
     * @param int $page
     * @param int $limit
     * @return int
     */
    private function getCorrespondingOffset(int $page, int $limit): int
    {
        return ($page - 1) * $limit;
    }

    private function count(int $limit, string $whereFilter): array
    {
        $sql = "
            SELECT COUNT(op.id)
            FROM operations AS op
            INNER JOIN accounts AS ac ON op.account_id = ac.id
            INNER JOIN users AS u ON ac.user_id = u.id
            WHERE ac.is_deleted = false AND
                  op.is_deleted = false AND
                  u.is_deleted = false AND
                  $whereFilter
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_OBJ);
        $stmt->execute();

        $count = $stmt->fetchColumn();
        $limit = $limit ?: $count;
        $numberOfPages = $limit === 0 ? $limit : ceil($count / $limit);

        return [$count, $numberOfPages];
    }
}
