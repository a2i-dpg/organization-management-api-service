<?php


namespace App\Services;

use App\Models\Rank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;



/**
 * Class RankService
 * @package App\Services
 */

class RankService
{
    /**
     *
     * @param Request $request
     * @return array
     */
    public function getRankList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
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
        )->leftJoin('organizations', 'ranks.organization_id', '=', 'organizations.id')
            ->join('rank_types', 'ranks.rank_type_id', '=', 'rank_types.id')
            ->where('ranks.row_status', '=', Rank::ROW_STATUS_ACTIVE)
            ->orderBy('ranks.id', $order);

        if (!empty($titleEn)) {
            $ranks->where('ranks.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $ranks->where('ranks.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $ranks = $ranks->paginate(10);
            $paginate_data = (object)$ranks->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $ranks = $ranks->get();
        }
        $action = [];
        foreach ($ranks as $rank) {
            $action['read'] = route('api.v1.ranks.read', ['id' => $rank->id]);
            $action['edit'] = route('api.v1.ranks.update', ['id' => $rank->id]);
            $action['delete'] = route('api.v1.ranks.destroy', ['id' => $rank->id]);
            $rank['action'] = $action;
            $data[] = $rank->toArray();
        }

        $response = [
            "data" => $data,
            "response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "links" => [
                'paginate' => $paginate_link,
                'parameters' => [
                    'title_en',
                    'title_bn'
                ],
                'link' => route('api.v1.ranks.getList')
            ],

            "_page" => $page,
            "_order" => $order
        ];

        return $response;
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneRank($id)
    {
        $startTime = Carbon::now();
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
        )->leftJoin('organizations', 'ranks.organization_id', '=', 'organizations.id')
            ->join('rank_types', 'ranks.rank_type_id', '=', 'rank_types.id')
            ->where('ranks.row_status', '=', 1)
            ->where('ranks.id', '=', $id);

        $rank = $rank->first();

        $action = [];
        if (!empty($rank)) {
            $action['edit'] = route('api.v1.ranks.update', ['id' => $id]);
            $action['delete'] = route('api.v1.ranks.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $rank ? $rank : [],
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "action" => $action,
        ];
        return $response;

    }

    /**
     * @param array $data
     * @return Rank
     */
    public function store(array $data): Rank
    {
        $locDistrict = new Rank();
        $locDistrict->fill($data);
        $locDistrict->save();

        return $locDistrict;
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
    public function validator(Request $request)
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
