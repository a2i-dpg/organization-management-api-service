<?php

namespace App\Services;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OrganizationService
 * @package App\Services
 */
class OrganizationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganization(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $organizationTypeId = $request['organization_type_id'] ?? "";


        /** @var Builder organizationBuilder */
        $organizationBuilder = Organization::select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title',
            'organizations.name_of_the_office_head',
            'organizations.name_of_the_office_head_en',
            'organizations.name_of_the_office_head_designation',
            'organizations.name_of_the_office_head_designation_en',
            'organizations.domain',
            'organizations.fax_no',
            'organizations.mobile',
            'organizations.email',
            'organizations.contact_person_name',
            'organizations.contact_person_name_en',
            'organizations.contact_person_mobile',
            'organizations.contact_person_email',
            'organizations.contact_person_designation',
            'organizations.contact_person_designation_en',
            'organizations.description',
            'organizations.description_en',
            'organizations.logo',
            'organizations.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'organizations.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'organizations.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'organizations.organization_type_id',
            'organization_types.title_en as organization_type_title_en',
            'organization_types.title as organization_type_title',
            'organizations.address',
            'organizations.address_en',
            'organizations.row_status',
            'organizations.created_by',
            'organizations.updated_by',
            'organizations.created_at',
            'organizations.updated_at'
        ]);
        $organizationBuilder->join('organization_types', function ($join) use ($rowStatus) {
            $join->on('organizations.organization_type_id', '=', 'organization_types.id')
                ->whereNull('organization_types.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('organization_types.row_status', $rowStatus);
            }
        });
        $organizationBuilder->leftjoin('loc_divisions', function ($join) use ($rowStatus) {
            $join->on('organizations.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $organizationBuilder->leftjoin('loc_districts', function ($join) use ($rowStatus) {
            $join->on('organizations.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $organizationBuilder->leftjoin('loc_upazilas', function ($join) use ($rowStatus) {
            $join->on('organizations.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $organizationBuilder->orderBy('organizations.id', $order);

        if (is_numeric($rowStatus)) {
            $organizationBuilder->where('organizations.row_status', $rowStatus);
        }

        if (is_numeric($organizationTypeId)) {
            $organizationBuilder->where('organizations.organization_type_id', $organizationTypeId);
        }

        if (!empty($titleEn)) {
            $organizationBuilder->where('organizations.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $organizationBuilder->where('organizations.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $organizations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $organizations = $organizationBuilder->paginate($pageSize);
            $paginateData = (object)$organizations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizations = $organizationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizations->toArray()['data'] ?? $organizations->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneOrganization(int $id, Carbon $startTime): array
    {
        /** @var Builder $organizationBuilder */
        $organizationBuilder = Organization::select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title',
            'organizations.name_of_the_office_head',
            'organizations.name_of_the_office_head_en',
            'organizations.name_of_the_office_head_designation',
            'organizations.name_of_the_office_head_designation_en',
            'organizations.domain',
            'organizations.fax_no',
            'organizations.mobile',
            'organizations.email',
            'organizations.contact_person_name',
            'organizations.contact_person_name_en',
            'organizations.contact_person_mobile',
            'organizations.contact_person_email',
            'organizations.contact_person_designation',
            'organizations.contact_person_designation_en',
            'organizations.description',
            'organizations.description_en',
            'organizations.logo',
            'organizations.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'organizations.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'organizations.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'organizations.location_latitude',
            'organizations.location_longitude',
            'organization_types.title_en as organization_type_title_en',
            'organization_types.title as organization_type_title',
            'organizations.address',
            'organizations.address_en',
            'organizations.row_status',
            'organizations.created_by',
            'organizations.updated_by',
            'organizations.created_at',
            'organizations.updated_at'
        ]);
        $organizationBuilder->join('organization_types', function ($join) {
            $join->on('organizations.organization_type_id', '=', 'organization_types.id')
                ->whereNull('organization_types.deleted_at');
        });
        $organizationBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('organizations.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');

        });
        $organizationBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('organizations.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $organizationBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('organizations.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');

        });
        $organizationBuilder->where('organizations.id', '=', $id);


        /** @var Organization $organization */
        $organization = $organizationBuilder->first();

        return [
            "data" => $organization ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ],
        ];
    }

    /**
     * @param Organization $organization
     * @param array $data
     * @return Organization
     */
    public function store(Organization $organization, array $data): Organization
    {
        $organization->fill($data);
        $organization->save();
        return $organization;
    }

    /**
     * @param array $data
     * @return array|mixed
     * @throws RequestException
     */
    public function createUser(array $data)
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'organization-or-institute-user-create';
        $userPostField = [
            'permission_sub_group_id' => $data['permission_sub_group_id'],
            'user_type' => BaseModel::ORGANIZATION_USER_TYPE,
            'organization_id' => $data['organization_id'],
            'username' => $data['contact_person_mobile'],
            'name_en' => $data['contact_person_name'],
            'name' => $data['contact_person_name'],
            'email' => $data['contact_person_email'],
            'mobile' => $data['contact_person_mobile'],
        ];
        Log::channel("org_reg")->info("Admin reg organization payload sent to core below");
        Log::channel("org_reg")->info(json_encode($userPostField));
        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->post($url, $userPostField)
            ->throw(function ($response, $e) {
                return $e;
            })
            ->json();
    }


    /**
     * @param array $data
     * @return mixed
     * @throws RequestException
     */
    public function createOpenRegisterUser(array $data): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'user-open-registration';

        Log::channel('org_reg')->info("organization registration core hit point");

        Log::channel('org_reg')->info($url);

        $userPostField = [
            'user_type' => BaseModel::ORGANIZATION_USER_TYPE,
            'username' => $data['contact_person_mobile'],
            'organization_id' => $data['organization_id'],
            'name_en' => $data['contact_person_name_en'],
            'name' => $data['contact_person_name'],
            'email' => $data['contact_person_email'],
            'mobile' => $data['contact_person_mobile'],
            'password' => $data['password']
        ];

        Log::channel('org_reg')->info("organization registration data provided to core", $userPostField);

        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->post($url, $userPostField)
            ->throw(function ($response, $e) {
                throw $e;
            })
            ->json();
    }

    /**
     * @param Organization $organization
     * @param array $data
     * @return Organization
     */
    public function update(Organization $organization, array $data): Organization
    {
        $organization->fill($data);
        $organization->save();
        return $organization;
    }

    /**
     * @param Organization $organization
     * @return bool
     */
    public function destroy(Organization $organization): bool
    {
        return $organization->delete();
    }


    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllTrashedOrganization(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $title = $request->query('title');
        $page_size = $request->query('page_size', BaseModel::DEFAULT_PAGE_SIZE);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder organizationBuilder */
        $organizationBuilder = Organization::onlyTrashed()->select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title',
            'organizations.domain',
            'organizations.fax_no',
            'organizations.mobile',
            'organizations.email',
            'organizations.contact_person_name',
            'organizations.contact_person_mobile',
            'organizations.contact_person_email',
            'organizations.contact_person_designation',
            'organizations.description',
            'organizations.logo',
            'organizations.loc_division_id',
            'organizations.loc_district_id',
            'organizations.loc_upazila_id',
            'organizations.organization_type_id',
            'organization_types.title_en as organization_types_title',
            'organizations.address',
            'organizations.row_status',
            'organizations.created_by',
            'organizations.updated_by',
            'organizations.created_at',
            'organizations.updated_at'
        ]);
        $organizationBuilder->join('organization_types', 'organizations.organization_type_id', '=', 'organization_types.id');
        $organizationBuilder->orderBy('organizations.id', $order);

        if (!empty($titleEn)) {
            $organizationBuilder->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $organizationBuilder->where('organization_types.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $organizations */

        if (is_numeric($paginate) || is_numeric($page_size)) {
            $page_size = $page_size ?: BaseModel::DEFAULT_PAGE_SIZE;
            $organizations = $organizationBuilder->paginate($page_size);
            $paginateData = (object)$organizations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizations = $organizationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizations->toArray()['data'] ?? $organizations->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param Organization $organization
     * @return bool
     */
    public function restore(Organization $organization): bool
    {
        return $organization->restore();
    }

    /**
     * @param Organization $organization
     * @return bool
     */
    public function forceDelete(Organization $organization): bool
    {
        return $organization->forceDelete();
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
            'organization_type_id' => [
                'required',
                'int',
                'exists:organization_types,id,deleted_at,NULL'
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
                'exists:loc_division,id,deleted_at,NULL'
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
                'integer',
            ],
            "location_longitude" => [
                'nullable',
                'integer',
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
                "string"
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
            'description' => [
                'nullable',
                'string',
            ],
            'description_en' => [
                'nullable',
                'string',
            ],
            'domain' => [
                'nullable',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'string',
                'max:191',
                Rule::unique('organizations', 'domain')
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
                Rule::in(Organization::ROW_STATUSES),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    public function registerOrganizationValidator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
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
            'organization_type_id' => [
                'required',
                'integer',
                'exists:organization_types,id,deleted_at,NULL'
            ],
            'email' => [
                'required',
                'email',
            ],
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'contact_person_mobile' => [
                'required',
                Rule::unique('organizations', 'contact_person_mobile')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    }),
                BaseModel::MOBILE_REGEX,

            ],
            "name_of_the_office_head" => [
                "required",
                "string",
                'max:600',
                'min:2'
            ],
            "name_of_the_office_head_en" => [
                "nullable",
                "string",
                'max:300',
                'min:2'
            ],
            "name_of_the_office_head_designation" => [
                "nullable",
                "string",
                'max:600',
                'min:2'
            ],
            "name_of_the_office_head_designation_en" => [
                "nullable",
                "string",
                'max:300',
                'min:2'
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
            'contact_person_email' => [
                'required',
                'email'
            ],
            'loc_division_id' => [
                'required',
                'integer',
                'exists:loc_division,id,deleted_at,NULL'
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
            "password" => [
                "required",
                "confirmed",
                Password::min(BaseModel::PASSWORD_MIN_LENGTH)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
            "password_confirmation" => 'required_with:password',
            'row_status' => [
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_PENDING])
            ]
        ];

        return Validator::make($request->all(), $rules);
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
            'organization_type_id' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "integer",
                Rule::in(Organization::ROW_STATUSES),
            ],
        ], $customMessage);
    }
}
