<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class IndustryAssociationCodePessimisticLockingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        Schema::disableForeignKeyConstraints();

        DB::table('industry_association_code_pessimistic_lockings')->truncate();

        DB::table('industry_association_code_pessimistic_lockings')->insert(array(
            array(
                'last_incremental_value' => 3,
            ),
        ));

        Schema::enableForeignKeyConstraints();


    }
}
