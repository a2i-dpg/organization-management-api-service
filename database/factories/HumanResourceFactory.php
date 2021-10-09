<?php

namespace Database\Factories;

use App\Models\HumanResource;
use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

class HumanResourceFactory extends Factory
{
    protected $model = HumanResource::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(["Marketing", "Sales executive"]);
        $rank = Rank::inRandomOrder()->first();

        return [
            'rank_id' => $rank->id,
            'display_order' => $this->faker->randomDigit(),
            'is_designation' => 1,
            'status' => 1,
            'title_en' => $title,
            'title' => $title,
        ];
    }
}
