<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\Rank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class RankService
 * @package App\Services
 */
class RankService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getRankList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $rowStatus = $request->query('row_status');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $rankBuilder */
        $rankBuilder = Rank::select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title_bn',
                'ranks.grade',
                'ranks.display_order',
                'ranks.organization_id',
                'organizations.title_en as organization_title_en',
                'rank_types.id as rank_type_id',
                'rank_types.title_en as rank_type_title_en',
                'ranks.row_status',
                'ranks.created_by',
                'ranks.updated_by',
                'ranks.created_at',
                'ranks.updated_at',
            ]
        );
        $rankBuilder->leftJoin('organizations', function ($join) use ($rowStatus) {
            $join->on('ranks.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
            if (!is_null($rowStatus)) {
                $join->where('organizations.row_status', $rowStatus);
            }
        });
        $rankBuilder->join('rank_types', function ($join) use ($rowStatus) {
            $join->on('ranks.rank_type_id', '=', 'rank_types.id')
                ->whereNull('rank_types.deleted_at');
            if (!is_null($rowStatus)) {
                $join->where('ranks.row_status', $rowStatus);
            }
        });
        $rankBuilder->orderBy('ranks.id', $order);

        if (!is_null($rowStatus)) {
            $rankBuilder->where('ranks.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $rankBuilder->where('ranks.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $rankBuilder->where('ranks.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $ranks */

        if (!is_null($paginate) || !is_null($limit)) {
            $limit = $limit ?: 10;
            $ranks = $rankBuilder->paginate($limit);
            $paginateData = (object)$ranks->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $ranks = $rankBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $ranks->toArray()['data'] ?? $ranks->toArray();
        $response['response_status'] = [
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
    public function getOneRank(int $id, Carbon $startTime): array
    {
        /** @var Builder $rankBuilder */
        $rankBuilder = Rank::select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title_bn',
                'ranks.grade',
                'ranks.display_order',
                'ranks.organization_id',
                'organizations.title_en as organization_title_en',
                'rank_types.id as rank_type_id',
                'rank_types.title_en as rank_type_title_en',
                'ranks.row_status',
                'ranks.created_by',
                'ranks.updated_by',
                'ranks.created_at',
                'ranks.updated_at',
            ]
        );
        $rankBuilder->leftJoin('organizations', function ($join) {
            $join->on('ranks.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
        });
        $rankBuilder->join('rank_types', function ($join) {
            $join->on('ranks.rank_type_id', '=', 'rank_types.id')
                ->whereNull('rank_types.deleted_at');
        });
        $rankBuilder->where('ranks.id', '=', $id);

        /** @var Rank $rank */
        $rank = $rankBuilder->first();

        return [
            "data" => $rank ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return Rank
     */
    public function store(array $data): Rank
    {
        $rank = new Rank();
        $rank->fill($data);
        $rank->save();
        return $rank;
    }

    /**
     * @param Rank $rank
     * @param array $data
     * @return Rank
     */
    public function update(Rank $rank, array $data): Rank
    {
        $rank->fill($data);
        $rank->save();
        return $rank;
    }

    /**
     * @param Rank $rank
     * @return bool
     */
    public function destroy(Rank $rank): bool
    {
        return $rank->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedRankList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $rowStatus = $request->query('row_status');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $rankBuilder */
        $rankBuilder = Rank::onlyTrashed()->select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title_bn',
                'ranks.grade',
                'ranks.display_order',
                'ranks.organization_id',
                'organizations.title_en as organization_title_en',
                'rank_types.id as rank_type_id',
                'rank_types.title_en as rank_type_title_en',
                'ranks.row_status',
                'ranks.created_by',
                'ranks.updated_by',
                'ranks.created_at',
                'ranks.updated_at',
            ]
        );
        $rankBuilder->leftJoin('organizations', 'ranks.organization_id', '=', 'organizations.id');
        $rankBuilder->join('rank_types', 'ranks.rank_type_id', '=', 'rank_types.id');
        $rankBuilder->orderBy('ranks.id', $order);


        if (!empty($titleEn)) {
            $rankBuilder->where('ranks.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $rankBuilder->where('ranks.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $ranks */

        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $ranks = $rankBuilder->paginate($limit);
            $paginateData = (object)$ranks->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $ranks = $rankBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $ranks->toArray()['data'] ?? $ranks->toArray();
        $response['response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param Rank $rank
     * @return bool
     */
    public function restore(Rank $rank): bool
    {
        return $rank->restore();
    }

    /**
     * @param Rank $rank
     * @return bool
     */
    public function forceDelete(Rank $rank): bool
    {
        return $rank->forceDelete();
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
                'max:500',
                'min:2'
            ],
            'rank_type_id' => [
                'required',
                'int',
                'exists:rank_types,id',
            ],
            'grade' => [
                'nullable',
                'string',
                'max:100',
            ],
            'display_order' => [
                'nullable',
                'int',
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}
