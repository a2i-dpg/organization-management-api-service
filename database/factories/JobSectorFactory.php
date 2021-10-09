<?php

namespace Database\Factories;


use App\Models\JobSector;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobSectorFactory extends Factory
{
    protected $model = JobSector::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->jobTitle;
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title),
        ];
    }
}
