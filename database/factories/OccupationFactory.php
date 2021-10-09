<?php

namespace Database\Factories;

use App\Models\Occupation;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class OccupationFactory
 * @package Database\Factories
 */
class OccupationFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Occupation::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $title = $this->faker->unique->jobTitle;
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title),
        ];
    }
}
