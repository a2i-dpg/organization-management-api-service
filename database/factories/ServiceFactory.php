<?php

namespace Database\Factories;

use App\Models\Organization;
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
        $title = $this->faker->randomElement(["Web Development", "Graphic design","video editing"]);
        $organization = Organization::all()->random();
    	return [
            'title_en' => $title,
            'title_bn' => $title,
            'organization_id' => $organization->id
    	];
    }
}
