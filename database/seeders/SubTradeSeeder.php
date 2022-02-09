<?php

namespace Database\Seeders;

use App\Models\SubTrade;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SubTradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        SubTrade::query()->truncate();

        $subTrades = [
            [
                "title" => "Automotive Industry Sub Trade 1",
                "title_en" => "Automotive Industry Sub Trade 1",
                "trade_id" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Automotive Industry Sub Trade 2",
                "title_en" => "Automotive Industry Sub Trade 2",
                "trade_id" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Chemical Sub Trade 1",
                "title_en" => "Chemical Sub Trade 1",
                "trade_id" => 14,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Chemical Sub Trade 2",
                "title_en" => "Chemical Sub Trade 2",
                "trade_id" => 14,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Biotech Sub Trade 1",
                "title_en" => "Biotech Sub Trade 1",
                "trade_id" => 17,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Biotech Sub Trade 2",
                "title_en" => "Biotech Sub Trade 2",
                "trade_id" => 17,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Pharmaceutical Sub Trade 1",
                "title_en" => "Pharmaceutical Sub Trade 1",
                "trade_id" => 11,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Pharmaceutical Sub Trade 2",
                "title_en" => "Pharmaceutical Sub Trade 2",
                "trade_id" => 11,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "T K Group Sub Trade 1",
                "title_en" => "T K Group Sub Trade 1",
                "trade_id" => 9,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "T K Group Sub Trade 2",
                "title_en" => "T K Group Sub Trade 2",
                "trade_id" => 9,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Tourism Sub Trade 1",
                "title_en" => "Tourism Sub Trade 1",
                "trade_id" => 16,
                "created_at" => Carbon::now()
            ],
            [
                "title" => "Tourism Sub Trade 2",
                "title_en" => "Tourism Sub Trade 2",
                "trade_id" => 16,
                "created_at" => Carbon::now()
            ]
        ];

        SubTrade::insert($subTrades);

        Schema::enableForeignKeyConstraints();

    }
}
