<?php

namespace App\Services;

use App\Models\RankType;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

/**
 * Class RankTypeService
 * @package App\Services
 */
class RankTypeService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return mixed
     */
    public function getRankTypeList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var RankType|Builder $rankTypes */
        $rankTypes = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'rank_types.description',
                'organizations.title_en as organization_title_en',
                'rank_types.description',
                'rank_types.row_status',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        );
        $rankTypes->leftJoin('organizations', 'rank_types.organization_id', '=', 'organizations.id');
        $rankTypes->orderBy('rank_types.id', $order);

        if (!empty($titleEn)) {
            $rankTypes->where('rank_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $rankTypes->where('rank_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $rankTypes = $rankTypes->paginate(10);
            $paginateData = (object)$rankTypes->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $rankTypes = $rankTypes->get();
        }

        $data = [];
        foreach ($rankTypes as $rankType) {
            $links['read'] = route('api.v1.rank-types.read', ['id' => $rankType->id]);
            $links['edit'] = route('api.v1.rank-types.update', ['id' => $rankType->id]);
            $links['delete'] = route('api.v1.rank-types.destroy', ['id' => $rankType->id]);
            $rankType['_links'] = $links;
            $data[] = $rankType->toArray();
        }

        return [
            "data" => $data ? : null,
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
                    '_link' => route('api.v1.rank-types.get-list')
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
    public function getOneRankType(int $id, Carbon $startTime): array
    {
        /** @var RankType|Builder $rankType */
        $rankType = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'rank_types.description',
                'organizations.title_en as organization_title_en',
                'rank_types.description',
                'rank_types.row_status',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        );
        $rankType->leftJoin('organizations', 'rank_types.organization_id', '=', 'organizations.id');
        $rankType->where('rank_types.id', '=', $id);
        $rankType = $rankType->first();

        $links = [];
        if (!empty($rankType)) {
            $links['update'] = route('api.v1.rank-types.update', ['id' => $id]);
            $links['delete'] = route('api.v1.rank-types.destroy', ['id' => $id]);
        }

        return [
            "data" => $rankType ? : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];
    }

    /**
     * @param $data
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
     * @return RankType
     */
    public function destroy(RankType $rankType): RankType
    {
        $rankType->row_status = RankType::ROW_STATUS_DELETED;
        $rankType->save();
        return $rankType;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:191',
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 191',
            ],
            'organization_id' => [
                'nullable',
                'int',
                'exists:organizations,id', //always check for foreign key
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
        return Validator::make($request->all(), $rules);
    }

}
