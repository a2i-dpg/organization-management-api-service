<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IndustryAssociationService
{

    public function store(IndustryAssociation $industryAssociation, array $data): IndustryAssociation
    {
        $industryAssociation->fill($data);
        $industryAssociation->save();
        return $industryAssociation;
    }

    /**
     * industryAssociation validator
     * @param array $data
     * @return mixed|null
     * @throws RequestException
     */
    public function createUser(array $data): array|null
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'admin-user-create';
        $userPostField = [
            'permission_sub_group_id' => $data['permission_sub_group_id'] ?? "",
            'user_type' => BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE,
            'industry_association_id' => $data['industry_association_id'] ?? "",
            'username' => $data['contact_person_mobile'] ?? "",
            'name_en' => $data['contact_person_name'] ?? "",
            'name' => $data['contact_person_name'] ?? "",
            'email' => $data['contact_person_email'] ?? "",
            'mobile' => $data['contact_person_mobile'] ?? ""
        ];

        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->post($url, $userPostField)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                return $e;
            })
            ->json();
    }


    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        $rules = [
            'industry_association_type_id' => [
                'required',
                'int',
                Rule::in(IndustryAssociation::INDUSTRY_ASSOCIATION_TYPES)
            ],
            'permission_sub_group_id' => [
                Rule::requiredIf(function () use ($id) {
                    return is_null($id);
                }),
                'nullable',
                'integer'
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
                'nullable',
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
                'nullable',
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
                'max:191'
            ],
            'fax_no' => [
                'nullable',
                'string',
                'max: 30',
            ],
            'trade_number' => [
                'required',
                'string',
                'max:100'
            ],
            "name_of_the_office_head" => [
                "required",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_en" => [
                "nullable",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_designation" => [
                "required",
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
                Rule::unique('industry_associations', 'contact_person_mobile')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    }),
                BaseModel::MOBILE_REGEX,

            ],
            'contact_person_email' => [
                'required',
                'email',
                'max:191'
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
            'domain' => [
                'nullable',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'string',
                'max:191',
                Rule::unique('industry_associations', 'domain')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'logo' => [
                'nullable',
                'string',
            ],
            'row_status' => [
                'nullable',
                Rule::in(IndustryAssociation::ROW_STATUSES),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }


    /**
     * industryAssociation open registration validation
     * @param Request $request
     * @return mixed
     */
    public function industryAssociationRegistrationValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        $rules = [
            'industry_association_type_id' => [
                'required',
                'int',
                Rule::in(IndustryAssociation::INDUSTRY_ASSOCIATION_TYPES)
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
                'nullable',
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
                'nullable',
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
                'max:191'
            ],
            'fax_no' => [
                'nullable',
                'string',
                'max: 30',
            ],
            'trade_number' => [
                'required',
                'string',
                'max:100'
            ],
            "name_of_the_office_head" => [
                "required",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_en" => [
                "nullable",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_designation" => [
                "required",
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
                Rule::unique('industry_associations', 'contact_person_mobile')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    }),
                BaseModel::MOBILE_REGEX,

            ],
            'contact_person_email' => [
                'required',
                'email',
                'max:191'
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
            'domain' => [
                'nullable',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'string',
                'max:191',
                Rule::unique('industry_associations', 'domain')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'logo' => [
                'nullable',
                'string',
            ],
            'row_status' => [
                'nullable',
                Rule::in(IndustryAssociation::ROW_STATUSES),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }


}
