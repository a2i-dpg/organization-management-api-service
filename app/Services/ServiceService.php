<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

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
        $organizationId = $request->query('organization_id');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $serviceBuilder */
        $serviceBuilder = Service::select(
            [
                'services.id as id',
                'services.title_en',
                'services.title_bn',
                'services.row_status',
                'services.created_by',
                'services.updated_by',
                'services.created_at',
                'services.updated_at',
            ]
        );

        if (!empty($organizationId)) {
            $serviceBuilder->where('services.organization_id', '=', $organizationId);
        }
        if (!empty($titleEn)) {
            $serviceBuilder->where('services.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $serviceBuilder->where('services.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $services */
        if ($paginate) {
            $services = $serviceBuilder->paginate(10);
            $paginateData = (object)$services->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $services = $serviceBuilder->get();
        }

        $data = $services->toArray();

        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
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
    public function getOneService(int $id, Carbon $startTime): array
    {
        /** @var Builder $serviceBuilder */
        $serviceBuilder = Service::select(
            [
                'services.id as id',
                'services.title_en',
                'services.title_bn',
                'services.row_status',
                'services.created_by',
                'services.updated_by',
                'services.created_at',
                'services.updated_at',
            ]
        );
        $serviceBuilder->where('services.id', '=', $id);

        /** @var  Service $service */
        $service = $serviceBuilder->first();

        return [
            "data" => $service ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
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
     * @return bool
     */
    public function destroy(Service $service): bool
    {
        return $service->delete();
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
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
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}
