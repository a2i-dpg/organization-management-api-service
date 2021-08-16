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
        OrganizationType::query()->truncate();
        Organization::query()->truncate();

        OrganizationType::factory()
            ->has(Organization::factory()->count(3))
            ->count(2)
            ->state(new Sequence(
                [
                    'title_en' => "Government org",
                    'title_bn' => "Government org",
                    'is_government' => 1,
                ],
                [
                    'title_en' => "Private org",
                    'title_bn' => "Private org",
                    'is_government' => 2,
                ]
            ))
            ->create();

        Schema::enableForeignKeyConstraints();

    }
}
