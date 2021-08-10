<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Skill;

use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(["Computer skill", "Leadership skills","Management skills"]);
        $organization = Organization::all()->random();

        return [
            'organization_id' => $organization->id,
            'title_en' => $title,
            'title_bn'=>$title,
        ];

    }
}
