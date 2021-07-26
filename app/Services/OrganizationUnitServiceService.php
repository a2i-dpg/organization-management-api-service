<?php


namespace App\Services;

use App\Models\OrganizationUnitService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class OrganizationUnitServiceService
 * @package App\Services
 */
class OrganizationUnitServiceService
{

    /**
     * @param Request $request
     * @return array
     */
    public function getOrganizationUnitServiceList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $organizationUnitServices = OrganizationUnitService::select(
            [
                'organization_unit_services.id as id',
                'organizations.title_en as organization_title_en',
                'organization_units.title_en as organization_unit_title_en',
                'services.title_en as service_title_en',

            ]
        )
            ->join('organizations', 'organization_unit_services.organization_id', '=', 'organizations.id')
            ->join('organization_units', 'organization_unit_services.organization_unit_id', '=', 'organization_units.id')
            ->join('services', 'organization_unit_services.service_id', '=', 'services.id');
        //->orderBy('organization_unit_types.id', $order);

        if (!empty($titleEn)) {
            $organizationUnitServices->where('organization_unit_services.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationUnitServices->where('organization_unit_services.title_bn', 'like', '%' . $titleBn . '%');
        }


        if ($paginate) {
            $organizationUnitServices = $organizationUnitServices->paginate(10);
            $paginate_data = (object)$organizationUnitServices->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $organizationUnitServices = $organizationUnitServices->get();
        }

        $data = [];


        foreach ($organizationUnitServices as $organizationUnitService) {
            $_links['read'] = route('api.v1.organization-unit-services.read', ['id' => $organizationUnitService->id]);
            $_links['update'] = route('api.v1.organization-unit-services.update', ['id' => $organizationUnitService->id]);
            $_links['delete'] = route('api.v1.organization-unit-services.destroy', ['id' => $organizationUnitService->id]);
            $organizationUnitService['_links'] = $_links;
            $data[] = $organizationUnitService->toArray();
        }

        $response = [
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
                    '_link' => route('api.v1.organization-unit-services.get-list')

                ],

            ],

            "_page" => $page,
            "_order" => $order
        ];

        return $response;

    }

    /**
     * @param $id
     * @return array
     */
    public function getOneOrganizationUnitService($id)
    {
        $startTime = Carbon::now();
        $organizationUnitService = OrganizationUnitService::select(
            [
                'organization_unit_services.id as id',
                'organizations.title_en as organization_title_en',
                'organization_units.title_en as organization_unit_title_en',
                'services.title_en as service_title_en',
            ]
        )
            ->join('organizations', 'organization_unit_services.organization_id', '=', 'organizations.id')
            ->join('organization_units', 'organization_unit_services.organization_unit_id', '=', 'organization_units.id')
            ->join('services', 'organization_unit_services.service_id', '=', 'services.id')
            ->where('organization_unit_services.row_status', '=', OrganizationUnitService::ROW_STATUS_ACTIVE)
            ->where('organization_unit_services.id', '=', $id);
        $organizationUnitService = $organizationUnitService->first();

        $links = [];
        if (!empty($organizationUnitService)) {
            $links['update'] = route('api.v1.organization-unit-services.update', ['id' => $id]);
            $links['delete'] = route('api.v1.organization-unit-services.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $organizationUnitService ? $organizationUnitService : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];
        return $response;
    }

    /**
     * @param array $data
     * @return OrganizationUnitService
     */
    public function store(array $data): bool
    {
        $services = [];
        for ($i = 0; $i < count($data['service_id']); $i++) {
            $services[] = [
                'organization_id' => $data['organization_id'],
                'organization_unit_id' => $data['organization_unit_id'],
                'service_id' => $data['service_id'][$i]
            ];
        }
        return OrganizationUnitService::insert($services);
    }

    /**
     * @param OrganizationUnitService $organizationUnitService
     * @param array $data
     * @return OrganizationUnitService
     */
    public function update(OrganizationUnitService $organizationUnitService): OrganizationUnitService
    {
        return $this->destroy($organizationUnitService);
    }

    /**
     * @param OrganizationUnitService $organizationUnitService
     * @return OrganizationUnitService
     */
    public function destroy(OrganizationUnitService $organizationUnitService): OrganizationUnitService
    {
        $organizationUnitService->row_status = 99;
        $organizationUnitService->save();

        return $organizationUnitService;
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     */
    public function validator(Request $request)
    {
        $rules = [
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id',
            ],
            'organization_unit_id' => [
                'required',
                'int',
                'exists:organization_units,id',
            ],
            'service_id' => [
                'required',
                'exists:services,id',
            ],
        ];
        return Validator::make($request->all(), $rules);
    }

}
