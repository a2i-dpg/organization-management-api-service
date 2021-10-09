<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ServiceFactory
 * @package Database\Factories
 */
class ServiceFactory extends Factory
{

    /**
     * @var string
     */
    protected $model = Service::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->jobTitle;
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title),
        ];
    }
}
