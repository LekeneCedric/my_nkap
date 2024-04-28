<?php

namespace App\Profession\Tests\e2e;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GetAllProfessionActionTest extends TestCase
{
    use RefreshDatabase;
    const GET_PROFESSIONS_ROUTES = 'api/professions/all';
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_all_professions()
    {
        $numberOfProfessions = 3;
        $this->buildSUT(count: $numberOfProfessions);

        $response = $this->getJson(self::GET_PROFESSIONS_ROUTES);

        $response->assertOk();
        $this->assertTrue($response['status']);
        $this->assertCount($numberOfProfessions, $response['professions']);
    }

    private function buildSUT(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('professions')->insert([
                'uuid' => Uuid::uuid4()->toString(),
                'name' => 'Software Engineer'.$i+1,
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
