<?php

namespace Database\Factories;


use App\Models\Organization;
use App\Models\OrganizationUnit;
use App\Models\OrganizationUnitService;
use App\Models\Service;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class OrganizationUnitServiceFactory
 * @package Database\Factories
 */
class OrganizationUnitServiceFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = OrganizationUnitService::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $organizationUnit = OrganizationUnit::all()->random();
        $organization = Organization::all()->random();
        $service = Service::all()->random();

        return [
            'organization_id' => $organization->id,
            'organization_unit_id' => $organizationUnit->id,
            'service_id' => $service->id

        ];
    }
}
