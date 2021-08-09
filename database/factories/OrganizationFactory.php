<?php

namespace Database\Factories;


use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'title_en' => $this->faker->randomElement(["Government org", "private org"]),
            'title_bn' => $this->faker->randomElement(["Government org", "private org"]),
            'domain' => 'https://' . $this->faker->domainName,
        ];
    }
}
