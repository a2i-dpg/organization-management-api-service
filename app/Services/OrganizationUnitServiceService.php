<?php

namespace App\Services;

use App\Models\OrganizationUnitService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Builder;

/**
 * Class OrganizationUnitServiceService
 * @package App\Services
 */
class OrganizationUnitServiceService
{

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getOrganizationUnitServiceList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var OrganizationUnitService|Builder $organizationUnitServices */
        $organizationUnitServices = OrganizationUnitService::select(
            [
                'organization_unit_services.id as id',
                'organizations.title_en as organization_title_en',
                'organization_units.title_en as organization_unit_title_en',
                'services.title_en as service_title_en',
                'organization_unit_services.row_status',
                'organization_unit_services.created_at',
                'organization_unit_services.updated_at',
            ]
        );
        $organizationUnitServices->join('organizations', 'organization_unit_services.organization_id', '=', 'organizations.id');
        $organizationUnitServices->join('organization_units', 'organization_unit_services.organization_unit_id', '=', 'organization_units.id');
        $organizationUnitServices->join('services', 'organization_unit_services.service_id', '=', 'services.id');

        if (!empty($titleEn)) {
            $organizationUnitServices->where('organization_unit_services.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationUnitServices->where('organization_unit_services.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $organizationUnitServices = $organizationUnitServices->paginate(10);
            $paginateData = (object)$organizationUnitServices->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $organizationUnitServices = $organizationUnitServices->get();
        }

        $data = [];
        foreach ($organizationUnitServices as $organizationUnitService) {
            $links['read'] = route('api.v1.organization-unit-services.read', ['id' => $organizationUnitService->id]);
            $links['update'] = route('api.v1.organization-unit-services.update', ['id' => $organizationUnitService->id]);
            $links['delete'] = route('api.v1.organization-unit-services.destroy', ['id' => $organizationUnitService->id]);
            $organizationUnitService['_links'] = $links;
            $data[] = $organizationUnitService->toArray();
        }

        return [
            "data" => $data,
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
                    '_link' => route('api.v1.organization-unit-services.get-list')
                ],
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
    public function getOneOrganizationUnitService(int $id, Carbon $startTime)
    {
        /** @var OrganizationUnitService|Builder $organizationUnitService */
        $organizationUnitService = OrganizationUnitService::select(
            [
                'organization_unit_services.id as id',
                'organizations.title_en as organization_title_en',
                'organization_units.title_en as organization_unit_title_en',
                'services.title_en as service_title_en',
                'organization_unit_services.row_status',
                'organization_unit_services.created_at',
                'organization_unit_services.updated_at',
            ]
        );
        $organizationUnitService->join('organizations', 'organization_unit_services.organization_id', '=', 'organizations.id');
        $organizationUnitService->join('organization_units', 'organization_unit_services.organization_unit_id', '=', 'organization_units.id');
        $organizationUnitService->join('services', 'organization_unit_services.service_id', '=', 'services.id');
        $organizationUnitService->where('organization_unit_services.id', '=', $id);
        $organizationUnitService = $organizationUnitService->first();

        $links = [];
        if (!empty($organizationUnitService)) {
            $links['update'] = route('api.v1.organization-unit-services.update', ['id' => $id]);
            $links['delete'] = route('api.v1.organization-unit-services.destroy', ['id' => $id]);
        }

        return [
            "data" => $organizationUnitService ? $organizationUnitService : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => $links,
        ];
    }

    /**
     * @param array $data
     * @return bool
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
     * @return \Illuminate\Contracts\Validation\Validator
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
