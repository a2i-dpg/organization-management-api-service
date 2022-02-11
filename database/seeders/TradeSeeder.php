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
                "title" => "Automotive Industry",
                "title_en" => "Automotive Industry"
            ],
            [
                'id' => '2',
                "title" => "Hi-Tech Park",
                "title_en" => "Hi-Tech Park"
            ],
            [
                'id' => '3',
                "title" => "Bangladeshi Cuisine",
                "title_en" => "Bangladeshi Cuisine"
            ],
            [
                'id' => '4',
                "title" => "Call Centre",
                "title_en" => "Call Centre"
            ],
            [
                'id' => '5',
                "title" => "Ceramics",
                "title_en" => "Ceramics"
            ],
            [
                'id' => '6',
                "title" => "Food",
                "title_en" => "Food"
            ],
            [
                'id' => '7',
                "title" => "Steel",
                "title_en" => "Steel"
            ],
            [
                'id' => '8',
                "title" => "Jute",
                "title_en" => "Jute"
            ],
            [
                'id' => '9',
                "title" => "T K Group",
                "title_en" => "T K Group"
            ],
            [
                'id' => '10',
                "title" => "Leather",
                "title_en" => "Leather"
            ],
            [
                'id' => '11',
                "title" => "Pharmaceutical",
                "title_en" => "Pharmaceutical"
            ],
            [
                'id' => '12',
                "title" => "Ship Building",
                "title_en" => "Ship Building"
            ],
            [
                'id' => '13',
                "title" => "Textile",
                "title_en" => "Textile"
            ],
            [
                'id' => '14',
                "title" => "Chemical",
                "title_en" => "Chemical"
            ],
            [
                'id' => '15',
                "title" => "Garments",
                "title_en" => "Garments"
            ],
            [
                'id' => '16',
                "title" => "Tourism",
                "title_en" => "Tourism"
            ],
            [
                'id' => '17',
                "title" => "Biotech",
                "title_en" => "Biotech"
            ],
        ];

        Trade::insert($trades);

        Schema::enableForeignKeyConstraints();
    }
}
