<?php


namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\OrganizationUnit;
use Illuminate\Support\Facades\Validator;

/**
 * Class OrganizationUnitService
 * @package App\Services
 */
class OrganizationUnitService
{
    public function getAllOrganizationUnit(Request $request): array
    {
        $paginate_link = [];
        $page = [];
        $startTime = Carbon::now();

        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

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
                    'organization_units.row_status',
                    'organization_units.created_at',
                    'organization_units.updated_at',
                    'organizations.title_en as organization_name',
//                     'loc_divisions.title_en as division_name',
//                     'loc_districts.title_en as district_name',
//                     'loc_upazilas.title_en as upazila_name',
                     'organization_unit_types.title_en as organization_unit_name'
                ]);

                $organizationUnits->join('organizations', 'organization_units.organization_id', '=', 'organizations.id');
//                 $organizationUnits->leftJoin('loc_divisions', 'organization_units.loc_division_id', '=', 'loc_divisions.id');
//                 $organizationUnits->leftJoin('loc_districts', 'organization_units.loc_district_id', '=', 'loc_districts.id');
//                 $organizationUnits->leftJoin('loc_upazilas', 'organization_units.loc_upazila_id', '=', 'loc_upazilas.id');
                 $organizationUnits->join('organization_unit_types', 'organization_units.organization_unit_type_id', '=', 'organization_unit_types.id');


         if (!empty($titleEn)) {
                    $organizationUnits->where('organization_units.title_en', 'like', '%' . $titleEn . '%');
                } elseif (!empty($titleBn)) {
                    $organizationUnits->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
                }


         if ($paginate) {
                     $organizationUnits = $organizationUnits->paginate(10);
                     $paginate_data = (object)$organizationUnits->toArray();
                     $page = [
                         "size" => $paginate_data->per_page,
                         "total_element" => $paginate_data->total,
                         "total_page" => $paginate_data->last_page,
                         "current_page" => $paginate_data->current_page
                     ];
                     $paginate_link = $paginate_data->links;
                 } else {
                     $organizationUnits = $organizationUnits->get();
                 }

         $data = [];
                 foreach ($organizationUnits as $organizationUnit) {
                     $_links['read'] = route('api.v1.organization-units.read', ['id' => $organizationUnit->id]);
                     $_links['update'] = route('api.v1.organization-units.update', ['id' => $organizationUnit->id]);
                     $_links['delete'] = route('api.v1.organization-units.destroy', ['id' => $organizationUnit->id]);
                     $organizationUnit['_links'] = $_links;
                     $data[] = $organizationUnit->toArray();

                 }


          return [
                     "data" => $data,
                     "_response_status" => [
                         "success" => true,
                         "code" => JsonResponse::HTTP_OK,
                         "message" => "Job finished successfully.",
                         "started" => $startTime,
                         "finished" => Carbon::now(),
                     ],
                     "_links" => [
                         'paginate' => $paginate_link,
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

    public function getOneOrganizationUnit($id): array
    {

        $startTime = Carbon::now();
        $links = [];
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
            'organization_units.row_status',
            'organization_units.created_at',
            'organization_units.updated_at',
            'organizations.title_en as organization_name',
//                     'loc_divisions.title_en as division_name',
//                     'loc_districts.title_en as district_name',
//                     'loc_upazilas.title_en as upazila_name',
            'organization_unit_types.title_en as organization_unit_name'
        ]);
        $organizationUnit->join('organizations', 'organization_units.organization_id', '=', 'organizations.id');
        $organizationUnit->where('organization_units.id','=', $id);
        $organizationUnit->join('organization_unit_types', 'organization_units.organization_unit_type_id', '=', 'organization_unit_types.id');
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
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
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


    public function destroy(OrganizationUnit $organizationUnit): OrganizationUnit
        {
            $organizationUnit->row_status = 99;
            $organizationUnit->save();
            return $organizationUnit;

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
         * @param array $data
         * @return OrganizationUnit
         */



        public function validator(Request $request, $id = null): \Illuminate\Contracts\Validation\Validator
             {
                 $rules = [
                     'title_en' => [
                         'required'
                     ],
                     'title_bn' => [
                         'required'
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
//                      'loc_division_id' => [
//                          'required',
//                          'int',
//                          'exists:loc_divisions,id',
//                      ],
//                      'loc_district_id' => [
//                          'required',
//                          'int',
//                          'exists:loc_districts,id',
//                      ],
//                      'loc_upazila_id' => [
//                          'required',
//                          'int',
//                          'exists:loc_upazilas,id',
//                      ],
//                      'address' => [
//                          'nullable',
//                          'string',
//                          'max:191',
//                      ],
//                      'mobile' => [
//                          'nullable',
//                          'string',
//                          'max:20',
//                      ],
//                      'email' => [
//                          'nullable',
//                          'string',
//                          'max:191',
//                      ],
//                      'fax_no' => [
//                          'nullable',
//                          'string',
//                          'max:50',
//                      ],
//                      'contact_person_name' => [
//                          'nullable',
//                          'string',
//                          'max:191',
//                      ],
//                      'contact_person_mobile' => [
//                          'nullable',
//                          'string',
//                          'max:20',
//                      ],
//                      'contact_person_designation' => [
//                          'nullable',
//                          'string',
//                          'max:191',
//                      ],
//                      'employee_size' => [
//                          'required',
//                          'int',
//                      ],
//                      'row_status' => [
//                          Rule::requiredIf(function () use ($id) {
//                              return !empty($id);
//                          }),
//                          'int',
//                          'exists:row_status,code',
//                      ],
                 ];

//                  return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

                 return Validator::make($request->all(), $rules);

             }
}
