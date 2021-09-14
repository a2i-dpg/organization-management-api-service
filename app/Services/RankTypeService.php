<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\RankType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RankTypeService
 * @package App\Services
 */
class RankTypeService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return mixed
     */
    public function getRankTypeList(array $request, Carbon $startTime): array
    {
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";
        $organizationId = array_key_exists('organization_id', $request) ? $request['organization_id'] : "";

        /** @var Builder $rankTypeBuilder */
        $rankTypeBuilder = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'rank_types.organization_id',
                'organizations.title_en as organization_title_en',
                'organizations.title_bn as organization_title_bn',
                'rank_types.description',
                'rank_types.row_status',
                'rank_types.created_by',
                'rank_types.updated_by',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        );
        $rankTypeBuilder->leftJoin('organizations', function ($join) use ($rowStatus) {
            $join->on('rank_types.organization_id', '=', 'organizations.id')
                ->whereNUll('organizations.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('organizations.row_status', $rowStatus);
            }
        });
        $rankTypeBuilder->orderBy('rank_types.id', $order);

        if (is_numeric($rowStatus)) {
            $rankTypeBuilder->where('rank_types.row_status', $rowStatus);
        }
        if (is_numeric($organizationId)) {
            $rankTypeBuilder->where('rank_types.organization_id', $organizationId);
        }
        if (!empty($titleEn)) {
            $rankTypeBuilder->where('rank_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $rankTypeBuilder->where('rank_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $rankTypes */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $rankTypes = $rankTypeBuilder->paginate($pageSize);
            $paginateData = (object)$rankTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $rankTypes = $rankTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $rankTypes->toArray()['data'] ?? $rankTypes->toArray();
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
    public function getOneRankType(int $id, Carbon $startTime): array
    {
        /** @var Builder $rankTypeBuilder */
        $rankTypeBuilder = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'rank_types.description',
                'rank_types.organization_id',
                'organizations.title_en as organization_title_en',
                'organizations.title_bn as organization_title_bn',
                'rank_types.description',
                'rank_types.row_status',
                'rank_types.created_by',
                'rank_types.updated_by',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        );
        $rankTypeBuilder->leftJoin('organizations', 'rank_types.organization_id', '=', 'organizations.id');
        $rankTypeBuilder->where('rank_types.id', '=', $id);

        /** @var RankType $rankType */
        $rankType = $rankTypeBuilder->first();

        return [
            "data" => $rankType ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return RankType
     */
    public function store(array $data): RankType
    {
        $rankType = new RankType();
        $rankType->fill($data);
        $rankType->save();
        return $rankType;
    }

    /**
     * @param RankType $rankType
     * @param array $data
     * @return RankType
     */
    public function update(RankType $rankType, array $data): RankType
    {
        $rankType->fill($data);
        $rankType->save();
        return $rankType;
    }

    /**
     * @param RankType $rankType
     * @return bool
     */
    public function destroy(RankType $rankType): bool
    {
        return $rankType->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedRankTypeList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $rankTypeBuilder */
        $rankTypeBuilder = RankType::onlyTrashed()->select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'rank_types.organization_id',
                'organizations.title_en as organization_title_en',
                'rank_types.description',
                'rank_types.row_status',
                'rank_types.created_by',
                'rank_types.updated_by',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        );
        $rankTypeBuilder->leftJoin('organizations', 'rank_types.organization_id', '=', 'organizations.id');
        $rankTypeBuilder->orderBy('rank_types.id', $order);

        if (!empty($titleEn)) {
            $rankTypeBuilder->where('rank_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $rankTypeBuilder->where('rank_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $rankTypes */

        if (!is_null($paginate) || !is_null($limit)) {
            $limit = $limit ?: 10;
            $rankTypes = $rankTypeBuilder->paginate($limit);
            $paginateData = (object)$rankTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $rankTypes = $rankTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $rankTypes->toArray()['data'] ?? $rankTypes->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }


    /**
     * @param RankType $rankType
     * @return bool
     */
    public function restore(RankType $rankType): bool
    {
        return $rankType->restore();
    }

    /**
     * @param RankType $rankType
     * @return bool
     */
    public function forceDelete(RankType $rankType): bool
    {
        return $rankType->forceDelete();
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
                'max: 500',
                'min:2'
            ],
            'organization_id' => [
                'nullable',
                'int',
                'exists:organizations,id',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'int',
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
            'organization_id' => 'numeric|gt:0',
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
