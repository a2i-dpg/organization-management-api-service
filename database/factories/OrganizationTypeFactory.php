<?php

namespace Database\Factories;

use App\Models\OrganizationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationTypeFactory extends Factory
{
    protected $model = OrganizationType::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(["Government org","private org"]);
    	return [
            'title_en'=>$title,
    	    'title_bn'=>$title,
            'is_government'=>$this->faker->randomElement([0,1]),
    	];
    }
}
