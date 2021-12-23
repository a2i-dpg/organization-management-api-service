<?php

namespace Database\Factories;

use App\Models\ContactInfo;
use App\Models\IndustryAssociation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactInfoFactory extends Factory
{
    protected $model = ContactInfo::class;

    public function definition(): array
    {
        $industryAssociationId = IndustryAssociation::inRandomOrder()->first();

        return [
            'title' => $this->faker->name,
            'title_en' => $this->faker->name,
            'industry_association_id' => $industryAssociationId,
            'mobile'=>$this->faker->regexify('01[3-9]\d{8}'),
            'phone'=>$this->faker->regexify('0\d{9}'),
            'email' => $this->faker->unique()->safeEmail,
            'created_by' => $this->faker->numberBetween(1,10000),
            'updated_by' => $this->faker->numberBetween(1,10000),
            'row_status' => $this->faker->numberBetween(0,1),
            'created_at' => $this->faker->date(),
            'updated_at' => Carbon::now()
        ];
    }
}
