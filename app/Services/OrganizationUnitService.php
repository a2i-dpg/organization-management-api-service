<?php

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\OrganizationUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Service;

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
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
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
            'loc_divisions.id',
            'loc_districts.id',
            'loc_upazilas.id',

            'organization_units.row_status',
            'organization_units.created_by',
            'organization_units.updated_by',
            'organization_units.created_at',
            'organization_units.updated_at',

        ]);
        $organizationUnitBuilder->join('organizations', 'organization_units.organization_id', '=', 'organizations.id');
        $organizationUnitBuilder->join('organization_unit_types', 'organization_units.organization_unit_type_id', '=', 'organization_unit_types.id');

        if (!empty($titleEn)) {
            $organizationUnitBuilder->where('organization_units.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationUnitBuilder->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var  Collection $organizationUnits */

        if ($paginate) {
            $organizationUnits = $organizationUnitBuilder->paginate(10);
            $paginateData = (object)$organizationUnits->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink = $paginateData->links;
        } else {
            $organizationUnits = $organizationUnitBuilder->get();
        }

        $data = $organizationUnits->toArray();

        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => [
                'paginate' => $paginateLink,
            ],
            "_page" => $page,
            "_order" => $order
        ];
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
            'loc_divisions.id',
            'loc_districts.id',
            'loc_upazilas.id',
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
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
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
                'string',
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
                'regex: /\S+@\S+\.\S+/'
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
                Rule::in([OrganizationUnit::ROW_STATUS_ACTIVE, OrganizationUnit::ROW_STATUS_INACTIVE]),
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
        $data = [
            'serviceIds' => explode(',', $request['serviceIds'])
        ];
        $rules = [
            'serviceIds' => 'required|array|min:1',
            'serviceIds.*' => 'required|integer|distinct|min:1'
        ];
        return Validator::make($data, $rules);
    }
}
