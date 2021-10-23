<?php

namespace Database\Factories;

use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankFactory extends Factory
{
    protected $model = Rank::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->jobTitle;
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title),
        ];
    }
}
