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
            IndustryAssociationTradeSeeder::class,
            IndustryAssociationSeeder::class,
            ServiceSeeder::class,
            SkillSeeder::class,
            JobSectorSeeder::class,
            GeoLocationDatabaseSeeder::class,
            OrganizationTypeSeeder::class,
            OrganizationSeeder::class,
            HumanResourceTemplateSeeder::class,
            PublicationSeeder::class,
            ContactInfoSeeder::class,
            EducationalInstitutionSeeder::class
        ]);
    }
}
