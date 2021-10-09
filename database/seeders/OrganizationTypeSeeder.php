<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use App\Models\OrganizationType;
use Illuminate\Support\Facades\Schema;


class OrganizationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        OrganizationType::factory()
            ->count(4)
            ->state(new Sequence(
                [
                    'title_en' => "Government Org",
                    'title' => "Government Org",
                    'is_government' => 1,
                ],
                [
                    'title_en' => "Private Org",
                    'title' => "Private Org",
                    'is_government' => 2,
                ],
                [
                    'title_en' => "NGO",
                    'title' => "NGO",
                    'is_government' => 2,
                ],
                [
                    'title_en' => "International",
                    'title' => "International",
                    'is_government' => 2,
                ]
            ))
            ->has(Organization::factory()->count(10))
            ->create();

        Schema::enableForeignKeyConstraints();

    }
}
