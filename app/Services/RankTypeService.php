<?php

namespace App\Services;

use App\Models\RankType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

        /** @var Builder $rankTypeBuilder */
        $rankTypeBuilder = RankType::select(
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

        if ($paginate) {
            $rankTypes = $rankTypeBuilder->paginate(10);
            $paginateData = (object)$rankTypes->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $rankTypes = $rankTypeBuilder->get();
        }
        $data = $rankTypes->toArray();

        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
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
            "data" => $rankType ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
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
                'max:5000',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'int',
                Rule::in([RankType::ROW_STATUS_ACTIVE, RankType::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}
