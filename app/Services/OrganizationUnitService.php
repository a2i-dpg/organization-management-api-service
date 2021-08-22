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
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganizationUnit(Request $request, Carbon $startTime): array
    {
        $response = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $organizationUnitBuilder */
        $organizationUnitBuilder = OrganizationUnit::select([
            'organization_units.id',
            'organization_units.title_en',
            'organization_units.title_bn',
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
            'organization_units.organization_id',
            'organizations.title_en as organization_name',
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
        } elseif (!empty($titleBn)) {
            $organizationUnitBuilder->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var  Collection $organizationUnits */

        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $organizationUnits = $organizationUnitBuilder->paginate($limit);
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
        $response['response_status'] = [
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
            'organization_units.title_bn',
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
            'organization_units.organization_id',
            'organizations.title_en as organization_name',
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
        $organizationUnitBuilder->where('organization_units.id', '=', $id);
        $organizationUnitBuilder->join('organization_unit_types', 'organization_units.organization_unit_type_id', '=', 'organization_unit_types.id');


        /** @var OrganizationUnit $organizationUnit */
        $organizationUnit = $organizationUnitBuilder->first();
        return [
            "data" => $organizationUnit ?: null,
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
     * @param OrganizationUnit $organizationUnit
     * @param array $serviceIds
     * @return OrganizationUnit
     */
    public function assignService(OrganizationUnit $organizationUnit, array $serviceIds): OrganizationUnit
    {
        $validServices = Service::whereIn('id', $serviceIds)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $organizationUnit->services()->syncWithoutDetaching($validServices);
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
                'required',
                'string',
                'max:191',
                'min:2'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id',
            ],
            'organization_unit_type_id' => [
                'required',
                'int',
                'exists:organization_unit_types,id',
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
            'address' => [
                'nullable',
                'string',
                'max:191',
            ],
            'mobile' => [
                'nullable',
                'string',
                'max:20',
            ],
            'email' => [
                'nullable',
                'email',
                'max:191',
            ],
            'fax_no' => [
                'nullable',
                'string',
                'max:50',
            ],
            'contact_person_name' => [
                'nullable',
                'string',
                'max:191',
            ],
            'contact_person_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],
            'contact_person_email' => [
                'nullable',
                'string',
                'email'
            ],
            'contact_person_designation' => [
                'nullable',
                'string',
                'max:191',
            ],
            'employee_size' => [
                'required',
                'int',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
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
        $data["serviceIds"] = is_array($request['serviceIds']) ? $request['serviceIds'] : explode(',', $request['serviceIds']);
        $rules = [
            'serviceIds' => 'required|array|min:1',
            'serviceIds.*' => 'required|integer|distinct|min:1'
        ];
        return Validator::make($data, $rules);
    }
}
