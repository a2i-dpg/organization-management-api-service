<?php

namespace Database\Seeders;

use App\Models\JobSector;
use Illuminate\Database\Seeder;

class JobSectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobSector::factory()->count(3)->create();
    }
}
