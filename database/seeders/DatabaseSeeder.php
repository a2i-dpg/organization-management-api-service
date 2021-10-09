<?php

namespace Database\Seeders;

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
            ServiceSeeder::class,
            SkillSeeder::class,
            JobSectorSeeder::class,
            GeoLocationDatabaseSeeder::class,
            OrganizationTypeSeeder::class,
            OrganizationSeeder::class,
            HumanResourceTemplateSeeder::class,
        ]);
    }
}
