<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrganizationUnitTypeSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('organization_unit_types')->truncate();
        $organizations = Organization::pluck('id')->toArray();

        DB::table('organization_unit_types')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'organization_id' => $organizations[array_rand($organizations)],
                    'title_en' => 'Mobile Banking',
                    'title_bn' => 'মোবাইল ব্যাংকিং',
                    'row_status' => 1,
                ),
            1 =>
                array(
                    'id' => 2,
                    'organization_id' => $organizations[array_rand($organizations)],
                    'title_en' => 'Payment Method',
                    'title_bn' => 'রকেট মোবাইল ব্যাংকিং',
                    'row_status' => 1,
                ),
        ));

        Schema::enableForeignKeyConstraints();
    }
}
