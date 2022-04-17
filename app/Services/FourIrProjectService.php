<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\FourIrProject;
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
class FourIrProjectService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getRankList(array $request, Carbon $startTime): array
    {
        $orgName = $request['organization_name'] ?? "";
        $orgNameEn = $request['organization_name_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectBuilder */
        $fourIrProjectBuilder = FourIrProject::select(
            [
                'four_ir_projects.id',
                'four_ir_projects.organization_name',
                'four_ir_projects.organization_name_en',
                'four_ir_projects.occupation_id',
                'four_ir_projects.start_date',
                'four_ir_projects.budget',
                'four_ir_projects.project_code',
                'four_ir_projects.file_path',
                'four_ir_projects.tasks',
                'four_ir_projects.guideline_file_path',
                'four_ir_projects.row_status',
                'four_ir_projects.created_by',
                'four_ir_projects.updated_by',
                'four_ir_projects.created_at',
                'four_ir_projects.updated_at'
            ]
        )->acl();

        $fourIrProjectBuilder->orderBy('four_ir_projects.id', $order);


        if (is_numeric($rowStatus)) {
            $fourIrProjectBuilder->where('ranks.row_status', $rowStatus);
        }
        if (is_numeric($organizationId)) {
            $fourIrProjectBuilder->where('ranks.organization_id', $organizationId);
        }
        if (!empty($titleEn)) {
            $fourIrProjectBuilder->where('ranks.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $fourIrProjectBuilder->where('ranks.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $ranks */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $ranks = $fourIrProjectBuilder->paginate($pageSize);
            $paginateData = (object)$ranks->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $ranks = $fourIrProjectBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $ranks->toArray()['data'] ?? $ranks->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIrProject
     */
    public function getOneRank(int $id): FourIrProject
    {
        /** @var FourIrProject|Builder $fourIrProjectBuilder */
        $fourIrProjectBuilder = FourIrProject::select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title',
                'ranks.grade',
                'ranks.display_order',
                'ranks.organization_id',
                'organizations.title_en as organization_title_en',
                'organizations.title as organization_title',
                'rank_types.id as rank_type_id',
                'rank_types.title_en as rank_type_title_en',
                'rank_types.title as rank_type_title',
                'ranks.row_status',
                'ranks.created_by',
                'ranks.updated_by',
                'ranks.created_at',
                'ranks.updated_at',
            ]
        );
        $fourIrProjectBuilder->leftJoin('organizations', function ($join) {
            $join->on('ranks.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
        });
        $fourIrProjectBuilder->join('rank_types', function ($join) {
            $join->on('ranks.rank_type_id', '=', 'rank_types.id')
                ->whereNull('rank_types.deleted_at');
        });
        $fourIrProjectBuilder->where('ranks.id', '=', $id);

        return $fourIrProjectBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIrProject
     */
    public function store(array $data): FourIrProject
    {
        $rank = new FourIrProject();
        $rank->fill($data);
        $rank->save();
        return $rank;
    }

    /**
     * @param FourIrProject $rank
     * @param array $data
     * @return FourIrProject
     */
    public function update(FourIrProject $rank, array $data): FourIrProject
    {
        $rank->fill($data);
        $rank->save();
        return $rank;
    }

    /**
     * @param FourIrProject $rank
     * @return bool
     */
    public function destroy(FourIrProject $rank): bool
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
        $title = $request->query('title');
        $pageSize = $request->query('pageSize', BaseModel::DEFAULT_PAGE_SIZE);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $fourIrProjectBuilder */
        $fourIrProjectBuilder = FourIrProject::onlyTrashed()->select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title',
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
        $fourIrProjectBuilder->leftJoin('organizations', 'ranks.organization_id', '=', 'organizations.id');
        $fourIrProjectBuilder->join('rank_types', 'ranks.rank_type_id', '=', 'rank_types.id');
        $fourIrProjectBuilder->orderBy('ranks.id', $order);


        if (!empty($titleEn)) {
            $fourIrProjectBuilder->where('ranks.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $fourIrProjectBuilder->where('ranks.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $ranks */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $ranks = $fourIrProjectBuilder->paginate($pageSize);
            $paginateData = (object)$ranks->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $ranks = $fourIrProjectBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $ranks->toArray()['data'] ?? $ranks->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param FourIrProject $rank
     * @return bool
     */
    public function restore(FourIrProject $rank): bool
    {
        return $rank->restore();
    }

    /**
     * @param FourIrProject $rank
     * @return bool
     */
    public function forceDelete(FourIrProject $rank): bool
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
                'max:600',
                'min:2'
            ],
            'rank_type_id' => [
                'exists:rank_types,id,deleted_at,NULL',
                'required',
                'int'
            ],
            'grade' => [
                'nullable',
                'string',
                'max:100',
            ],
            'display_order' => [
                'nullable',
                'integer',
            ],
            'organization_id' => [
                'required',
                'exists:organizations,id,deleted_at,NULL',
                'int'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([FourIrProject::ROW_STATUS_ACTIVE, FourIrProject::ROW_STATUS_INACTIVE]),
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
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'organization_id' => 'nullable||integer|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([FourIrProject::ROW_STATUS_ACTIVE, FourIrProject::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
