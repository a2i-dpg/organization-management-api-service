<?php

namespace Database\Factories;


use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class OrganizationFactory
 * @package Database\Factories
 */
class OrganizationFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Organization::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->company;
        return [
            'title_en' => ucfirst($title),
            'title_bn' => ucfirst($title),
            'loc_division_id' => 1,
            'loc_district_id' => 1,
            'loc_upazila_id' => 1,
            'address' => $this->faker->address(),
            'mobile' => "01758393749",
            'email' => $this->faker->companyEmail(),
            'fax_no' => "+123456",
            'description' => $this->faker->sentence(),
            'logo' => "logo.jpg",
            'domain' => 'https://www.' . $this->faker->unique()->domainName(),
            'contact_person_name' => ucfirst($this->faker->name()),
            'contact_person_mobile' => "01758393749",
            'contact_person_email' => $this->faker->safeEmail(),
            'contact_person_designation' => ucfirst($this->faker->unique()->jobTitle)
        ];
    }
}
