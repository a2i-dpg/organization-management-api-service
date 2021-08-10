<?php

namespace Database\Factories;

use App\Models\HumanResourceTemplate;
use App\Models\Organization;
use App\Models\OrganizationUnitType;
use App\Models\Rank;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class HumanResourceTemplateFactory extends Factory
{
    protected $model = HumanResourceTemplate::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(["Marketing", "Sales executive"]);

        $organization = Organization::all()->random();
        $organizationUnitType = OrganizationUnitType::all()->random();
        $parent = HumanResourceTemplate::all() ? HumanResourceTemplate::all()->random():HumanResourceTemplate::factory()->create();

        $skill = Skill::all()->toArray();
        $rank = Rank::all()->random();

        return [
            'organization_id' => $organization->id,
            'organization_unit_type_id'=>$organizationUnitType->id,
            'rank_id'=>$rank->id,
            'parent_id'=>$parent->id,
            'skill_ids'=>array_rand($skill,2),
            'display_order'=>$this->faker->randomDigit(),
            'is_designation'=>1,
            'status'=>1,
            'title_en'=>$title,
            'title_bn'=>$title,
    	];
    }
}
