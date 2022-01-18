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
                'id' => '1',
                "title" => "Tesla",
                "title_en" => "Tesla",
                "industry_association_trade_id" => 1,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '2',
                "title" => "Daimler AG",
                "title_en" => "Daimler AG",
                "industry_association_trade_id" => 1,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '3',
                "title" => "Volkswagen AG",
                "title_en" => "Volkswagen AG",
                "industry_association_trade_id" => 1,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '4',
                "title" => "Ford Motor",
                "title_en" => "Ford Motor",
                "industry_association_trade_id" => 2,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '5',
                "title" => "Bayerische Motoren",
                "title_en" => "Bayerische Motoren",
                "industry_association_trade_id" => 2,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '6',
                "title" => "Geely Automobile",
                "title_en" => "Geely Automobile",
                "industry_association_trade_id" => 2,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '7',
                "title" => "Champion Automotive Repair Services",
                "title_en" => "Champion Automotive Repair Services",
                "industry_association_trade_id" => 3,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '8',
                "title" => "PepsiCo",
                "title_en" => "PepsiCo",
                "industry_association_trade_id" => 3,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '9',
                "title" => "The Coca-Cola Company",
                "title_en" => "The Coca-Cola Company",
		        "industry_association_trade_id" => 3,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '10',
                "title" => "Mondelēz International",
                "title_en" => "Mondelēz International",
                "industry_association_trade_id" => 4,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '11',
                "title" => "Anheuser-Busch InBev",
                "title_en" => "Anheuser-Busch InBev",
                "industry_association_trade_id" => 5,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '12',
                "title" => "AUTO-SATTLEREI BRUHN",
                "title_en" => "AUTO-SATTLEREI BRUHN",
                "industry_association_trade_id" => 5,
                "created_at" => Carbon::now()
            ],
            [
                'id' => '13',
                "title" => "AGENTUR GABRIELE KLESSE",
                "title_en" => "AGENTUR GABRIELE KLESSE",
                "industry_association_trade_id" => 5,
                "created_at" => Carbon::now()
            ],];

        SubTrade::insert($subTrades);

        Schema::enableForeignKeyConstraints();

    }
}
