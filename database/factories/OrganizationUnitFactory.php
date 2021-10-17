<?php

namespace Database\Factories;

use App\Models\OrganizationUnit;
use App\Services\LocationManagementServices\LocationSeederHelper;
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
     * @throws \Exception
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->company;

        $len = count(LocationSeederHelper::$data);
        $index = random_int(0, $len - 1);
        $location = LocationSeederHelper::$data[$index];

        $designation = $this->faker->randomElement(["DC", "UNO", "Marketing Executive", "Sales Executive", "Finance Executive ", 'HR Manager', 'Admin Manager']);
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title),
            'employee_size' => random_int(5, 50),
            'loc_division_id' => $location['loc_division_id'],
            'loc_district_id' => $location['loc_district_id'],
            'loc_upazila_id' => $location['loc_upazila_id'],
            'location_latitude' => $location['location_longitude'],
            'location_longitude' => $location['location_longitude'],
            'address' => $this->faker->address(),
            'address_en' => $this->faker->address(),
            'mobile' => $this->faker->numerify('017########'),
            'email' => $this->faker->companyEmail(),
            'fax_no' => "+123456",
            'contact_person_name' => $this->faker->name(),
            'contact_person_name_en' => $this->faker->name(),
            'contact_person_mobile' => $this->faker->numerify('017########'),
            'contact_person_email' => $this->faker->safeEmail(),
            'contact_person_designation' => $designation,
            'contact_person_designation_en' => $designation,

        ];
    }
}
