<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ProfessionsSeed extends Seeder
{
    public function run()
    {
        $professions = $this->extractProfessionsFromImportFile();
        foreach ($professions as $profession) {
            $professionAlreadyExist = $this->checkIfProfessionAlreadyExist($profession);
            if (!$professionAlreadyExist) {
                DB::table('professions')->insert([
                    'uuid' => Uuid::uuid4()->toString(),
                    'name' => $profession->name,
                    'created_at' => now(),
                ]);
            }
        }
    }

    private function extractProfessionsFromImportFile()
    {
        $file = fopen(base_path("public/importData/professions.json"), 'r');
        $data = fread($file, filesize(base_path("public/importData/professions.json")));
        fclose($file);
        return json_decode($data);
    }

    /**
     * @param mixed $profession
     * @return bool
     */
    public function checkIfProfessionAlreadyExist(mixed $profession): bool
    {
        return DB::table('professions')->where('name', $profession->name)->count() > 0;
    }
}
