<?php

namespace Database\Seeders;

use App\Models\RankType;
use Illuminate\Database\Seeder;

class RankTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RankType::factory()->count(3)->create();
    }
}
