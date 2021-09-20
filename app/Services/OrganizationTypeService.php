<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\OrganizationType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OrganizationTypeService
 * @package App\Services
 */
class OrganizationTypeService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllOrganizationType(array $request, Carbon $startTime): array
    {
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";


        /** @var Builder $organizationTypeBuilder */
        $organizationTypeBuilder = OrganizationType::select([
            'organization_types.id',
            'organization_types.title_en',
            'organization_types.title_bn',
            'organization_types.is_government',
            'organization_types.row_status',
            'organization_types.created_by',
            'organization_types.updated_by',
            'organization_types.created_at',
            'organization_types.updated_at'
        ]);
        $organizationTypeBuilder->orderBy('organization_types.id', $order);

        if (is_numeric($rowStatus)) {
            $organizationTypeBuilder->where('organization_types.row_status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $organizationTypeBuilder->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationTypeBuilder->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $organizationTypes */

        if (!is_null($paginate) || !is_null($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $organizationTypes = $organizationTypeBuilder->paginate($pageSize);
            $paginateData = (object)$organizationTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizationTypes = $organizationTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizationTypes->toArray()['data'] ?? $organizationTypes->toArray();
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
    public function getOneOrganizationType(int $id, carbon $startTime): array
    {
        /** @var Builder $organizationTypeBuilder */
        $organizationTypeBuilder = OrganizationType::select([
            'organization_types.id',
            'organization_types.title_en',
            'organization_types.title_bn',
            'organization_types.is_government',
            'organization_types.row_status',
            'organization_types.created_by',
            'organization_types.updated_by',
            'organization_types.created_at',
            'organization_types.updated_at'
        ]);
        $organizationTypeBuilder->where('organization_types.id', '=', $id);


        /** @var OrganizationType $organizationType */
        $organizationType = $organizationTypeBuilder->first();

        return [
            "data" => $organizationType ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return OrganizationType
     */
    public function store(array $data): OrganizationType
    {
        $organizationType = new OrganizationType();
        $organizationType->fill($data);
        $organizationType->save();
        return $organizationType;
    }

    /**
     * @param OrganizationType $organizationType
     * @param array $data
     * @return OrganizationType
     */
    public function update(OrganizationType $organizationType, array $data): OrganizationType
    {
        $organizationType->fill($data);
        $organizationType->save();
        return $organizationType;
    }

    /**
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function destroy(OrganizationType $organizationType): bool
    {
        return $organizationType->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllTrashedOrganizationUnit(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $pageSize = $request->query(' $pageSize', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $organizationTypeBuilder */
        $organizationTypeBuilder = OrganizationType::onlyTrashed()->select([
            'organization_types.id',
            'organization_types.title_en',
            'organization_types.title_bn',
            'organization_types.is_government',
            'organization_types.row_status',
            'organization_types.created_by',
            'organization_types.updated_by',
            'organization_types.created_at',
            'organization_types.updated_at'
        ]);
        $organizationTypeBuilder->orderBy('organization_types.id', $order);

        if (!empty($titleEn)) {
            $organizationTypeBuilder->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationTypeBuilder->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $organizationTypes */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $organizationTypes = $organizationTypeBuilder->paginate($pageSize);
            $paginateData = (object)$organizationTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $organizationTypes = $organizationTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $organizationTypes->toArray()['data'] ?? $organizationTypes->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function restore(OrganizationType $organizationType): bool
    {
        return $organizationType->restore();
    }

    /**
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function forceDelete(OrganizationType $organizationType): bool
    {
        return $organizationType->forceDelete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
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
                'max:191',
                'min:2',
                'required',
                'string'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:400',
                'min:2',
            ],
            'is_government' => [
                'nullable',
                'boolean'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
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
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page' => 'numeric|gt:0',
            'pageSize' => 'numeric',
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
