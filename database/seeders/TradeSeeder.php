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
                "title" => "Automotive Industry in Bangladesh",
                "title_en" => "Automotive Industry in Bangladesh"
            ],
            [
                'id' => '2',
                "title" => "Bangladesh Hi-Tech Park Authority",
                "title_en" => "Bangladesh Hi-Tech Park Authority"
            ],
            [
                'id' => '3',
                "title" => "Bangladesh Industrial and Technical Assistance Center",
                "title_en" => "Bangladesh Industrial and Technical Assistance Center"
            ],
            [
                'id' => '4',
                "title" => "Bangladeshi Cuisine",
                "title_en" => "Bangladeshi Cuisine"
            ],
            [
                'id' => '5',
                "title" => "Call Centre Industry in Bangladesh",
                "title_en" => "Call Centre Industry in Bangladesh"
            ],
            [
                'id' => '6',
                "title" => "Ceramics Industry in Bangladesh",
                "title_en" => "Ceramics Industry in Bangladesh"
            ],
            [
                'id' => '7',
                "title" => "Department of Inspection for Factories and Establishments",
                "title_en" => "Department of Inspection for Factories and Establishments"
            ],
            [
                'id' => '8',
                "title" => "Food Industry in Bangladesh",
                "title_en" => "Food Industry in Bangladesh"
            ],
            [
                'id' => '9',
                "title" => "Steel Industry in Bangladesh",
                "title_en" => "Steel Industry in Bangladesh"
            ],
            [
                'id' => '10',
                "title" => "Jute Industry of Bangladesh",
                "title_en" => "Jute Industry of Bangladesh"
            ],
            [
                'id' => '11',
                "title" => "T K Group of Industries",
                "title_en" => "T K Group of Industries"
            ],
            [
                'id' => '12',
                "title" => "Leather Industry in Bangladesh",
                "title_en" => "Leather Industry in Bangladesh"
            ],
            [
                'id' => '13',
                "title" => "Ministry of Industries (Bangladesh)",
                "title_en" => "Ministry of Industries (Bangladesh)"
            ],
            [
                'id' => '14',
                "title" => "Pharmaceutical Industry in Bangladesh",
                "title_en" => "Pharmaceutical Industry in Bangladesh"
            ],
            [
                'id' => '15',
                "title" => "Ship Building in Bangladesh",
                "title_en" => "Ship Building in Bangladesh"
            ],
            [
                'id' => '16',
                "title" => "Textile Industry in Bangladesh",
                "title_en" => "Textile Industry in Bangladesh"
            ],
            [
                'id' => '17',
                "title" => "Chemical Industry in Bangladesh",
                "title_en" => "Chemical Industry in Bangladesh"
            ],
        ];

        Trade::insert($trades);

        Schema::enableForeignKeyConstraints();
    }
}
