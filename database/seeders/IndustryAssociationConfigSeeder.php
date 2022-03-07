<?php

namespace Database\Seeders;

use App\Models\IndustryAssociationConfig;
use App\Models\PaymentTransactionHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class IndustryAssociationConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        IndustryAssociationConfig::query()->truncate();
        $config = [
            [
                'industry_association_id' => 2,
                'session_type' => IndustryAssociationConfig::SESSION_TYPE_JUNE_JULY,
                'payment_gateways' => json_encode([
                    PaymentTransactionHistory::PAYMENT_GATEWAY_SSLCOMMERZ => config('sslcommerz')
                ])
            ]
        ];
        IndustryAssociationConfig::insert($config);
        Schema::enableForeignKeyConstraints();
    }
}
