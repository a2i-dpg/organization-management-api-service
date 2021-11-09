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

        Occupation::query()->truncate();
        JobSector::query()->truncate();

        JobSector::factory()
            ->count(10)
            ->has(Occupation::factory()->count(10))
            ->create();

        Schema::enableForeignKeyConstraints();
    }
}
