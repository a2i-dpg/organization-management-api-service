<?php

namespace App\Services\JobManagementServices;

use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class AdditionalJobInformationService
{
    public function getJobLocation(): array
    {
        return Cache::rememberForever("JOB_LOCATION_FOR_JOB_POSTING", function () {
            return $this->getLocationData();
        });

    }

    private function getLocationData(): array
    {
        $jobLocation = [];
        $divisions = LocDivision::all();

        /** @var Builder $districtsBuilder */
        $districtsBuilder = LocDistrict::select([
            'loc_districts.id',
            'loc_districts.loc_division_id',
            'loc_districts.title',
            'loc_districts.title_en',
            'loc_districts.is_sadar_district',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
        ]);

        $districtsBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_districts.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $districts = $districtsBuilder->get();


        /** @var LocUpazila|Builder $upazilasBuilder */
        $upazilasBuilder = LocUpazila::select([
            'loc_upazilas.id',
            'loc_upazilas.title',
            'loc_upazilas.title_en',
            'loc_upazilas.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'loc_upazilas.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en'
        ]);

        $upazilasBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_upazilas.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $upazilasBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_upazilas.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $upazilas = $upazilasBuilder->get();

        foreach ($divisions as $division) {
            $key = $division->id;
            $jobLocation[$key] = strtoupper($division->title_en . "(" . $division->title . ")");
        }

        foreach ($districts as $district) {
            $key = $district->loc_division_id . "_" . $district->id;
            $titleEn = $district->division_title_en . " => " . $district->title_en;
            $titleBn = " (" . $district->division_title . " => " . $district->title . ")";
            if ($district->is_sadar_district) {
                $titleEn = $district->division_title_en . " => " . $district->title_en . "(Zilla Sadar)";
                $titleBn = " (" . $district->division_title . " => " . $district->title . "(জেলা সদর))";
            }
            $jobLocation[$key] = strtoupper($titleEn . $titleBn);
        }

        foreach ($upazilas as $upazila) {
            $key = $upazila->loc_division_id . "_" . $upazila->loc_district_id . "_" . $upazila->id;
            $titleEn = $upazila->division_title_en . " => " . $upazila->district_title_en . " => " . $upazila->title_en;
            $titleBn = " (" . $upazila->division_title . " => " . $upazila->district_title . " => " . $upazila->title . ")";
            $jobLocation[$key] = strtoupper($titleEn . $titleBn);
        }

        return $jobLocation;
    }


    public function validator(Request $request)
    {

    }
}
