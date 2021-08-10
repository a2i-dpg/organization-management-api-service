<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Rank;
use App\Models\RankType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankFactory extends Factory
{
    protected $model = Rank::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(["জেনারেল", "লেফটেন্যান্ট জেনারেল","ব্রিগেডিয়ার জেনারেল"]);
        $organization = Organization::all()->random();
        $rankType = RankType::all()->random();
        return [
            'organization_id' => $organization->id,
            'rank_type_id' => $rankType->id,
            'title_en' => $title,
            'title_bn' => $title,
            'grade' => $this->faker->randomDigit(),
            'display_order'=>$this->faker->randomDigit()
        ];
    }
}
