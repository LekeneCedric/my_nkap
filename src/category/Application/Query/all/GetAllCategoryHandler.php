<?php

namespace App\category\Application\Query\all;

use Illuminate\Support\Facades\DB;

class GetAllCategoryHandler
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    public function handle(string $userId): GetAllCategoryResponse
    {
        $response = new GetAllCategoryResponse();

        $sql = "
            SELECT
                c.uuid AS categoryId,
                c.icon AS icon,
                c.name AS name,
                c.color AS color,
                c.description AS description
            FROM categories AS c
            INNER JOIN users AS u ON c.user_id = u.id
            WHERE u.uuid=:userId AND
                  u.is_deleted = false AND
                  u.is_active = true
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute(['userId' => $userId]);
        $categories = $stmt->fetchAll();

        $response->categories = $categories;
        return $response;
    }
}
