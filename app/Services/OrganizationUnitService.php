<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\OrganizationUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;

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

        /** @var OrganizationUnit|Builder $organizationUnits */
        $organizationUnits = OrganizationUnit::select([
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
            'organization_units.row_status',
            'organization_units.created_by',
            'organization_units.updated_by',
            'organization_units.created_at',
            'organization_units.updated_at',
//            'loc_divisions.title_en as division_name',
//            'loc_districts.title_en as district_name',
//            'loc_upazilas.title_en as upazila_name',


        ]);
        $organizationUnits->join('organizations', 'organization_units.organization_id', '=', 'organizations.id');
//        $organizationUnits->leftJoin('loc_divisions', 'organization_units.loc_division_id', '=', 'loc_divisions.id');
//        $organizationUnits->leftJoin('loc_districts', 'organization_units.loc_district_id', '=', 'loc_districts.id');
//        $organizationUnits->leftJoin('loc_upazilas', 'organization_units.loc_upazila_id', '=', 'loc_upazilas.id');
        $organizationUnits->join('organization_unit_types', 'organization_units.organization_unit_type_id', '=', 'organization_unit_types.id');

        if (!empty($titleEn)) {
            $organizationUnits->where('organization_units.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationUnits->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $organizationUnits = $organizationUnits->paginate(10);
            $paginateData = (object)$organizationUnits->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink = $paginateData->links;
        } else {
            $organizationUnits = $organizationUnits->get();
        }

        $data = [];
        foreach ($organizationUnits as $organizationUnit) {
            $links['read'] = route('api.v1.organization-units.read', ['id' => $organizationUnit->id]);
            $links['update'] = route('api.v1.organization-units.update', ['id' => $organizationUnit->id]);
            $links['delete'] = route('api.v1.organization-units.destroy', ['id' => $organizationUnit->id]);
            $organizationUnit['_links'] = $links;
            $data[] = $organizationUnit->toArray();
        }

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
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.organization-units.get-list')
                ]
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
        $links = [];
        /** @var OrganizationUnit|Builder $organizationUnit */
        $organizationUnit = OrganizationUnit::select([
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
            'organization_units.row_status',
            'organization_units.created_by',
            'organization_units.updated_by',
            'organization_units.created_at',
            'organization_units.updated_at',
//            'loc_divisions.title_en as division_name',
//            'loc_districts.title_en as district_name',
//            'loc_upazilas.title_en as upazila_name',

        ]);
        $organizationUnit->join('organizations', 'organization_units.organization_id', '=', 'organizations.id');
        $organizationUnit->where('organization_units.id', '=', $id);
        $organizationUnit->join('organization_unit_types', 'organization_units.organization_unit_type_id', '=', 'organization_unit_types.id');
//        $organizationUnit->leftJoin('loc_divisions', 'organization_units.loc_division_id', '=', 'loc_divisions.id');
//        $organizationUnit->leftJoin('loc_districts', 'organization_units.loc_district_id', '=', 'loc_districts.id');
//        $organizationUnit->leftJoin('loc_upazilas', 'organization_units.loc_upazila_id', '=', 'loc_upazilas.id');
        $organizationUnit = $organizationUnit->first();

        if (!empty($organizationUnit)) {
            $links = [
                'update' => route('api.v1.organization-units.update', ['id' => $id]),
                'delete' => route('api.v1.organization-units.destroy', ['id' => $id])
            ];
        }

        return [
            "data" => $organizationUnit ? $organizationUnit : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => $links
        ];
    }

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

//            'loc_division_id' => [
//                 'required',
//                 'int',
//                 'exists:loc_divisions,id',
//             ],
//             'loc_district_id' => [
//                 'required',
//                 'int',
//                 'exists:loc_districts,id',
//             ],
//             'loc_upazila_id' => [
//                 'required',
//                 'int',
//                 'exists:loc_upazilas,id',
//             ],
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
                'max:20',
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
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}
