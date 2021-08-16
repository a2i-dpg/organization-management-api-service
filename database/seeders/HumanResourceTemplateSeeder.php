<?php

namespace Database\Seeders;

use App\Models\HumanResource;
use App\Models\HumanResourceTemplate;
use App\Models\OrganizationUnit;
use Faker\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
        HumanResourceTemplate::query()->truncate();
        HumanResource::query()->truncate();

        $organizationUnits = OrganizationUnit::all();

        foreach ($organizationUnits as $organizationUnit) {

            /** @var HumanResourceTemplate $humanisersTemplate */
            $humanisersTemplate = HumanResourceTemplate::factory()
                ->state(
                    new Sequence(
                        [
                            'organization_id' => $organizationUnit->organization_id,
                            'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                            'title_en' => "Marketing Child(" . $organizationUnit->title_en . ")",
                            "title_bn" => "Marketing Child(" . $organizationUnit->title_bn . ")",
                            "parent_id" => HumanResourceTemplate::factory()
                                ->state(
                                    [
                                        'organization_id' => $organizationUnit->organization_id,
                                        'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                                        'title_en' => "Marketing(" . $organizationUnit->title_en . ")",
                                        "title_bn" => "Marketing(" . $organizationUnit->title_bn . ")",
                                    ]
                                )
                        ],
                        [
                            'organization_id' => $organizationUnit->organization_id,
                            'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                            'title_en' => "Sales executive Child(" . $organizationUnit->title_en . ")",
                            "title_bn" => "Sales executive Child(" . $organizationUnit->title_en . ")",
                            "parent_id" => HumanResourceTemplate::factory()
                                ->state(
                                    [
                                        'organization_id' => $organizationUnit->organization_id,
                                        'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                                        'title_en' => "Sales executive(" . $organizationUnit->title_en . ")",
                                        "title_bn" => "Sales executive(" . $organizationUnit->title_en . ")",
                                    ]
                                )
                        ]
                    ))
                ->create();

            HumanResource::factory()->state([
                'organization_id' => $organizationUnit->organization_id,
                'organization_unit_id' => $organizationUnit->id,
                'human_resource_template_id' => $humanisersTemplate->id,
                'parent_id' => HumanResource::factory()->state([
                    'organization_id' => $organizationUnit->organization_id,
                    'organization_unit_id' => $organizationUnit->id,
                    'human_resource_template_id' => $humanisersTemplate->id
                ])
            ])->create();

        }
        Schema::enableForeignKeyConstraints();

    }
}
