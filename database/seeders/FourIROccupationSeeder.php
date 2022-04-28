<?php

namespace Database\Seeders;

use App\Models\FourIROccupation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FourIROccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        FourIROccupation::query()->truncate();

        $occupations = [
            array('id' => '1', 'title_en' => 'Occupation 1', 'title' => 'Occupation 1', 'row_status' => 1, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '2', 'title_en' => 'Occupation 2', 'title' => 'Occupation 2', 'row_status' => 1, 'updated_at' => NULL, 'deleted_at' => NULL),
        ];

        FourIROccupation::insert($occupations);

        Schema::enableForeignKeyConstraints();
    }
}
