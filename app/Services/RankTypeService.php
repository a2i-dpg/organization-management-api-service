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
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $organizationId = $request['organization_id'] ?? "";

        /** @var Builder $rankTypeBuilder */
        $rankTypeBuilder = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title',
                'rank_types.organization_id',
                'organizations.title_en as organization_title_en',
                'organizations.title as organization_title',
                'rank_types.description_en',
                'rank_types.description',
                'rank_types.row_status',
                'rank_types.created_by',
                'rank_types.updated_by',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        )->byOrganization('rank_types');

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
        }
        if (!empty($title)) {
            $rankTypeBuilder->where('rank_types.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $rankTypes */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
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
                'rank_types.title',
                'rank_types.description',
                'rank_types.description_en',
                'rank_types.organization_id',
                'organizations.title_en as organization_title_en',
                'organizations.title as organization_title',
                'rank_types.description_en',
                'rank_types.description',
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
        $title = $request->query('title');
        $pageSize = $request->query('page_size', BaseModel::DEFAULT_PAGE_SIZE);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $rankTypeBuilder */
        $rankTypeBuilder = RankType::onlyTrashed()->select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title',
                'rank_types.organization_id',
                'organizations.title_en as organization_title_en',
                'rank_types.description',
                'rank_types.description_en',
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
        } elseif (!empty($title)) {
            $rankTypeBuilder->where('rank_types.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $rankTypes */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
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
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'title' => [
                'required',
                'string',
                'max: 600',
                'min:2'
            ],
            'organization_id' => [
                'nullable',
                'integer',
                'exists:organizations,id,deleted_at,NULL'
            ],
            'description' => [
                'nullable',
                'string',
                'max: 600',
            ],
            'description_en' => [
                'nullable',
                'string',
                'max: 300',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                'integer',
                Rule::in([RankType::ROW_STATUS_ACTIVE, RankType::ROW_STATUS_INACTIVE]),
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
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:300|min:2',
            'title' => 'nullable|max:600|min:2',
            'organization_id' => 'nullable||integer|gt:0',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([RankType::ROW_STATUS_ACTIVE, RankType::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
