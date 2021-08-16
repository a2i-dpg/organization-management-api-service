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
            OrganizationTypeSeeder::class,
            OrganizationSeeder::class,
            ServiceSeeder::class,
            JobSectorSeeder::class,
            SkillSeeder::class,
            HumanResourceTemplateSeeder::class,
        ]);
    }
}
