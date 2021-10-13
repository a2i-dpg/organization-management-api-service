<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Service;
use Carbon\Carbon;
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
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getServiceList(array $request, Carbon $startTime): array
    {

        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $serviceBuilder */
        $serviceBuilder = Service::select(
            [
                'services.id',
                'services.title_en',
                'services.title',
                'services.row_status',
                'services.created_by',
                'services.updated_by',
                'services.created_at',
                'services.updated_at',
            ]
        );
        $serviceBuilder->orderBy('services.id', $order);

        if (is_int($rowStatus)) {
            $serviceBuilder->where('services.row_Status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $serviceBuilder->where('services.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $serviceBuilder->where('services.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $services */

        if (is_int($paginate) || is_int($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $services = $serviceBuilder->paginate($pageSize);
            $paginateData = (object)$services->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $services = $serviceBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $services->toArray()['data'] ?? $services->toArray();
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
    public function getOneService(int $id, Carbon $startTime): array
    {
        /** @var Builder $serviceBuilder */
        $serviceBuilder = Service::select(
            [
                'services.id',
                'services.title_en',
                'services.title',
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
            "data" => $service ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
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
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedServiceList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $title = $request->query('title');
        $pageSize = $request->query('page_size', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $serviceBuilder */
        $serviceBuilder = Service::onlyTrashed()->select(
            [
                'services.id as id',
                'services.title_en',
                'services.title',
                'services.row_status',
                'services.created_by',
                'services.updated_by',
                'services.created_at',
                'services.updated_at',
            ]
        );
        $serviceBuilder->orderBy('services.id', $order);

        if (!empty($organizationId)) {
            $serviceBuilder->where('services.organization_id', '=', $organizationId);
        }
        if (!empty($titleEn)) {
            $serviceBuilder->where('services.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $serviceBuilder->where('services.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $services */

        if (!is_int($paginate) || !is_int($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $services = $serviceBuilder->paginate($pageSize);
            $paginateData = (object)$services->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $services = $serviceBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $services->toArray()['data'] ?? $services->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param Service $service
     * @return bool
     */
    public function restore(Service $service): bool
    {
        return $service->restore();
    }

    /**
     * @param Service $service
     * @return bool
     */
    public function forceDelete(Service $service): bool
    {
        return $service->forceDelete();
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
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
            'title_en' => [
                'nullable',
                'string',
                'max:500',
                'min:2',
            ],
            'title' => [
                'required',
                'string',
                'max: 1000',
                'min:2',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([Service::ROW_STATUS_ACTIVE, Service::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
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
            'title_en' => 'nullable|max:500|min:2',
            'title' => 'nullable|max:1000|min:2',
            'page' => 'integer|gt:0',
            'page_size' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "integer",
                Rule::in([Service::ROW_STATUS_ACTIVE, Service::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
