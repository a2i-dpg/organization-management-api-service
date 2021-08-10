<?php

namespace Database\Factories;

use App\Model;
use App\Models\Organization;
use App\Models\OrganizationUnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class OrganizationUnitTypeFactory
 * @package Database\Factories
 */
class OrganizationUnitTypeFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = OrganizationUnitType::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $organization = Organization::all()->random();
        $title = $this->faker->randomElement(["Mobile Banking", "Payment Method"]);
        return [
            'organization_id' => $organization->id,
            'title_en' => $title,
            'title_bn' => $title,
        ];
    }
}
