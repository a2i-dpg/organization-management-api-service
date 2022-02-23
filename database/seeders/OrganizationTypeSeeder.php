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
            array('id' => '1','title' => 'Government Organization','title_en' => 'Government Organization','is_government' => '1','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '2','title' => 'Private Organization','title_en' => 'Private Organization','is_government' => '0','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '3','title' => 'Public Corporation','title_en' => 'Public Corporation','is_government' => '0','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '4','title' => 'NGO','title_en' => 'NGO','is_government' => '0','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '5','title' => 'International','title_en' => 'International','is_government' => '0','row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL)
        ];

        OrganizationType::insert($organizationTypes);

        Schema::enableForeignKeyConstraints();

    }
}
