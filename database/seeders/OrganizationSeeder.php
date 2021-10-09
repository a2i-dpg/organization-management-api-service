<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationUnit;
use App\Models\OrganizationUnitType;
use App\Models\Rank;
use App\Models\RankType;
use App\Models\Service;
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

        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            OrganizationUnitType::factory()
                ->count(3)
                ->state(['organization_id' => $organization->id])
//                ->has(                                  //TODO: fix organization seeder
//                    OrganizationUnit::factory()
//                        ->count(5)
//                        ->state(['organization_id' => $organization->id])
//                        ->hasAttached(Service::factory()->count(4))
//                )
                ->create();

            RankType::factory()
                ->count(3)
                ->state(['organization_id' => $organization->id])
                ->has(
                    Rank::factory()
                        ->count(6)
                        ->state(['organization_id' => $organization->id])
                        ->state(new Sequence(
                                [
                                    'grade' => 1,
                                    'display_order' => 1
                                ],
                                [
                                    'grade' => 2,
                                    'display_order' => 2
                                ],
                                [
                                    'grade' => 3,
                                    'display_order' => 3
                                ],
                                [
                                    'grade' => 4,
                                    'display_order' => 4
                                ],
                                [
                                    'grade' => 5,
                                    'display_order' => 5
                                ],
                                [
                                    'grade' => 6,
                                    'display_order' => 6
                                ]
                            )
                        )
                )
                ->create();

        }
        Schema::enableForeignKeyConstraints();
    }
}
