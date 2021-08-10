<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\OrganizationUnitType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            OrganizationTypeSeeder::class,
            OrganizationSeeder::class,
            OrganizationUnitTypeSeeder::class,
            OrganizationUnitSeeder::class,
            ServiceSeeder::class,
            OrganizationUnitServiceSeeder::class,
            RankTypeSeeder::class,
            RankSeeder::class,
            JobSectorSeeder::class,
            SkillSeeder::class,
            OccupationSeeder::class,
            HumanResourceTemplateSeeder::class,
            HumanResourceSeeder::class
        ]);
    }
}
