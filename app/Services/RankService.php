<?php


namespace App\Services;
use App\Models\Rank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use PhpParser\Builder;

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
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Rank|Builder $ranks */
        $ranks = Rank::select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title_bn',
                'ranks.grade',
                'ranks.order',
                'organizations.title_en as organization_title_en',
                'rank_types.title_en as rank_type_title_en',
                'ranks.row_status',
                'ranks.created_at',
                'ranks.updated_at',
            ]
        );
        $ranks->leftJoin('organizations', 'ranks.organization_id', '=', 'organizations.id');
        $ranks->join('rank_types', 'ranks.rank_type_id', '=', 'rank_types.id');
        $ranks->orderBy('ranks.id', $order);

        if (!empty($titleEn)) {
            $ranks->where('ranks.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $ranks->where('ranks.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $ranks = $ranks->paginate(10);
            $paginateData = (object)$ranks->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $ranks = $ranks->get();
        }

        $data = [];
        foreach ($ranks as $rank) {
            $links['read'] = route('api.v1.ranks.read', ['id' => $rank->id]);
            $links['update'] = route('api.v1.ranks.update', ['id' => $rank->id]);
            $links['delete'] = route('api.v1.ranks.destroy', ['id' => $rank->id]);
            $rank['_links'] = $links;
            $data[] = $rank->toArray();
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

                "search" => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.ranks.get-list')
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
    public function getOneRank(int $id, Carbon $startTime): array
    {
        /** @var Rank|Builder $rank */
        $rank = Rank::select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title_bn',
                'ranks.grade',
                'ranks.order',
                'organizations.title_en as organization_title_en',
                'rank_types.title_en as rank_type_title_en',
                'ranks.row_status',
                'ranks.created_at',
                'ranks.updated_at',
            ]
        );
        $rank->leftJoin('organizations', 'ranks.organization_id', '=', 'organizations.id');
        $rank->join('rank_types', 'ranks.rank_type_id', '=', 'rank_types.id');
        $rank->where('ranks.id', '=', $id);
        $rank = $rank->first();

        $links = [];
        if (!empty($rank)) {
            $links['update'] = route('api.v1.ranks.update', ['id' => $id]);
            $links['delete'] = route('api.v1.ranks.destroy', ['id' => $id]);
        }

        return [
            "data" => $rank ? $rank : null,
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
     * @return Rank
     */
    public function destroy(Rank $rank): Rank
    {
        $rank->row_status = 99;
        $rank->save();
        return $rank;
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
            'order' => [
                'nullable',
                'int',
            ],
            'organization_id' => [
                'nullable',
                'int',
                'exists:organizations,id',
            ],
        ];
        return Validator::make($request->all(), $rules);
    }


}
