<?php

namespace Database\Factories;


use App\Models\BaseModel;
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
        $title = ucfirst($this->faker->unique()->company);
        $orgAddress = $this->faker->address();
        $orgDescription = $this->faker->sentence();
        $len = count(LocationSeederHelper::$data);
        $index = random_int(0, $len - 1);
        $location = LocationSeederHelper::$data[$index];

        $officeHeadName = ucfirst($this->faker->name());
        $contactPersonName = ucfirst($this->faker->name());
        $contactPersonJobTitle = ucfirst($this->faker->unique()->jobTitle);

        return [
            'title_en' => $title,
            'title' => $title,
            'loc_division_id' => $location['loc_division_id'],
            'loc_district_id' => $location['loc_district_id'],
            'loc_upazila_id' => $location['loc_upazila_id'],
            'location_latitude' => $location['location_longitude'],
            'location_longitude' => $location['location_longitude'],
            'address' => $orgAddress,
            'address_en' => $orgAddress,
            'mobile' => $this->faker->numerify('017########'),
            'email' => $this->faker->companyEmail(),
            'fax_no' => $this->faker->phoneNumber(),
            'description' => $orgDescription,
            'description_en' => $orgDescription,
            'logo' => "logo.jpg",
            'domain' => 'https://www.' . $this->faker->unique()->domainName(),

            'name_of_the_office_head' => $officeHeadName,
            'name_of_the_office_head_en' => $officeHeadName,
            'name_of_the_office_head_designation' => 'CEO',
            'name_of_the_office_head_designation_en' => 'CEO',

            'contact_person_name_en' => $contactPersonName,
            'contact_person_mobile' => $this->faker->numerify('017########'),
            'contact_person_email' => $this->faker->safeEmail(),
            'contact_person_designation' => $contactPersonJobTitle,
            'contact_person_designation_en' => $contactPersonJobTitle,
            'row_status' => BaseModel::ROW_STATUS_ACTIVE
        ];
    }
}
