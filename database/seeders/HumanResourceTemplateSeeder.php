<?php

namespace Database\Seeders;

use App\Models\HumanResourceTemplate;
use Faker\Factory;
use Illuminate\Database\Seeder;

class HumanResourceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HumanResourceTemplate::factory()->create();
        HumanResourceTemplate::factory()->count(9)->create();
    }
}
