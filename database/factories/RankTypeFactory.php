<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\RankType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankTypeFactory extends Factory
{
    protected $model = RankType::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(["Chief of Army Staff", "Chief of General Staff","Chief of brigade"]);
        $organization = Organization::all()->random();
        return [
            'organization_id' => $organization->id,
            'title_en' => $title,
            'title_bn' => $title,
            'description' => $this->faker->sentence(10,false)
        ];
    }
}
