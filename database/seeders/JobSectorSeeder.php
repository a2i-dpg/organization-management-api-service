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
        JobSector::factory()
            ->count(10)
            ->has(Occupation::factory()->count(10))
            ->create();
        Schema::disableForeignKeyConstraints();
    }
}
