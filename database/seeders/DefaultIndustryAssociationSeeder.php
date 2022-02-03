<?php

namespace Database\Seeders;

use App\Models\IndustryAssociation;
use App\Services\CommonServices\CodeGenerateService;
use Faker\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DefaultIndustryAssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        IndustryAssociation::query()->truncate();
        IndustryAssociation::factory()
            ->state(new Sequence(
                [
                    "code" => CodeGenerateService::getIndustryAssociationCode()
                ],
                [
                    "code" => CodeGenerateService::getIndustryAssociationCode()
                ]
            ))
            ->count(2)->create();

        Schema::enableForeignKeyConstraints();
    }
}
