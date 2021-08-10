<?php

namespace Database\Factories;


use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(["Sonali Bank", "Pubali Bank", "Akij Group", "City Bank"]);
        return [
            'title_en' => $title,
            'title_bn' => $title,
            'loc_division_id' => 1,
            'domain' => 'https://www.' . $this->faker->domainName,
        ];
    }
}
