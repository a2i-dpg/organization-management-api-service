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
        OtherBenefit::query()->truncate();

        $otherBenefits = [
            [
                "title" => "T/A",
                "title_en" => "T/A",
            ],
            [
                "title" => "Mobile bill",
                "title_en" => "Mobile bill",
            ],
            [
                "title" => "Pension policy",
                "title_en" => "Pension policy",
            ],
            [
                "title" => "Tour allowance",
                "title_en" => "Tour allowance",
            ],
            [
                "title" => "Credit card",
                "title_en" => "Credit card",
            ],
            [
                "title" => "Medical allowance",
                "title_en" => "Medical allowance",
            ],
            [
                "title" => "Performance bonus",
                "title_en" => "Performance bonus",
            ],
            [
                "title" => "Profit share",
                "title_en" => "Profit share",
            ],
            [
                "title" => "Provident fund",
                "title_en" => "Provident fund",
            ],
            [
                "title" => "Weekly 2 holidays",
                "title_en" => "Weekly 2 holidays",
            ],
            [
                "title" => "Insurance",
                "title_en" => "Insurance",
            ],
            [
                "title" => "Gratuity",
                "title_en" => "Gratuity",
            ],
            [
                "title" => "Over time allowance",
                "title_en" => "Over time allowance",
            ],
        ];

        OtherBenefit::insert($otherBenefits);

    }
}
