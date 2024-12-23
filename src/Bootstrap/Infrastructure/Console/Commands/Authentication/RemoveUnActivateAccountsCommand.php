<?php

namespace App\Bootstrap\Infrastructure\Console\Commands\Authentication;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;

class RemoveUnActivateAccountsCommand extends Command
{
    private PDO $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = DB::getPdo();
    }

    protected $signature = 'authentication:remove-unactivated-accounts-after-10-minutes-of-his-creation';

    protected $description = 'Remove account that has not been activated after 10 minutes of his creation';

    public function handle(): void
    {
        $unactivatedAccountsBefore10MinutesIds = $this->getUnactivatedUsersBefore10MinutesIds();
        if (empty($unactivatedAccountsBefore10MinutesIds)) {
            $this->info('No unactivated accounts found before 10 minutes');
            return;
        }
        $this->deleteUserAccount($unactivatedAccountsBefore10MinutesIds);
        $this->info('finish', 'success');
    }

    private function deleteUserAccount(array $unactivatedAccountIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $queryIds = implode(',', $unactivatedAccountIds);
            $sql = "
                DELETE FROM users
                WHERE id IN (".$queryIds.")";
            $st = $this->pdo->prepare($sql);
            $st->execute();
            $this->pdo->commit();
        } catch (PDOException | Exception $e) {
            $this->pdo->rollBack();
            $this->error($e->getMessage());
        }
    }

    private function getUnactivatedUsersBefore10MinutesIds(): array
    {
        $currentTime = time();

        $sql = "
            SELECT id
            FROM users
            WHERE status = 'pending' AND
                  is_deleted = false AND
                  verification_code_exp < :currentTime
        ";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            'currentTime' => $currentTime
        ]);
        return $st->fetchAll(PDO::FETCH_COLUMN) ?? [];
    }
}
