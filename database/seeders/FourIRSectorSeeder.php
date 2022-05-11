<?php

namespace Database\Seeders;

use App\Models\FourIRSector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FourIRSectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        FourIRSector::query()->truncate();

        $occupations = [
            array('id' => '1', 'title_en' => 'Sector 1', 'title' => 'Sector 1', 'row_status' => 1, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '2', 'title_en' => 'Sector 2', 'title' => 'Sector 2', 'row_status' => 1, 'updated_at' => NULL, 'deleted_at' => NULL),
        ];

        FourIRSector::insert($occupations);

        Schema::enableForeignKeyConstraints();
    }
}
