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
        HumanResource::factory()->create();
        HumanResource::factory()->count(9)->create();
    }
}
