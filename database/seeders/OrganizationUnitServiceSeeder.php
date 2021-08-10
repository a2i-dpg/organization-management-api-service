<?php

namespace Database\Seeders;

use App\Models\OrganizationUnitService;
use Illuminate\Database\Seeder;

class OrganizationUnitServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrganizationUnitService::factory()->count(3)->create();
    }
}
