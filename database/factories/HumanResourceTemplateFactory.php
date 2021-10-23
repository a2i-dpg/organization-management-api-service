<?php

namespace Database\Factories;

use App\Models\HumanResourceTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class HumanResourceTemplateFactory extends Factory
{
    protected $model = HumanResourceTemplate::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement([
            "Marketing Executive",
            "Assistant Marketing Executive",
            "Junior Marketing Executive",
            "Sales Executive",
            "Assistant Sales Executive",
            "Junior Sales Executive",
            "Finance Executive",
            "Assistant Finance Executive",
            "Junior Finance Executive",
            'HR Manager',
            'Assistant HR Manager',
            'Admin Manager',
            'Assistant Admin Manager',
            'Executive'
        ]);
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title),
            'display_order' => $this->faker->randomDigit(),
            'is_designation' => 1,
            'status' => 1
        ];
    }
}
