<?php

namespace Database\Factories;

use App\Models\OrganizationUnit;
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
        return [
            'employee_size' => $this->faker->randomDigit(),
            'loc_division_id' => 1,
            'loc_district_id' => 1,
            'loc_upazila_id' => 18,
            'address' => $this->faker->address(),
            'mobile' => "01758393749",
            'email' => $this->faker->companyEmail(),
            'fax_no' => "+123456",
            'contact_person_name' => $this->faker->name(),
            'contact_person_mobile' => "01758393749",
            'contact_person_email' => $this->faker->safeEmail(),
            'contact_person_designation' => "HR",

        ];
    }
}
