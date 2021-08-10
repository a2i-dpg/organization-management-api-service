<?php

namespace Database\Seeders;


use App\Models\OrganizationUnitType;
use Illuminate\Database\Seeder;

class OrganizationUnitTypeSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        OrganizationUnitType::factory()->count(3)->create();
    }
}
