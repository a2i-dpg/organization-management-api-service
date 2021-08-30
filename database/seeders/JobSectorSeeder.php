<?php

namespace Database\Seeders;

use App\Models\JobSector;
use App\Models\Occupation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class JobSectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        JobSector::query()->truncate();
        Occupation::query()->truncate();
        JobSector::factory()->has(Occupation::factory()->count(2))
            ->count(20)->create();
        Schema::disableForeignKeyConstraints();
    }
}
