<?php

namespace App\Services;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\OrganizationUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Service;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OrganizationUnitService
 * @package App\Services
 */
class OrganizationUnitService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganizationUnit(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $organizationId = $request['organization_id'] ?? "";
        $organizationUnitTypeId = $request['organization_unit_type_id'] ?? "";

        /** @var Builder $organizationUnitBuilder */
        $organizationUnitBuilder = OrganizationUnit::select([
            'organization_units.id',
            'organization_units.title_en',
            'organization_units.title',
            'organization_units.address',
            'organization_units.mobile',
            'organization_units.email',
            'organization_units.fax_no',
            'organization_units.contact_person_name',
            'organization_units.contact_person_name_en',
            'organization_units.contact_person_mobile',
            'organization_units.contact_person_email',
            'organization_units.contact_person_designation',
            'organization_units.contact_person_designation_en',
            'organization_units.employee_size',
            'organization_units.organization_unit_type_id',
            'organization_unit_types.title_en as organization_unit_type_title_en',
            'organization_unit_types.title as organization_unit_type_title',
            'organization_units.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title',
            'organization_units.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'organization_units.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'organization_units.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'organization_units.row_status',
            'organization_units.created_by',
            'organization_units.updated_by',
            'organization_units.created_at',
            'organization_units.updated_at',

        ])->byOrganization('organization_units');

        $organizationUnitBuilder->join('organizations', function ($join) use ($rowStatus) {
            $join->on('organization_units.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('organizations.row_status', $rowStatus);
            }
        });
        $organizationUnitBuilder->join('organization_unit_types', function ($join) use ($rowStatus) {
            $join->on('organization_units.organization_unit_type_id', '=', 'organization_unit_types.id')
                ->whereNull('organization_unit_types.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('organization_unit_types.row_status', $rowStatus);
            }
        });
        $organizationUnitBuilder->leftjoin('loc_divisions', function ($join) use ($rowStatus) {
            $join->on('organization_units.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $organizationUnitBuilder->leftjoin('loc_districts', function ($join) use ($rowStatus) {
            $join->on('organization_units.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $organizationUnitBuilder->leftjoin('loc_upazilas', function ($join) use ($rowStatus) {
            $join->on('organization_units.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $organizationUnitBuilder->orderBy('organization_units.id', $order);

        if (is_numeric($rowStatus)) {
            $organizationUnitBuilder->where('organization_units.row_status', $rowStatus);
        }
        if (is_numeric($organizationId)) {
            $organizationUnitBuilder->where('organization_units.organization_id', $organizationId);
        }
        if (is_numeric($organizationUnitTypeId)) {
            $organizationUnitBuilder->where('organization_units.organization_unit_type_id', $organizationUnitTypeId);
        }
        if (!empty($titleEn)) {
            $organizationUnitBuilder->where('organization_units.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $organizationUnitBuilder->where('organization_units.title', 'like', '%' . $title . '%');
        }

        /** @var  Collection $organizationUnits */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $organizationUnits = $organizationUnitBuilder->paginate($pageSize);
            $paginateData = (object)$organizationUnits->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizationUnits = $organizationUnitBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizationUnits->toArray()['data'] ?? $organizationUnits->toArray();
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
    public function getOneOrganizationUnit(int $id, Carbon $startTime): array
    {
        /** @var Builder $organizationUnitBuilder */
        $organizationUnitBuilder = OrganizationUnit::select([
            'organization_units.id',
            'organization_units.title_en',
            'organization_units.title',
            'organization_units.address',
            'organization_units.address_en',
            'organization_units.mobile',
            'organization_units.email',
            'organization_units.fax_no',
            'organization_units.contact_person_name',
            'organization_units.contact_person_name_en',
            'organization_units.contact_person_mobile',
            'organization_units.contact_person_email',
            'organization_units.contact_person_designation',
            'organization_units.contact_person_designation_en',
            'organization_units.employee_size',
            'organization_units.organization_unit_type_id',
            'organization_unit_types.title_en as organization_unit_type_title_en',
            'organization_unit_types.title as organization_unit_type_title',
            'organization_units.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title',
            'organization_units.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'organization_units.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'organization_units.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'organization_units.row_status',
            'organization_units.created_by',
            'organization_units.updated_by',
            'organization_units.created_at',
            'organization_units.updated_at',

        ]);
        $organizationUnitBuilder->with('services');
        $organizationUnitBuilder->join('organizations', function ($join) {
            $join->on('organization_units.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
        });
        $organizationUnitBuilder->join('organization_unit_types', function ($join) {
            $join->on('organization_units.organization_unit_type_id', '=', 'organization_unit_types.id')
                ->whereNull('organization_unit_types.deleted_at');
        });

        $organizationUnitBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('organization_units.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $organizationUnitBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('organization_units.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $organizationUnitBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('organization_units.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $organizationUnitBuilder->where('organization_units.id', $id);


        /** @var OrganizationUnit $organizationUnit */
        $organizationUnit = $organizationUnitBuilder->first();

        return [
            "data" => $organizationUnit ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param OrganizationUnit $organizationUnit
     * @param array $data
     * @return OrganizationUnit
     */
    public function update(OrganizationUnit $organizationUnit, array $data): OrganizationUnit
    {
        $organizationUnit->fill($data);
        $organizationUnit->save();
        return $organizationUnit;
    }

    /**
     * @param OrganizationUnit $organizationUnit
     * @return bool
     */
    public function destroy(OrganizationUnit $organizationUnit): bool
    {
        return $organizationUnit->delete();
    }

    /**
     * @param array $data
     * @return OrganizationUnit
     */
    public function store(array $data): OrganizationUnit
    {
        $organizationUnit = new OrganizationUnit();
        $organizationUnit->fill($data);
        $organizationUnit->save();
        return $organizationUnit;
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllTrashedOrganizationUnit(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $title = $request->query('title');
        $pageSize = $request->query('pageSize', BaseModel::DEFAULT_PAGE_SIZE);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $organizationUnitBuilder */
        $organizationUnitBuilder = OrganizationUnit::onlyTrashed()->select([
            'organization_units.id',
            'organization_units.title_en',
            'organization_units.title',
            'organization_units.address',
            'organization_units.mobile',
            'organization_units.email',
            'organization_units.fax_no',
            'organization_units.contact_person_name',
            'organization_units.contact_person_mobile',
            'organization_units.contact_person_email',
            'organization_units.contact_person_designation',
            'organization_units.employee_size',
            'organization_units.organization_unit_type_id',
            'organization_unit_types.title_en as organization_unit_type_title_en',
            'organization_unit_types.title as organization_unit_type_title',
            'organization_units.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title',
            'organization_units.loc_division_id',
            'organization_units.loc_district_id',
            'organization_units.loc_upazila_id',
            'organization_units.row_status',
            'organization_units.created_by',
            'organization_units.updated_by',
            'organization_units.created_at',
            'organization_units.updated_at',

        ]);
        $organizationUnitBuilder->join('organizations', 'organization_units.organization_id', '=', 'organizations.id');
        $organizationUnitBuilder->join('organization_unit_types', 'organization_units.organization_unit_type_id', '=', 'organization_unit_types.id');
        $organizationUnitBuilder->orderBy('organization_units.id', $order);


        if (!empty($titleEn)) {
            $organizationUnitBuilder->where('organization_units.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $organizationUnitBuilder->where('organization_types.title', 'like', '%' . $title . '%');
        }

        /** @var  Collection $organizationUnits */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $organizationUnits = $organizationUnitBuilder->paginate($pageSize);
            $paginateData = (object)$organizationUnits->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizationUnits = $organizationUnitBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizationUnits->toArray()['data'] ?? $organizationUnits->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param OrganizationUnit $organizationUnit
     * @return bool
     */
    public function restore(OrganizationUnit $organizationUnit): bool
    {
        return $organizationUnit->restore();
    }

    /**
     * @param OrganizationUnit $organizationUnit
     * @return bool
     */
    public function forceDelete(OrganizationUnit $organizationUnit): bool
    {
        return $organizationUnit->forceDelete();
    }


    /**
     * @param OrganizationUnit $organizationUnit
     * @param array $serviceIds
     * @return OrganizationUnit
     */
    public function assignService(OrganizationUnit $organizationUnit, array $serviceIds): OrganizationUnit
    {
        $validServices = Service::whereIn('id', $serviceIds)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $organizationUnit->services()->sync($validServices);
        return $organizationUnit;
    }


    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'title' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id,deleted_at,NULL',
            ],
            'organization_unit_type_id' => [
                'required',
                'integer',
                'exists:organization_unit_types,id,deleted_at,NULL',
            ],
            'loc_division_id' => [
                'nullable',
                'integer',
            ],
            'loc_district_id' => [
                'nullable',
                'integer',
            ],
            'loc_upazila_id' => [
                'nullable',
                'integer',
            ],
            'address' => [
                'nullable',
                'string',
                'max:191',
            ],
            'mobile' => [
                'nullable',
                'string',
                'max:15',
            ],
            'email' => [
                'nullable',
                'email',
                'max:191',
            ],
            'fax_no' => [
                'nullable',
                'string',
                'max:30',
            ],
            'contact_person_name' => [
                'nullable',
                'string',
                'max:500',
            ],
            'contact_person_name_en' => [
                'nullable',
                'string',
                'max:250',
            ],
            'contact_person_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],
            'contact_person_email' => [
                'nullable',
                'string',
                'email',
                'max:191'
            ],
            'contact_person_designation' => [
                'nullable',
                'string',
                'max:600',
            ],
            'contact_person_designation_en' => [
                'nullable',
                'string',
                'max:300',
            ],
            'employee_size' => [
                'required',
                'integer',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([Service::ROW_STATUS_ACTIVE, Service::ROW_STATUS_INACTIVE]),
            ]
        ];


        return Validator::make($request->all(), $rules);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function serviceValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $data = $request->all();
        $data["serviceIds"] = is_array($data['serviceIds']) ? $data['serviceIds'] : explode(',', $data['serviceIds']);
        $rules = [
            'serviceIds' => 'required|array|min:1',
            'serviceIds.*' => 'required|exists:services,id,deleted_at,NULL|integer|distinct'
        ];
        return Validator::make($data, $rules, $customMessage);
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
            'title_en' => 'nullable|max:300|min:2',
            'title' => 'nullable|max:600|min:2',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'organization_id' => 'nullable|exists:organizations,id,deleted_at,NULL|integer',
            'organization_unit_type_id' => 'nullable|exists:organization_unit_types,id,deleted_at,NULL|integer',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([Service::ROW_STATUS_ACTIVE, Service::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
