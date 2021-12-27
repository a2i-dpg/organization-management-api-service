<?php

namespace Database\Seeders;

use App\Models\EmploymentType;
use Illuminate\Database\Seeder;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employmentTypes = [
            [
                "code" => 'FULL',
                "title_en" => "Full-Time",
                "title" => "ফুল টাইম",
            ],
            [
                "code" => 'PART',
                "title_en" => "Part-Time",
                "title" => "খন্ডকালীন",
            ],
            [
                "code" => 'APPREN',
                "title_en" => "Apprentices and Trainees",
                "title" => "শিক্ষানবিশ এবং প্রশিক্ষণার্থী",
            ],
            [
                "code" => 'TEMP',
                "title_en" => "Temporary",
                "title" => "সাময়িক",
            ],
            [
                "code" => 'SEAS',
                "title_en" => "Seasonal",
                "title" => "মৌসুমী",
            ],
            [
                "code" => 'LEAS',
                "title_en" => "Leased",
                "title" => "ইজারা ভিত্তিক",
            ]
        ];

        EmploymentType::insert($employmentTypes);
    }
}
