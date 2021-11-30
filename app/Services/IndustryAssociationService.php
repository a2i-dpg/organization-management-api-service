<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class IndustryAssociationService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getIndustryAssociationList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $industryAssociationTypeId = $request['industry_association_type_id'] ?? "";

        /** @var Builder organizationBuilder */
        $industryAssociationBuilder = IndustryAssociation::select([
            'industry_associations.id',
            'industry_associations.industry_association_type_id',
            'industry_associations.title_en',
            'industry_associations.title',
            'industry_associations.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'industry_associations.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'industry_associations.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'loc_upazilas.title as location_latitude',
            'loc_upazilas.title as location_longitude',
            'loc_upazilas.title as google_map_src',
            'industry_associations.name_of_the_office_head',
            'industry_associations.name_of_the_office_head_en',
            'industry_associations.name_of_the_office_head_designation',
            'industry_associations.name_of_the_office_head_designation_en',
            'industry_associations.address',
            'industry_associations.address_en',
            'industry_associations.country',
            'industry_associations.phone_code',
            'industry_associations.mobile',
            'industry_associations.email',
            'industry_associations.fax_no',
            'industry_associations.trade_number',
            'industry_associations.contact_person_name',
            'industry_associations.contact_person_name_en',
            'industry_associations.contact_person_mobile',
            'industry_associations.contact_person_email',
            'industry_associations.contact_person_designation',
            'industry_associations.contact_person_designation_en',
            'industry_associations.logo',
            'industry_associations.domain',
            'industry_associations.row_status',
            'industry_associations.created_by',
            'industry_associations.updated_by',
            'industry_associations.created_at',
            'industry_associations.updated_at'
        ]);

        $industryAssociationBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('industry_associations.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $industryAssociationBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('industry_associations.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $industryAssociationBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('industry_associations.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $industryAssociationBuilder->orderBy('industry_associations.id', $order);


        if (is_numeric($rowStatus)) {
            $industryAssociationBuilder->where('industry_associations.row_status', $rowStatus);
        }

        if (is_numeric($industryAssociationTypeId)) {
            $industryAssociationBuilder->where('industry_associations.industry_association_type_id', $industryAssociationTypeId);
        }

        if (!empty($titleEn)) {
            $industryAssociationBuilder->where('industry_associations.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $industryAssociationBuilder->where('industry_associations.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $organizations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $industryAssociations = $industryAssociationBuilder->paginate($pageSize);
            $paginateData = (object)$industryAssociations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $industryAssociations = $industryAssociationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $industryAssociations->toArray()['data'] ?? $industryAssociations->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());

        return $response;
    }

    /**
     * @param int $id
     * @return Model|Builder
     */
    public function getOneIndustryAssociation(int $id): Model|Builder
    {
        /** @var Builder $industryAssociationBuilder */
        $industryAssociationBuilder = IndustryAssociation::select([
            'industry_associations.id',
            'industry_associations.industry_association_type_id',
            'industry_associations.title_en',
            'industry_associations.title',
            'industry_associations.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'industry_associations.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'industry_associations.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'loc_upazilas.title as location_latitude',
            'loc_upazilas.title as location_longitude',
            'loc_upazilas.title as google_map_src',
            'industry_associations.name_of_the_office_head',
            'industry_associations.name_of_the_office_head_en',
            'industry_associations.name_of_the_office_head_designation',
            'industry_associations.name_of_the_office_head_designation_en',
            'industry_associations.address',
            'industry_associations.address_en',
            'industry_associations.country',
            'industry_associations.phone_code',
            'industry_associations.mobile',
            'industry_associations.email',
            'industry_associations.fax_no',
            'industry_associations.trade_number',
            'industry_associations.contact_person_name',
            'industry_associations.contact_person_name_en',
            'industry_associations.contact_person_mobile',
            'industry_associations.contact_person_email',
            'industry_associations.contact_person_designation',
            'industry_associations.contact_person_designation_en',
            'industry_associations.logo',
            'industry_associations.domain',
            'industry_associations.row_status',
            'industry_associations.created_by',
            'industry_associations.updated_by',
            'industry_associations.created_at',
            'industry_associations.updated_at'
        ]);

        $industryAssociationBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('industry_associations.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $industryAssociationBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('industry_associations.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $industryAssociationBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('industry_associations.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $industryAssociationBuilder->where('industry_associations.id', '=', $id);

        return $industryAssociationBuilder->firstOrFail();
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @param array $data
     * @return IndustryAssociation
     */
    public function store(IndustryAssociation $industryAssociation, array $data): IndustryAssociation
    {
        $industryAssociation->fill($data);
        $industryAssociation->save();
        return $industryAssociation;
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @param array $data
     * @return IndustryAssociation
     */
    public function update(IndustryAssociation $industryAssociation, array $data): IndustryAssociation
    {
        $industryAssociation->fill($data);
        $industryAssociation->save();
        return $industryAssociation;
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return bool
     */
    public function destroy(IndustryAssociation $industryAssociation): bool
    {
        return $industryAssociation->delete();
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return bool
     */
    public function restore(IndustryAssociation $industryAssociation): bool
    {
        return $industryAssociation->restore();
    }

    /**
     * @param array $data
     * @param Organization $organization
     */
    public function industryAssociationMembershipApproval(array $data, Organization $organization)
    {
        $organization->industryAssociations()->updateExistingPivot($data['industry_association_id'], [
            'row_status' => 1
        ]);
    }

    /**
     * @param array $data
     * @param Organization $organization
     */
    public function industryAssociationMembershipRejection(array $data, Organization $organization)
    {
        $organization->industryAssociations()->updateExistingPivot($data['industry_association_id'], [
            'row_status' => 4
        ]);
    }

    public function industryAssociationMembershipValidator(Request $request, int $organizationId): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'industry_association_id' => [
                'required',
                'integer',
                Rule::exists('industry_association_organization', 'industry_association_id')
                    ->where(function ($query) use ($organizationId) {
                        $query->where('organization_id', $organizationId)
                            ->where('row_status', BaseModel::ROW_STATUS_PENDING);
                    })
            ]
        ];
        return Validator::make($request->all(), $rules);
    }


    /**
     * industryAssociation validator
     * @param array $data
     * @return mixed
     * @throws RequestException
     */
    public function createUser(array $data): mixed
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


    /**
     * @param array $data
     * @return array|null
     * @throws RequestException
     */
    public function createOpenRegisterUser(array $data): array|null
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'user-open-registration';

        $userPostField = [
            'user_type' => BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE,
            'username' => $data['contact_person_mobile'] ?? "",
            'industry_association_id' => $data['organization_id'] ?? "",
            'name_en' => $data['contact_person_name_en'] ?? "",
            'name' => $data['contact_person_name'] ?? "",
            'email' => $data['contact_person_email'] ?? "",
            'mobile' => $data['contact_person_mobile'] ?? "",
            'password' => $data['password'] ?? ""
        ];

        Log::channel('org_reg')->info("organization registration data provided to core", $userPostField);

        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->post($url, $userPostField)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                throw $e;
            })
            ->json();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
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
            'password.regex' => BaseModel::PASSWORD_VALIDATION_MESSAGE
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
            'trade_number' => [
                'required',
                'string',
                'max:100'
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
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'email' => [
                'required',
                'email',
                'max:191'
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
            /**Commented it for custom validation message*/
//            "password" => [
//                "required",
//                "confirmed",
//                Password::min(BaseModel::PASSWORD_MIN_LENGTH)
//                    ->letters()
//                    ->mixedCase()
//                    ->numbers()
//            ],
            "password" => [
                'required',
                'min:' . BaseModel::PASSWORD_MIN_LENGTH,
                BaseModel::PASSWORD_REGEX
            ],
            "password_confirmation" => 'required_with:password',
            'row_status' => [
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_PENDING])
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:600|min:2',
            'title' => 'nullable|max:1200|min:2',
            'page' => 'integer|gt:0',
            'page_size' => 'integer|gt:0',
            'industry_association_type_id' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "integer",
                Rule::in(IndustryAssociation::ROW_STATUSES),
            ],
        ], $customMessage);
    }


}
