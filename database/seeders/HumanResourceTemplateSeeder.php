<?php

namespace Database\Seeders;

use App\Models\HumanResource;
use App\Models\HumanResourceTemplate;
use App\Models\OrganizationUnit;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Collection;
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

        $skillIdCollection = Skill::all()->pluck('id');

        $organizationUnits = OrganizationUnit::all();

        foreach ($organizationUnits as $organizationUnit) {

            /** @var HumanResourceTemplate $humanisersTemplateRoot */
            $humanisersTemplateRoot = HumanResourceTemplate::factory()
                ->state([
                    'organization_id' => $organizationUnit->organization_id,
                    'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                ])
                ->create();

            HumanResourceTemplate::factory()
                ->count(5)
                ->state([
                    'organization_id' => $organizationUnit->organization_id,
                    'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                    "parent_id" => $humanisersTemplateRoot->id
                ])
                ->create();

            /** @var HumanResource $humanResourceRoot */
            /** @var Collection $humanResourceRoots */
            $humanResourceRoots = HumanResource::factory()
                ->count(2)
                ->state([
                    'organization_id' => $organizationUnit->organization_id,
                    'organization_unit_id' => $organizationUnit->id,
                    'parent_id' => null
                ])
                ->create();

            foreach ($humanResourceRoots as $humanResourceRoot) {
                HumanResource::factory()
                    ->count(10)
                    ->state([
                        'organization_id' => $organizationUnit->organization_id,
                        'organization_unit_id' => $organizationUnit->id,
                        'parent_id' => $humanResourceRoot->id
                    ])
                    ->create();
            }
        }

        $humanResources = HumanResource::all();

        foreach ($humanResources as $humanResource) {
            /** @var HumanResource $humanResource */
            $humanResource->skills()->sync($skillIdCollection->random(3)->all());
        }

        $humanResourceTemplates = HumanResourceTemplate::all();
        foreach ($humanResourceTemplates as $humanResourceTemplate) {
            /** @var HumanResourceTemplate $humanResourceTemplate */
            $humanResourceTemplate->skills()->sync($skillIdCollection->random(3)->all());
        }

        Schema::enableForeignKeyConstraints();

    }
}
