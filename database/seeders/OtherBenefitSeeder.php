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
            array('id' => '2', 'title' => 'Mobile bill', 'title_en' => 'Mobile bill', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '3', 'title' => 'Pension policy', 'title_en' => 'Pension policy', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '4', 'title' => 'Tour allowance', 'title_en' => 'Tour allowance', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '5', 'title' => 'Credit card', 'title_en' => 'Credit card', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '6', 'title' => 'Medical allowance', 'title_en' => 'Medical allowance', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '7', 'title' => 'Performance bonus', 'title_en' => 'Performance bonus', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '8', 'title' => 'Profit share', 'title_en' => 'Profit share', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '9', 'title' => 'Provident fund', 'title_en' => 'Provident fund', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '10', 'title' => 'Weekly 2 holidays', 'title_en' => 'Weekly 2 holidays', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '11', 'title' => 'Insurance', 'title_en' => 'Insurance', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '12', 'title' => 'Gratuity', 'title_en' => 'Gratuity', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '13', 'title' => 'Over time allowance', 'title_en' => 'Over time allowance', 'deleted_at' => NULL, 'created_at' => NULL, 'updated_at' => NULL)
        ];

        OtherBenefit::insert($otherBenefits);

        Schema::enableForeignKeyConstraints();

    }
}
