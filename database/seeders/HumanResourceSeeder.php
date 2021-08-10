<?php

namespace Database\Seeders;

use App\Models\HumanResource;
use Illuminate\Database\Seeder;

/**
 * Class HumanResourceSeeder
 * @package Database\Seeders
 */
class HumanResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HumanResource::factory()->count(10)->create();
    }
}
