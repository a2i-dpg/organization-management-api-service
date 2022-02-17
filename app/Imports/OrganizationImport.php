<?php

namespace App\Imports;

use App\Facade\ServiceToServiceCall;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use App\Models\OrganizationType;
use App\Models\SubTrade;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class OrganizationImport implements ToCollection, SkipsEmptyRows, WithValidation, WithHeadingRow
{
    /**
     * @param $data
     * @param $index
     * @return mixed
     */
    public function prepareForValidation($data, $index): mixed
    {
        $request = request()->all();

        //handle only for industry association user
        if (!empty($request['industry_association_id']) && !empty($data['membership_id'])) {
            $industryAssociations = array([ 'industry_association_id' => $request['industry_association_id'],'membership_id' => $data['membership_id']]);
            $data['industry_associations'] =  $industryAssociations;
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
        return [];
    }

    /**
     * Store organizations as bulk.
     * Don't remove this collection method
     *
     * @param Collection $collection
     * @return void
     * @throws Throwable
     */
    public function collection(Collection $collection)
    {

    }
}
