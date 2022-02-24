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
            array('id' => '1', 'title' => 'Tesla', 'title_en' => 'Tesla', 'trade_id' => '1', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '2', 'title' => 'Daimler AG', 'title_en' => 'Daimler AG', 'trade_id' => '1', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '3', 'title' => 'Volkswagen AG', 'title_en' => 'Volkswagen AG', 'trade_id' => '1', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '4', 'title' => 'Ford Motor', 'title_en' => 'Ford Motor', 'trade_id' => '2', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '5', 'title' => 'Bayerische Motoren', 'title_en' => 'Bayerische Motoren', 'trade_id' => '2', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '6', 'title' => 'Geely Automobile', 'title_en' => 'Geely Automobile', 'trade_id' => '2', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '7', 'title' => 'Champion Automotive Repair Services', 'title_en' => 'Champion Automotive Repair Services', 'trade_id' => '3', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '8', 'title' => 'PepsiCo', 'title_en' => 'PepsiCo', 'trade_id' => '3', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '9', 'title' => 'The Coca-Cola Company', 'title_en' => 'The Coca-Cola Company', 'trade_id' => '3', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '10', 'title' => 'Mondelēz International', 'title_en' => 'Mondelēz International', 'trade_id' => '4', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '11', 'title' => 'Anheuser-Busch InBev', 'title_en' => 'Anheuser-Busch InBev', 'trade_id' => '5', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '12', 'title' => 'AUTO-SATTLEREI BRUHN', 'title_en' => 'AUTO-SATTLEREI BRUHN', 'trade_id' => '5', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL),
            array('id' => '13', 'title' => 'AGENTUR GABRIELE KLESSE', 'title_en' => 'AGENTUR GABRIELE KLESSE', 'trade_id' => '5', 'deleted_at' => NULL, 'created_at' => '2022-02-08 19:41:28', 'updated_at' => NULL)
        ];

        SubTrade::insert($subTrades);

        Schema::enableForeignKeyConstraints();

    }
}
