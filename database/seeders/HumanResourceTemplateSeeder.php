<?php

namespace Database\Seeders;

use App\Models\HumanResource;
use App\Models\HumanResourceTemplate;
use App\Models\OrganizationUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class HumanResourceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $organizationUnits = OrganizationUnit::all();

        foreach ($organizationUnits as $organizationUnit) {

            /** @var HumanResourceTemplate $humanisersTemplate */
            $humanisersTemplateRoot = HumanResourceTemplate::factory()
                ->state(function (array $attributes) use ($organizationUnit) {
                    return [
                        'organization_id' => $organizationUnit->organization_id,
                        'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                    ];
                })
                ->create();

            HumanResourceTemplate::factory()
                ->count(5)
                ->state(function (array $attributes) use ($organizationUnit, $humanisersTemplateRoot) {
                    return [
                        'organization_id' => $organizationUnit->organization_id,
                        'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                        "parent_id" => $humanisersTemplateRoot->id
                    ];
                })
                ->create();

            $humanResouceRoots = HumanResource::factory()
                ->count(2)
                ->state([
                    'organization_id' => $organizationUnit->organization_id,
                    'organization_unit_id' => $organizationUnit->id,
                    'parent_id' => null
                ])
                ->create();

            foreach ($humanResouceRoots as $humanResouceRoot) {
                HumanResource::factory()
                    ->count(10)
                    ->state([
                        'organization_id' => $organizationUnit->organization_id,
                        'organization_unit_id' => $organizationUnit->id,
                        'parent_id' => $humanResouceRoot->id
                    ])
                    ->create();
            }
        }

        Schema::enableForeignKeyConstraints();

    }
}
