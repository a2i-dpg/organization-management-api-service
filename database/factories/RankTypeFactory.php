<?php

namespace Database\Factories;

use App\Models\RankType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankTypeFactory extends Factory
{
    protected $model = RankType::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(10, false)
        ];
    }
}
