<?php

namespace Database\Factories;

use App\Models\OrganizationUnit;
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
    protected $model = OrganizationUnit::class;

    /**
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        $title = $this->faker->randomElement(["Development", "Management", "Support", "Marketing", "Sales", "Finance ", 'HR', 'Admin', 'Divisional', 'Zonal']);
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title)
        ];
    }
}
