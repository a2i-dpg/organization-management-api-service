<?php

namespace Database\Factories;

use App\Models\HumanResourceTemplate;
use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

class HumanResourceTemplateFactory extends Factory
{
    protected $model = HumanResourceTemplate::class;

    public function definition(): array
    {
        $rank = Rank::inRandomOrder()->first();
        $title = $this->faker->unique()->jobTitle;
        return [
            'title_en' => ucfirst($title),
            'title_bn' => ucfirst($title),
            'rank_id' => $rank->id,
            'display_order' => $this->faker->randomDigit(),
            'is_designation' => 1,
            'status' => 1

        ];
    }
}
