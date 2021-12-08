<?php

namespace Database\Seeders;

use App\Models\IndustryAssociation;
use Faker\Factory;
use Illuminate\Database\Seeder;

class IndustryAssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IndustryAssociation::factory()->count(10)->create();
    }
}
