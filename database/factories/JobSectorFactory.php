<?php

namespace Database\Factories;


use App\Models\JobSector;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobSectorFactory extends Factory
{
    protected $model = JobSector::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(["Software Industry", "Garments Sector","Banking"]);
    	return [
            'title_en' => $title,
            'title_bn' => $title,
    	];
    }
}
