<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactUsFactory extends Factory
{
    protected $model = \App\Models\ContactUs::class;

    public function definition(): array
    {
    	return [
            'title' => $this->faker->name,
            'title_en' => $this->faker->name,
            'industry_association_id' => $this->faker->randomDigit(),
            'mobile'=>$this->faker->regexify('01[3-9]\d{8}'),
            'phone'=>$this->faker->regexify('880\d{9}'),
            'email' => $this->faker->unique()->safeEmail,
            'created_by' => $this->faker->numberBetween(1,10000),
            'updated_by' => $this->faker->numberBetween(1,10000),
            'row_status' => $this->faker->numberBetween(0,1),
            'deleted_at' => $this->faker->date(),
            'created_at' => $this->faker->date(),
            'updated_at' => Carbon::now()
        ];
    }
}
