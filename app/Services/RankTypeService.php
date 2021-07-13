<?php


namespace App\Services;

use App\Models\RankType;
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
     * @return mixed
     */
    public function getRankTypeList(Request $request): array
    {

        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $startTime = Carbon::now();
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $rankTypes = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'organizations.title_en as organization_title_en',
                'rank_types.row_status',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        )->leftJoin('organizations', 'rank_types.organization_id', '=', 'organizations.id')
            ->where('rank_types.row_status', '=', RankType::ROW_STATUS_ACTIVE)
            ->orderBy('rank_types.id', $order);


        if (!empty($titleEn)) {
            $rankTypes->where('rank_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $rankTypes->where('rank_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $rankTypes = $rankTypes->paginate(10);
            $paginate_data = (object)$rankTypes->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $rankTypes = $rankTypes->get();
        }
        foreach ($rankTypes as $rankType) {
            $action['read'] = route('api.v1.ranktypes.read', ['id' => $rankType->id]);
            $action['edit'] = route('api.v1.ranktypes.update', ['id' => $rankType->id]);
            $action['delete'] = route('api.v1.ranktypes.destroy', ['id' => $rankType->id]);
            $rankType['action'] = $action;
            $data[] = $rankType->toArray();
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
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    'link' => route('api.v1.ranktypes.getList')
                ],
            ],
                "_page" => $page,
                "_order" => $order
        ];

        return $response;
    }

    public function getOneRanktype($id): array
    {
        $startTime = Carbon::now();
        $rankType = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'organizations.title_en as organization_title_en',
                'rank_types.row_status',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        );
        $rankType->leftJoin('organizations', 'rank_types.organization_id', '=', 'organizations.id');
        $rankType->where('rank_types.id', '=', $id);
        $rankType->where('rank_types.row_status', '=', 1);
        $rankType = $rankType->first();

        $action = [];
        if (!empty($rankType)) {
            $action['edit'] = route('api.v1.ranktypes.update', ['id' => $id]);
            $action['delete'] = route('api.v1.ranktypes.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $rankType ? $rankType : [],
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

    public function store($data): RankType
    {
        $rankType = new RankType();
        $rankType->fill($data);
        $rankType->save();

        return $rankType;
    }

    public function update(RankType $rankType, array $data): RankType

    {
        $rankType->fill($data);
        $rankType->save();
        return $rankType;
    }


    public function destroy(RankType $rankType): RankType
    {
        $rankType->row_status = 99;
        $rankType->save();
        return $rankType;
    }


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
