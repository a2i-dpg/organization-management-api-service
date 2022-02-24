<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MembershipType::query()->truncate();
        $membershipTypes = [
            [
                "industry_association_id" => 1,
                "name" => "অংশীদার",
                "name_en" => "Associate Member",
                "fee" => 1000,
                "renewal_fee" => 800,
                "payment_nature" => MembershipType::PAYMENT_NATURE_SESSION_WISE_KEY,
                "payment_frequency" => MembershipType::PAYMENT_FREQUENCY_MONTHLY_KEY
            ],
            [
                "industry_association_id" => 1,
                "name" => "সাধারণ",
                "name_en" => "General Member",
                "fee" => 1000,
                "renewal_fee" => 800,
                "payment_nature" => MembershipType::PAYMENT_NATURE_SESSION_WISE_KEY,
                "payment_frequency" => MembershipType::PAYMENT_FREQUENCY_QUARTERLY_KEY
            ]
        ];
        MembershipType::insert($membershipTypes);
        Schema::enableForeignKeyConstraints();
    }
}
