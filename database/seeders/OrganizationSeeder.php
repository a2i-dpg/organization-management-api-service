<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrganizationSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('organizations')->truncate();

        DB::table('organizations')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'title_en' => 'BDBL',
                    'title_bn' => '-ফিনান্স',
                    'address' => 'Dhaka-1212',
                    'organization_type_id' => '1',
                    'row_status' => 1,
                ),
            1 =>
                array(
                    'id' => 2,
                    'title_en' => 'City Group',
                    'title_bn' => '-ফিনান্স',
                    'address' => 'Dhaka-1212',
                    'organization_type_id' => '2',
                    'row_status' => 1,
                ),
            2 =>
                array(
                    'id' => 3,
                    'title_en' => 'BTCL',
                    'title_bn' => '-ফিনান্স',
                    'address' => 'Dhaka-1212',
                    'organization_type_id' => '1',
                    'row_status' => 1,
                ),
            3 =>
                array(
                    'id' => 4,
                    'title_en' => 'BRTC',
                    'title_bn' => '-ফিনান্স',
                    'address' => 'Dhaka-1212',
                    'organization_type_id' => '2',
                    'row_status' => 1,
                ),

        ));

        Schema::enableForeignKeyConstraints();
    }
}
