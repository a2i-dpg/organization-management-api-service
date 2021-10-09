<?php

namespace Database\Factories;


use App\Models\Organization;
use App\Services\LocationManagementServices\LocationSeederHelper;
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
     * @throws \Exception
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->company;
        $len = count(LocationSeederHelper::$data);
        $index = random_int(0, $len - 1);
        $location = LocationSeederHelper::$data[$index];

        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title),
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
            'description' => $this->faker->sentence(),
            'description_en' => $this->faker->sentence(),
            'logo' => "logo.jpg",
            'domain' => 'https://www.' . $this->faker->unique()->domainName(),
            'contact_person_name' => ucfirst($this->faker->name()),
            'contact_person_name_en' => ucfirst($this->faker->name()),
            'contact_person_mobile' => $this->faker->numerify('017########'),
            'contact_person_email' => $this->faker->safeEmail(),
            'contact_person_designation' => ucfirst($this->faker->unique()->jobTitle),
            'contact_person_designation_en' => ucfirst($this->faker->unique()->jobTitle)
        ];
    }
}
