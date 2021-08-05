<?php

namespace App\Services;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

/**
 * Class ServiceService
 * @package App\Services
 */
class ServiceService
{

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getServiceList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Service|Builder $services */
        $services = Service::select(
            [
                'services.id as id',
                'services.title_en',
                'services.title_bn',
                'services.organization_id',
                'organizations.title_en as organization_title_en',
                'services.row_status',
                'services.created_by',
                'services.updated_by',
                'services.created_at',
                'services.updated_at',
            ]
        );
        $services->join('organizations', 'services.organization_id', '=', 'organizations.id');

        if (!empty($titleEn)) {
            $services->where('services.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $services->where('services.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $services = $services->paginate(10);
            $paginateData = (object)$services->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $services = $services->get();
        }

        $data = [];

        foreach ($services as $service) {
            $links['read'] = route('api.v1.services.read', ['id' => $service->id]);
            $links['update'] = route('api.v1.services.update', ['id' => $service->id]);
            $links['delete'] = route('api.v1.services.destroy', ['id' => $service->id]);
            $service['_links'] = $links;
            $data[] = $service->toArray();
        }
        return [
            "data" => $data? : null,
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
                    '_link' => route('api.v1.services.get-list')
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
    public function getOneService(int $id, Carbon $startTime): array
    {
        /** @var Service|Builder $service */
        $service = Service::select(
            [
                'services.id as id',
                'services.title_en',
                'services.title_bn',
                'services.organization_id',
                'organizations.title_en as organization_title_en',
                'services.row_status',
                'services.created_by',
                'services.updated_by',
                'services.created_at',
                'services.updated_at',
            ]
        );
        $service->join('organizations', 'services.organization_id', '=', 'organizations.id');
        $service->where('services.id', '=', $id);
        $service = $service->first();

        $links = [];
        if (!empty($service)) {
            $links['update'] = route('api.v1.services.update', ['id' => $id]);
            $links['delete'] = route('api.v1.services.destroy', ['id' => $id]);
        }

        return [
            "data" => $service ? : null,
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
     * @return Service
     */
    public function store(array $data): Service
    {
        $service = new Service();
        $service->fill($data);
        $service->save();
        return $service;
    }

    /**
     * @param Service $service
     * @param array $data
     * @return Service
     */
    public function update(Service $service, array $data): Service
    {
        $service->fill($data);
        $service->save();
        return $service;
    }

    /**
     * @param Service $service
     * @return Service
     */
    public function destroy(Service $service): Service
    {
        $service->row_status = Service::ROW_STATUS_DELETED;
        $service->save();
        $service->delete();
        return $service;
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     */
    public function validator(Request $request,int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:191',
                'min:2',
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 1000',
                'min:2',
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([Service::ROW_STATUS_ACTIVE, Service::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}
