<?php

namespace Database\Seeders;

use App\Models\HumanResource;
use App\Models\HumanResourceTemplate;
use App\Models\Organization;
use App\Models\OrganizationUnit;
use App\Models\OrganizationUnitType;
use App\Models\Rank;
use App\Models\RankType;
use App\Models\Service;
use App\Models\Skill;
use App\Services\OrganizationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;


/**
 * Class OrganizationSeeder
 * @package Database\Seeders
 */
class OrganizationSeeder extends Seeder
{
    const createOrganization = true;
    const createIdpUser = false;

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $skillIdCollection = Skill::all()->pluck('id');

        /** @var OrganizationService $organizationService */
        $organizationService = app(OrganizationService::class);

        if (self::createOrganization) {
            $organizations = Organization::factory()->count(5)->create();
        } else {
            $organizations = Organization::all();
        }

        $serviceIds = Service::all()->pluck('id');

        foreach ($organizations as $organization) {

            /** @var Organization $organization */
            if (self::createIdpUser) {
                try {

                    $orgData = $organization->toArray();
                    unset($orgData['id']);
                    $orgData['password'] = '12345678';
                    $orgData['organization_id'] = $organization->id;
                    $orgData['permission_sub_group_id'] = 3;

                    $organizationService->createUser($orgData);

                } catch (\Exception $e) {
                    Log::debug('User Creation Failed for Org id: ', $organization->id);
                    Log::debug($e->getCode() . ' - ' . $e->getMessage());
                }
            }

            /** @var RankType $rankType */
            $rankType = app(RankType::class);
            $rankType->fill(
                [

                    'title' => 'General Rank Type',
                    'title_en' => 'সাধারণ রাঙ্ক টাইপ',
                    'organization_id' => $organization->id
                ]
            );

            $rankType->save();

            $ranks = [
                [
                    'title' => 'সি.ই.ও.',
                    'title_en' => 'C.E.O.',
                    'grade' => 1,
                    'display_order' => 1,
                    'rank_type_id' => $rankType->id,
                    'organization_id' => $organization->id
                ],
                [
                    'title' => 'এম.ডি.',
                    'title_en' => 'M.D.',
                    'rank_type_id' => $rankType->id,
                    'organization_id' => $organization->id,
                    'grade' => 2,
                    'display_order' => 2,
                ],
                [
                    'title_en' => 'C.F.O.',
                    'title' => 'সি.এফ.ও.',
                    'rank_type_id' => $rankType->id,
                    'organization_id' => $organization->id,
                    'grade' => 3,
                    'display_order' => 3
                ],
                [
                    'title' => 'মহাব্যবস্থাপক',
                    'title_en' => 'General Manager',
                    'rank_type_id' => $rankType->id,
                    'organization_id' => $organization->id,
                    'grade' => 4,
                    'display_order' => 4
                ],
                [
                    'title' => 'মানবসম্পদ ব্যবস্থাপক',
                    'title_en' => 'HR Manager',
                    'rank_type_id' => $rankType->id,
                    'organization_id' => $organization->id,
                    'grade' => 5,
                    'display_order' => 5
                ]
            ];

            $rankIds = collect([]);
            foreach ($ranks as $rankDatum) {
                /** @var Rank $rank */
                $rank = app(Rank::class);
                $rank->fill($rankDatum);
                $rank->save();
                $rankIds->add($rank->id);
            }

            $orgUnitTypesData = [
                [
                    'title' => 'বিজনেস ইউনিট 1',
                    'title_en' => 'Business Unit 1',
                    'organization_id' => $organization->id
                ],
                [
                    'title' => 'বিজনেস ইউনিট 2',
                    'title_en' => 'Business Unit 2',
                    'organization_id' => $organization->id
                ]
            ];

            foreach ($orgUnitTypesData as $orgUnitTypesDatum) {
                /** @var OrganizationUnitType $orgUnitType */
                $orgUnitType = app(OrganizationUnitType::class);
                $orgUnitType->fill($orgUnitTypesDatum);
                $orgUnitType->save();

               $humanResourceTemplates = HumanResourceTemplate::factory()
                    ->state([
                        'organization_id' => $organization->id,
                        'organization_unit_type_id' => $orgUnitType->id,
                        'rank_id' => $rankIds->random(),
                    ])
                    ->count(10)
                    ->create();

                $organizationUnits = OrganizationUnit::factory()
                    ->state([
                        'organization_id' => $organization->id,
                        'organization_unit_type_id' => $orgUnitType->id,
                    ])
                    ->count(3)
                    ->has(
                        HumanResource::factory()
                            ->state([
                                'organization_id' => $organization->id,
                                'rank_id' => $rankIds->random()
                            ])
                            ->count(10)
                    )
                    ->create();


                foreach ($organizationUnits as $organizationUnit) {
                    /** @var OrganizationUnit $organizationUnit */
                    $organizationUnit->services()->sync($serviceIds->random(3)->all());
                }

                $humanResources = HumanResource::where('organization_id', $organization->id)->get();

                foreach ($humanResources as $humanResource) {
                    /** @var HumanResource $humanResource */
                    $humanResource->skills()->sync($skillIdCollection->random(3)->all());
                }

                foreach ($humanResourceTemplates as $humanResourceTemplate) {
                    /** @var HumanResourceTemplate $humanResourceTemplate */
                    $humanResourceTemplate->skills()->sync($skillIdCollection->random(3)->all());
                }
            }

        }

        Schema::enableForeignKeyConstraints();
    }
}
