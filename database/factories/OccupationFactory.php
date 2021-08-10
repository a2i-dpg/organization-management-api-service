<?php

namespace Database\Factories;

use App\Models\JobSector;
use App\Models\Occupation;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class OccupationFactory
 * @package Database\Factories
 */
class OccupationFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Occupation::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $title = $this->faker->randomElement(["Software Engineer", "Banker","Teacher","Doctor","Civil Engineer"]);

        $jobSector = JobSector::all()->random();

        return [
            'job_sector_id' => $jobSector->id,
            'title_en' => $title,
            'title_bn' => $title,
        ];
    }
}
