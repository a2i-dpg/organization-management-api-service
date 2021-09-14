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

        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";

        /** @var Builder $serviceBuilder */
        $serviceBuilder = Service::select(
            [
                'services.id',
                'services.title_en',
                'services.title_bn',
                'services.row_status',
                'services.created_by',
                'services.updated_by',
                'services.created_at',
                'services.updated_at',
            ]
        );
        $serviceBuilder->orderBy('services.id', $order);

        if (is_numeric($rowStatus)) {
            $serviceBuilder->where('services.row_Status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $serviceBuilder->where('services.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $serviceBuilder->where('services.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $services */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
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
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $serviceBuilder */
        $serviceBuilder = Service::onlyTrashed()->select(
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
        $serviceBuilder->orderBy('services.id', $order);

        if (!empty($organizationId)) {
            $serviceBuilder->where('services.organization_id', '=', $organizationId);
        }
        if (!empty($titleEn)) {
            $serviceBuilder->where('services.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $serviceBuilder->where('services.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $services */

        if (!is_null($paginate) || !is_null($limit)) {
            $limit = $limit ?: 10;
            $services = $serviceBuilder->paginate($limit);
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
        return $this->forceDelete();
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC',
            'row_status.in' => 'Row status must be within 1 or 0'
        ];
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page' => 'numeric|gt:0',
            'limit' => 'numeric',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
