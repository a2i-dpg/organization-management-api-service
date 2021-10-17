<?php

namespace Database\Factories;

use App\Models\RankType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankTypeFactory extends Factory
{
    protected $model = RankType::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->jobTitle;
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title),
            'description' => $this->faker->sentence(50, false),
            'description_en' => $this->faker->sentence(50, false)
        ];
    }
}
