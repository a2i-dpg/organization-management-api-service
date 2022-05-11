<?php

namespace Database\Factories;

use App\Models\FourIRSector;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class FourIRSectorFactory extends Factory
{
    protected $model = FourIRSector::class;

    public function definition(): array
    {


        return [
            'title' => $this->faker->name,
            'title_en' => $this->faker->name,
            'created_by' => $this->faker->numberBetween(1,10000),
            'updated_by' => $this->faker->numberBetween(1,10000),
            'row_status' => $this->faker->numberBetween(0,1),
            'created_at' => $this->faker->date(),
            'updated_at' => Carbon::now()
        ];
    }
}
