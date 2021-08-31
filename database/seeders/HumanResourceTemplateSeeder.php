<?php

namespace Database\Seeders;

use App\Models\HumanResource;
use App\Models\HumanResourceTemplate;
use App\Models\OrganizationUnit;
use Faker\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
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
//        Schema::disableForeignKeyConstraints();
//        HumanResourceTemplate::query()->truncate();
//        HumanResource::query()->truncate();

        $organizationUnits = OrganizationUnit::all();

        foreach ($organizationUnits as $organizationUnit) {

            /** @var HumanResourceTemplate $humanisersTemplate */
            $humanisersTemplateRoot = HumanResourceTemplate::factory()
                ->state(
                    new Sequence(
                        [
                            'organization_id' => $organizationUnit->organization_id,
                            'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                            'title_en' => "Marketing Child(" . $organizationUnit->title_en . ")",
                            "title_bn" => "Marketing Child(" . $organizationUnit->title_bn . ")",
                            "parent_id" => null
                        ]
                    ))
                ->create();
            HumanResourceTemplate::factory()
                ->count(3)
                ->state(
                    new Sequence(
                        [
                            'organization_id' => $organizationUnit->organization_id,
                            'organization_unit_type_id' => $organizationUnit->organization_unit_type_id,
                            'title_en' => "Marketing Child(" . $organizationUnit->title_en . ")",
                            "title_bn" => "Marketing Child(" . $organizationUnit->title_bn . ")",
                            "parent_id" => $humanisersTemplateRoot->id
                        ]
                    ))
                ->create();

            $humanResouceRoot=HumanResource::factory()->state([
                'organization_id' => $organizationUnit->organization_id,
                'organization_unit_id' => $organizationUnit->id,
                'human_resource_template_id' => $humanisersTemplateRoot->id,
                'parent_id' => null
            ])->create();

            HumanResource::factory()
                ->count(3)
                ->state([
                'organization_id' => $organizationUnit->organization_id,
                'organization_unit_id' => $organizationUnit->id,
                'human_resource_template_id' => $humanisersTemplateRoot->id,
                'parent_id' => $humanResouceRoot->id
            ])->create();

        }
//        Schema::enableForeignKeyConstraints();

    }
}
