<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationUnit;
use App\Models\OrganizationUnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class OrganizationUnitFactory
 * @package Database\Factories
 */
class OrganizationUnitFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = OrganizationUnit::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $title = $this->faker->randomElement(["Mobile Banking", "Payment Method"]);
        $organizationUnitType = OrganizationUnitType::all()->random();
        $organization = Organization::all()->random();
    	return [
            'title_en' => $title,
            'title_bn' => $title,

            'organization_unit_type_id' => $organizationUnitType->id,
            'organization_id' => $organization->id,

            'loc_division_id' => 1,
            'loc_district_id' => 1,
            'loc_upazila_id' => 1,

            'address' => $this->faker->address(),
            'mobile' => "01758393749",
            'email' => $this->faker->companyEmail(),
            'fax_no' => "+123456",

            'contact_person_name' => $this->faker->name(),
            'contact_person_mobile' =>"01758393749",
            'contact_person_email' => $this->faker->safeEmail(),
            'contact_person_designation' => "HR",

    	];
    }
}
