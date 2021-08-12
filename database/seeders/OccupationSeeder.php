<?php

namespace Database\Seeders;

use App\Models\Occupation;
use Illuminate\Database\Seeder;

/**
 * Class OccupationSeeder
 * @package Database\Seeders
 */
class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Occupation::factory()->count(10)->create();
    }
}
