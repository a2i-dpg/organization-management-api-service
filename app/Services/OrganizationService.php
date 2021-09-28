<?php

namespace App\Services;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
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
        $titleBn = $request['title_bn'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $organizationTypeId = $request['organization_type_id'] ?? "";


        /** @var Builder organizationBuilder */
        $organizationBuilder = Organization::select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title_bn',
            'organizations.name_of_the_office_head',
            'organizations.name_of_the_office_head_designation',
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
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title_bn as loc_division_title_bn',
            'organizations.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title_bn as loc_district_title_bn',
            'organizations.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title_bn as loc_upazila_title_bn',
            'organizations.organization_type_id',
            'organization_types.title_en as organization_type_title_en',
            'organization_types.title_bn as organization_type_title_bn',
            'organizations.address',
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
            if (is_numeric($rowStatus)) {
                $join->where('loc_divisions.row_status', $rowStatus);
            }
        });
        $organizationBuilder->leftjoin('loc_districts', function ($join) use ($rowStatus) {
            $join->on('organizations.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_districts.row_status', $rowStatus);
            }
        });
        $organizationBuilder->leftjoin('loc_upazilas', function ($join) use ($rowStatus) {
            $join->on('organizations.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_upazilas.row_status', $rowStatus);
            }
        });

        $organizationBuilder->orderBy('organizations.id', $order);

        if (is_numeric($rowStatus)) {
            $organizationBuilder->where('organizations.row_status', $rowStatus);
        }

        if (is_numeric($organizationTypeId)) {
            $organizationBuilder->where('organizations.organization_type_id', $organizationTypeId);
        }

        if (!empty($titleEn)) {
            $organizationBuilder->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationBuilder->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $organizations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
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
            'organizations.title_bn',
            'organizations.name_of_the_office_head',
            'organizations.name_of_the_office_head_designation',
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
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title_bn as loc_division_title_bn',
            'organizations.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title_bn as loc_district_title_bn',
            'organizations.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title_bn as loc_upazila_title_bn',
            'organizations.organization_type_id',
            'organization_types.title_en as organization_type_title_en',
            'organization_types.title_bn as organization_type_title_bn',
            'organizations.address',
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
     * @throws RequestException
     */
    public function createUser(array $data)
    {
        $url = BaseModel::ORGANIZATION_USER_REGISTRATION_ENDPOINT_LOCAL . 'organization-or-institute-user-create';
        if (!in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            $url = BaseModel::ORGANIZATION_USER_REGISTRATION_ENDPOINT_REMOTE . 'organization-or-institute-user-create';
        }

        $username = str_replace(' ', '_', $data['title_en']);

        $userPostField = [
            'permission_sub_group_id' => $data['permission_sub_group_id'],
            'user_type' => BaseModel::ORGANIZATION_TYPE,
            'organization_id' => $data['organization_id'],
            'username' => $data['contact_person_mobile'],
            'name_en' => $data['contact_person_name'],
            'name_bn' => $data['contact_person_name'],
            'email' => $data['contact_person_email'],
            'mobile' => $data['contact_person_mobile'],
        ];

        return Http::retry(3)->post($url, $userPostField)->throw(function ($response, $e) {
            return $e;
        })->json();
    }


    /**
     * @param array $data
     * @return array|mixed
     * @throws RequestException
     */
    public function createRegisterUser(array $data)
    {
        $url = BaseModel::ORGANIZATION_USER_REGISTRATION_ENDPOINT_LOCAL . 'register-user';
        if (!in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            $url = BaseModel::ORGANIZATION_USER_REGISTRATION_ENDPOINT_REMOTE . 'register-user';
        }

        $userPostField = [
            'user_type' => BaseModel::ORGANIZATION_TYPE,
            'username' => $data['contact_person_mobile'],
            'organization_id' => $data['organization_id'],
            'name_en' => $data['contact_person_name'],
            'name_bn' => $data['contact_person_name'],
            'email' => $data['contact_person_email'],
            'mobile' => $data['contact_person_mobile'],
            'password' => $data['password']
        ];

        return Http::retry(3)->post($url, $userPostField)->throw(function ($response, $e) {
            return $e;
        })->json();
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
        $titleBn = $request->query('title_bn');
        $page_size = $request->query('page_size', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder organizationBuilder */
        $organizationBuilder = Organization::onlyTrashed()->select([
            'organizations.id',
            'organizations.title_en',
            'organizations.title_bn',
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
        } elseif (!empty($titleBn)) {
            $organizationBuilder->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $organizations */

        if (!is_null($paginate) || !is_null($page_size)) {
            $page_size = $page_size ?: 10;
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
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];
        $rules = [
            'permission_sub_group_id' => [
                'required',
                'numeric'
            ],
            'title_en' => [
                'required',
                'string',
                'max:300',
                'min:2',
            ],
            'title_bn' => [
                'required',
                'string',
                'max:1000',
                'min:2'
            ],
            'organization_type_id' => [
                'required',
                'int'
            ],
            "head_of_office" => [
                "required",
                "string"
            ],
            "head_of_office_designation" => [
                "nullable",
                "string"
            ],
            'domain' => [
                'nullable',
                'string',
                'max:191',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'unique:organizations,domain,' . $id
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'fax_no' => [
                'nullable',
                'string',
                'max: 50',
            ],
            'loc_division_id' => [
                'nullable',
                'int',
            ],
            'loc_district_id' => [
                'nullable',
                'int',
            ],
            'loc_upazila_id' => [
                'nullable',
                'int',
            ],
            'contact_person_mobile' => [
                'required',
                BaseModel::MOBILE_REGEX
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_designation' => [
                'required',
                'max: 300',
                "min:2"
            ],
            'contact_person_email' => [
                'required',
                'email'
            ],
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX
            ],
            'email' => [
                'required',
                'email',
            ],
            'logo' => [
                'required_if:' . $id . ',null',
                'string',
            ],
            'address' => [
                'required',
                'max: 1000',
                'min:2'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    public function registerOrganizationvalidator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:300',
                'min:2',
            ],
            'title_bn' => [
                'required',
                'string',
                'max:1000',
                'min:2'
            ],
            'organization_type_id' => [
                'required',
                'int'
            ],
            'email' => [
                'required',
                'email',
            ],
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX
            ],
            "name_of_the_office_head" => [
                "required",
                "string"
            ],
            "name_of_the_office_head_designation" => [
                "nullable",
                "string"
            ],
            'contact_person_mobile' => [
                'required',
                BaseModel::MOBILE_REGEX
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_designation' => [
                'required',
                'max: 300',
                "min:2"
            ],
            'contact_person_email' => [
                'required',
                'email'
            ],
            'address' => [
                'required',
                'max: 1000',
                'min:2'
            ],
            "password" => [
                'required_with:password_confirmation',
                'string',
                'confirmed'
            ],
            "password_confirmation" => 'required_with:password',
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
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page' => 'numeric|gt:0',
            'page_size' => 'numeric|gt:0',
            'organization_type_id' => 'numeric|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
