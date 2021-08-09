<?php

namespace Database\Factories;

use App\Models\OrganizationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationTypeFactory extends Factory
{
    protected $model = OrganizationType::class;

    public function definition(): array
    {
    	return [
            'title_en'=>$this->faker->randomElement(["Government org","private org"]),
    	    'title_bn'=>$this->faker->randomElement(["Government org","private org"]),
            'is_government'=>$this->faker->randomElement([0,1]),

    	];
    }
}
