<?php

namespace Database\Seeders;

use App\Models\OtherBenefit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class OtherBenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        OtherBenefit::query()->truncate();

        $otherBenefits = [
            array('id' => '1', 'title' => 'T/A', 'title_en' => 'T/A', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '2', 'title' => 'মোবাইল বিল', 'title_en' => 'Mobile bill', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '3', 'title' => 'পেনশন পলিসি', 'title_en' => 'Pension policy', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '4', 'title' => 'সফর ভাতা', 'title_en' => 'Tour allowance', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '5', 'title' => 'ক্রেডিট কার্ড', 'title_en' => 'Credit card', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '6', 'title' => 'চিকিৎসা ভাতা', 'title_en' => 'Medical allowance', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '7', 'title' => 'পারফর্ম্যান্স বোনাস', 'title_en' => 'Performance bonus', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '8', 'title' => 'প্রফিট শেয়ার', 'title_en' => 'Profit share', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '9', 'title' => 'প্রভিডেন্ট ফান্ড', 'title_en' => 'Provident fund', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '10', 'title' => 'সাপ্তাহিক দুইদিন ছুটি', 'title_en' => 'Weekly 2 holidays', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '11', 'title' => 'বীমা', 'title_en' => 'Insurance', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '12', 'title' => 'গ্র্যাচুইটি', 'title_en' => 'Gratuity', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '13', 'title' => 'ওভার টাইম এলাউন্স', 'title_en' => 'Over time allowance', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL)
        ];

        OtherBenefit::insert($otherBenefits);

        Schema::enableForeignKeyConstraints();

    }
}
