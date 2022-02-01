<?php

namespace Database\Seeders;

use App\Models\OtherBenefit;
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
            TradeSeeder::class,
            ServiceSeeder::class,
            SkillSeeder::class,
            JobSectorSeeder::class,
            GeoLocationDatabaseSeeder::class,
            OrganizationTypeSeeder::class,
            EducationalInstitutionSeeder::class,
            OtherBenefitSeeder::class,
            AreaOfExperienceSeeder::class,
            AreaOfBusinessSeeder::class,
            EmploymentTypeSeeder::class,
            EduGroupSeeder::class,
            EduBoardSeeder::class,
            EducationLevelSeeder::class,
            ExamDegreeSeeder::class,
            SubTradeSeeder::class,
            IndustryAssociationSeeder::class,
            OrganizationSeeder::class,
            HumanResourceTemplateSeeder::class,
            PublicationSeeder::class,
            ContactInfoSeeder::class,
        ]);
    }
}
