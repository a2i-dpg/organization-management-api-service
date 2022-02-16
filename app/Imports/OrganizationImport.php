<?php

namespace App\Imports;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use App\Models\OrganizationType;
use App\Models\SubTrade;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class OrganizationImport implements ToCollection, WithValidation, WithHeadingRow
{
    /**
     * @param $data
     * @param $index
     * @return mixed
     */
    public function prepareForValidation($data, $index): mixed
    {
        $request = request()->all();

        if (!empty($request['industry_association_id'])) {
            $data['industry_association_id'] = $request['industry_association_id'];
        }
        if (!empty($data['organization_type_id'])) {
            $organizationType = OrganizationType::where('title', $data['organization_type_id'])->firstOrFail();
            $data['organization_type_id'] = $organizationType->id;
        }
        if (!empty($data['sub_trade'])) {
            $subTrade = SubTrade::where('title', $data['sub_trade'])->firstOrFail();
            $data['sub_trades'] = [$subTrade->id];
        }
        if (!empty($data['permission_sub_group_id'])) {
            $permissionSubGroup = ServiceToServiceCall::getPermissionSubGroupsByTitle($data['permission_sub_group_id']);
            $data['permission_sub_group_id'] = $permissionSubGroup['id'];
        }
        if (!empty($data['loc_division_id'])) {
            $division = LocDivision::where('title_en', $data['loc_division_id'])->firstOrFail();
            $data['loc_division_id'] = $division->id;
        }
        if (!empty($data['loc_district_id'])) {
            $district = LocDistrict::where('title_en', $data['loc_district_id'])->firstOrFail();
            $data['loc_district_id'] = $district->id;
        }
        if (!empty($data['loc_upazila_id'])) {
            $upazila = LocUpazila::where('title_en', $data['loc_upazila_id'])->firstOrFail();
            $data['loc_upazila_id'] = $upazila->id;
        }
        if (!empty($data['date_of_establishment'])) {
            $data['date_of_establishment'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['date_of_establishment'])->format('Y-m-d');
        }
        if (!empty($data['phone_code']) && is_int($data['phone_code'])) {
            $data['phone_code'] = (string)$data['phone_code'];
        }
        if (!empty($data['mobile']) && is_int($data['mobile']) && strlen((string)$data['mobile']) == 10 && explode((string)$data['mobile'], '')[0] != 0) {
            $data['mobile'] = '0' . $data['mobile'];
        }
        if (!empty($data['contact_person_mobile']) && is_int($data['contact_person_mobile']) && strlen((string)$data['contact_person_mobile']) == 10 && explode((string)$data['contact_person_mobile'], '')[0] != 0) {
            $data['contact_person_mobile'] = '0' . $data['contact_person_mobile'];
        }

        return $data;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'organization_type_id' => [
                'required',
                'int',
                'exists:organization_types,id,deleted_at,NULL'
            ],
            'sub_trades' => [
                'required',
                'array',
                'min:1'
            ],
            'sub_trades.*' => [
                'nullable',
                'integer',
                'exists:sub_trades,id,deleted_at,NULL'
            ],
            'membership_id' => [
                'required',
                'string',
            ],
            'permission_sub_group_id' => [
                'required',
                'integer'
            ],
            'date_of_establishment' => [
                'nullable',
                'date_format:Y-m-d'
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:600',
                'min:2',
            ],
            'title' => [
                'required',
                'string',
                'max:1200',
                'min:2'
            ],
            'loc_division_id' => [
                'required',
                'integer',
                'exists:loc_divisions,id,deleted_at,NULL'
            ],
            'loc_district_id' => [
                'required',
                'integer',
                'exists:loc_districts,id,deleted_at,NULL'
            ],
            'loc_upazila_id' => [
                'nullable',
                'integer',
                'exists:loc_upazilas,id,deleted_at,NULL'
            ],
            "location_latitude" => [
                'nullable',
                'string',
            ],
            "location_longitude" => [
                'nullable',
                'string',
            ],
            "google_map_src" => [
                'nullable',
                'integer',
            ],
            'address' => [
                'required',
                'max: 1200',
                'min:2'
            ],
            'address_en' => [
                'nullable',
                'max: 600',
                'min:2'
            ],
            "country" => [
                "nullable",
                "string",
                "min:2"
            ],
            "phone_code" => [
                "nullable",
                "string"
            ],
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'email' => [
                'required',
                'email',
                'max:320'
            ],
            'fax_no' => [
                'nullable',
                'string',
                'max: 30',
            ],
            "name_of_the_office_head" => [
                "nullable",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_en" => [
                "nullable",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_designation" => [
                "nullable",
                "string"
            ],
            "name_of_the_office_head_designation_en" => [
                "nullable",
                "string"
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_name_en' => [
                'nullable',
                'max: 250',
                'min:2'
            ],
            'contact_person_mobile' => [
                'required',
                Rule::unique('organizations', 'contact_person_mobile')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    }),
                BaseModel::MOBILE_REGEX,
            ],
            'contact_person_email' => [
                'required',
                'email',
                Rule::unique('organizations', 'contact_person_email')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'contact_person_designation' => [
                'required',
                'max: 600',
                "min:2"
            ],
            'contact_person_designation_en' => [
                'nullable',
                'max: 300',
                "min:2"
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'description_en' => [
                'nullable',
                'string',
            ],
            'logo' => [
                'nullable',
                'string',
            ],
            'row_status' => [
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];
    }

    /**
     * Store organizations as bulk.
     *
     * @param Collection $collection
     * @return void
     * @throws Throwable
     */
    public function collection(Collection $collection)
    {

    }
}
