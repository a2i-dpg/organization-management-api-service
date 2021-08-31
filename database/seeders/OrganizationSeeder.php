<?php

namespace Database\Seeders;

use App\Models\HumanResourceTemplate;
use App\Models\Organization;
use App\Models\OrganizationUnit;
use App\Models\OrganizationUnitType;
use App\Models\Rank;
use App\Models\RankType;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;


/**
 * Class OrganizationSeeder
 * @package Database\Seeders
 */
class OrganizationSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
//        OrganizationUnitType::query()->truncate();
//        OrganizationUnit::query()->truncate();
//        RankType::query()->truncate();
//        Rank::query()->truncate();

        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            OrganizationUnitType::factory()
                ->has(OrganizationUnit::factory()
                    ->count(1)
                    ->state(new Sequence(
                        [
                            'organization_id' => $organization->id,
                            'title_en' => "Bangking",
                            'title_bn' => 'Bangking'
                        ]
                    ))
                )
                ->count(2)
                ->state(new Sequence(
                    [
                        'organization_id' => $organization->id,
                        'title_en' => 'Payment',
                        'title_bn' => 'Payment'
                    ]
                ))
                ->create();
            RankType::factory()
                ->has(Rank::factory()->count(3)->state(new Sequence(
                        [
                            'organization_id' => $organization->id,
                            'title_en' => "জেনারেল",
                            'title_bn' => "জেনারেল",
                            'grade' => 1,
                            'display_order' => 1
                        ],
                        [
                            'organization_id' => $organization->id,
                            'title_en' => "লেফটেন্যান্ট জেনারেল",
                            'title_bn' => "লেফটেন্যান্ট জেনারেল",
                            'grade' => 2,
                            'display_order' => 2
                        ],
                        [
                            'organization_id' => $organization->id,
                            'title_en' => "ব্রিগেডিয়ার জেনারেল",
                            'title_bn' => "ব্রিগেডিয়ার জেনারেল",
                            'grade' => 3,
                            'display_order' => 3
                        ]
                    )
                )
                )->count(3)->state(new Sequence(
                    [
                        'organization_id' => $organization->id,
                        'title_en' => "Chief of Army Staff",
                        "title_bn" => "Chief of Army Staff"

                    ],
                    [
                        'organization_id' => $organization->id,
                        'title_en' => "Chief of General Staff",
                        "title_bn" => "Chief of General Staff"

                    ],
                    [
                        'organization_id' => $organization->id,
                        'title_en' => "Chief of brigade",
                        "title_bn" => "Chief of brigade"

                    ]
                ))->create();

        }
        Schema::enableForeignKeyConstraints();
    }
}
