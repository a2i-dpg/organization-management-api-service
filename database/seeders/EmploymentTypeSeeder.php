<?php

namespace Database\Seeders;

use App\Models\EmploymentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        EmploymentType::query()->truncate();

        $employmentTypes = [
            array('id' => '1','code' => 'FULL','title_en' => 'Full-Time','title' => 'ফুল টাইম','row_status' => '1','created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '2','code' => 'PART','title_en' => 'Part-Time','title' => 'খন্ডকালীন','row_status' => '1','created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '3','code' => 'APPREN','title_en' => 'Apprentices and Trainees','title' => 'শিক্ষানবিশ এবং প্রশিক্ষণার্থী','row_status' => '1','created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '4','code' => 'TEMP','title_en' => 'Temporary','title' => 'সাময়িক','row_status' => '1','created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '5','code' => 'SEAS','title_en' => 'Seasonal','title' => 'মৌসুমী','row_status' => '1','created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL),
            array('id' => '6','code' => 'LEAS','title_en' => 'Leased','title' => 'ইজারা ভিত্তিক','row_status' => '1','created_at' => NULL,'updated_at' => NULL,'deleted_at' => NULL)
        ];

        EmploymentType::insert($employmentTypes);

        Schema::enableForeignKeyConstraints();

    }
}
