<?php

namespace App\Profession\Application\Queries\GetAll;

use Illuminate\Support\Facades\DB;

class GetAllProfessionQueryHandler
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::getPdo();
    }

    public function handle(): GetAllProfessionResponse
    {
        $sql = "
            SELECT
                uuid as id,
                name,
                created_at as createdAt
            FROM professions
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return new GetAllProfessionResponse(
            professions:$result,
        );
    }
}
