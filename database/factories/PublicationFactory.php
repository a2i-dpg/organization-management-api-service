<?php

namespace Database\Factories;

use App\Models\IndustryAssociation;
use App\Models\Publication;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PublicationFactory extends Factory
{
    protected $model = Publication::class;

    public function definition(): array
    {
        $industryAssociationId = IndustryAssociation::inRandomOrder()->first();

        return [
            'title' => $this->faker->name,
            'title_en' => $this->faker->name,
            'description' => $this->faker->sentence,
            'description_en' => $this->faker->sentence,
            'image_path' => $this->faker->sentence,
            'author' => $this->faker->name(),
            'author_en' => $this->faker->name(),
            'industry_association_id' => $industryAssociationId,
            'created_by' => $this->faker->numberBetween(1, 10000),
            'updated_by' => $this->faker->numberBetween(1, 10000),
            'row_status' => $this->faker->numberBetween(0, 1),
            'created_at' => $this->faker->date(),
            'updated_at' => Carbon::now()
        ];
    }
}
