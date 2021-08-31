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
            LocDivisionsTableSeeder::class,
            LocDistrictsTableSeeder::class,
            LocUpazilasTableSeeder::class,
            OrganizationTypeSeeder::class,
            OrganizationSeeder::class,
            ServiceSeeder::class,
            JobSectorSeeder::class,
            SkillSeeder::class,
            HumanResourceTemplateSeeder::class,
        ]);
    }
}
