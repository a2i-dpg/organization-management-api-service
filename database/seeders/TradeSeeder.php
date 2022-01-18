<?php

namespace Database\Seeders;

use App\Models\Trade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Trade::query()->truncate();

        $trades = [
            [
                'id' => '1',
                "title" => "Automotive industry in Bangladesh",
                "title_en" => "Automotive industry in Bangladesh"
            ],
            [
                'id' => '2',
                "title" => "Bangladesh Hi-Tech Park Authority",
                "title_en" => "Bangladesh Hi-Tech Park Authority"
            ],
            [
                'id' => '3',
                "title" => "Bangladesh Industrial and Technical Assistance Centre",
                "title_en" => "Bangladesh Industrial and Technical Assistance Centre"
            ],
            [
                'id' => '4',
                "title" => "Bangladeshi cuisine",
                "title_en" => "Bangladeshi cuisine"
            ],
            [
                'id' => '5',
                "title" => "Call centre industry in Bangladesh",
                "title_en" => "Call centre industry in Bangladesh"
            ],
            [
                'id' => '6',
                "title" => "Ceramics industry in Bangladesh",
                "title_en" => "Ceramics industry in Bangladesh"
            ],
            [
                'id' => '7',
                "title" => "Department of Inspection for Factories and Establishments",
                "title_en" => "Department of Inspection for Factories and Establishments"
            ],
            [
                'id' => '8',
                "title" => "Food industry in Bangladesh",
                "title_en" => "Food industry in Bangladesh"
            ],
            [
                'id' => '9',
                "title" => "Steel industry in Bangladesh",
                "title_en" => "Steel industry in Bangladesh"
            ],
            [
                'id' => '10',
                "title" => "Jute industry of Bangladesh",
                "title_en" => "Jute industry of Bangladesh"
            ],
            [
                'id' => '11',
                "title" => "T K Group of Industries",
                "title_en" => "T K Group of Industries"
            ],
            [
                'id' => '12',
                "title" => "Leather industry in Bangladesh",
                "title_en" => "Leather industry in Bangladesh"
            ],
            [
                'id' => '13',
                "title" => "Ministry of Industries (Bangladesh)",
                "title_en" => "Ministry of Industries (Bangladesh)"
            ],
            [
                'id' => '14',
                "title" => "Pharmaceutical industry in Bangladesh",
                "title_en" => "Pharmaceutical industry in Bangladesh"
            ],
            [
                'id' => '15',
                "title" => "Shipbuilding in Bangladesh",
                "title_en" => "Shipbuilding in Bangladesh"
            ],
            [
                'id' => '16',
                "title" => "Textile industry in Bangladesh",
                "title_en" => "Textile industry in Bangladesh"
            ]
        ];

        Trade::insert($trades);

        Schema::enableForeignKeyConstraints();
    }
}
