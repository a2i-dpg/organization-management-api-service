<?php

namespace Database\Seeders;

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

        $organizationTypes = [
            [
                'title_en' => "Government Organization",
                'title' => "Government Organization",
                'is_government' => 1,
            ],
            [
                'title_en' => "Private Organization",
                'title' => "Private Organization",
                'is_government' => 0,
            ],
            [
                'title_en' => "Public Corporation",
                'title' => "Public Corporation",
                'is_government' => 0,
            ],
            [
                'title_en' => "NGO",
                'title' => "NGO",
                'is_government' => 0,
            ],
            [
                'title_en' => "International",
                'title' => "International",
                'is_government' => 0,
            ]
        ];

        OrganizationType::insert($organizationTypes);

        Schema::enableForeignKeyConstraints();

    }
}
