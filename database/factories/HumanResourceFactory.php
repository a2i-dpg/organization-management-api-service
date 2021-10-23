<?php

namespace Database\Factories;

use App\Models\HumanResource;
use Illuminate\Database\Eloquent\Factories\Factory;

class HumanResourceFactory extends Factory
{
    protected $model = HumanResource::class;

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
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
            'Executive',
        ]);

        return [
            'display_order' => $this->faker->randomDigit(),
            'is_designation' => 1,
            'status' => 1,
            'title_en' => $title,
            'title' => $title,
        ];
    }
}
