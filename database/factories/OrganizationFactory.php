<?php

namespace Database\Factories;


use App\Models\Organization;
use App\Models\OrganizationType;
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
        $title = $this->faker->randomElement(["Sonali Bank", "Pubali Bank", "Akij Group", "City Bank"]);
        $organization_type = OrganizationType::all()->random();
        return [
            'organization_type_id' => $organization_type->id,

            'title_en' => $title,
            'title_bn' => $title,

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

            'contact_person_name' => $this->faker->name(),
            'contact_person_mobile' =>"01758393749",
            'contact_person_email' => $this->faker->safeEmail(),
            'contact_person_designation' => "HR",


        ];
    }
}
