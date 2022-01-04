<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use JetBrains\PhpStorm\ArrayShape;
use phpDocumentor\Reflection\Types\Self_;

class AdditionalJobInformationJobLocation extends BaseModel
{

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    #[ArrayShape(["location_id" => "\mixed|string", "title" => "\mixed|string", "title_en" => "\mixed|string"])]
    public function toArray(): array
    {
        $data = parent::toArray();
        return array_merge($data, self::getJobLocationId($data));
    }

    #[ArrayShape(["location_id" => "mixed|string", "title" => "mixed|string", "title_en" => "mixed|string"])]
    public static function getJobLocationId(array $locationInfo): array
    {
        $locationId = "";
        $locationTitle = "";
        $locationTitleEn = "";
        if (!empty($locationInfo['loc_division_id'])) {
            $locationId = $locationInfo['loc_division_id'];
            $locationTitle = $locationInfo['loc_division_title'] ?? "";
            $locationTitleEn = $locationInfo['loc_division_title_en'] ?? "";
        }
        if (!empty($locationInfo['loc_district_id'])) {
            $locationId .= "_" . $locationInfo['loc_district_id'];
            $locationTitle .= " => " . $locationInfo['loc_district_title'] ?? "";
            $locationTitleEn .= " => " . $locationInfo['loc_district_title_en'] ?? "";
        }
        if (!empty($locationInfo['loc_area_id'])) {
            $locationId .= "_" . $locationInfo['loc_area_id'];
            $locationTitle .= " => " . $locationInfo['loc_area_title'] ?? "";
            $locationTitleEn .= " => " . $locationInfo['loc_area_title_en'] ?? "";
        }

        return [
            "location_id" => $locationId,
            "title" => $locationTitle,
            "title_en" => strtoupper($locationTitleEn)
        ];
    }


}
