<?php

namespace Database\Seeders;

use App\Models\HumanResourceTemplate;
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
        HumanResourceTemplate::factory()->count(10)->create();
    }
}
