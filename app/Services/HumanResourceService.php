<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\HumanResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HumanResourceService
 * @package App\Services
 */
class HumanResourceService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getHumanResourceList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $titleBn = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $organizationId = $request['organization_id'] ?? "";
        $organizationUnitId = $request['organization_unit_id'] ?? "";

        /** @var Builder $humanResourceBuilder */
        $humanResourceBuilder = HumanResource::select([
            'human_resources.id',
            'human_resources.title_en',
            'human_resources.title',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.parent_id',
            'human_res_2.title_en as parent_title_en',
            'human_res_2.title as parent_title',
            'human_resources.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title',
            'human_resources.organization_unit_id',
            'organization_units.title_en as organization_unit_title_en',
            'organization_units.title as organization_unit_title',
            'human_resources.rank_id',
            'ranks.title_en as rank_title_en',
            'ranks.title as rank_title',
            'human_resources.status',
            'human_resources.row_status',
            'human_resources.created_by',
            'human_resources.updated_by',
            'human_resources.created_at',
            'human_resources.updated_at'

        ])->byOrganization('human_resources');

        $humanResourceBuilder->join('organizations', function ($join) use ($rowStatus) {
            $join->on('human_resources.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('organizations.row_status', $rowStatus);
            }
        });
        $humanResourceBuilder->join('organization_units', function ($join) use ($rowStatus) {
            $join->on('human_resources.organization_unit_id', '=', 'organization_units.id')
                ->whereNull('organization_units.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('organization_units.row_status', $rowStatus);
            }
        });
        $humanResourceBuilder->leftJoin('ranks', function ($join) use ($rowStatus) {
            $join->on('human_resources.rank_id', '=', 'ranks.id')
                ->whereNull('ranks.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('ranks.row_status', $rowStatus);
            }
        });
        $humanResourceBuilder->leftJoin('human_resources as human_res_2', function ($join) use ($rowStatus) {
            $join->on('human_resources.parent_id', '=', 'human_res_2.id')
                ->whereNull('human_res_2.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('human_res_2.row_status', $rowStatus);
            }
        });
        $humanResourceBuilder->orderBy('human_resources.id', $order);

        if (is_numeric($rowStatus)) {
            $humanResourceBuilder->where('human_resources.row_status', $rowStatus);
        }
        if (is_numeric($organizationId)) {
            $humanResourceBuilder->where('human_resources.organization_id', $organizationId);
        }
        if (is_numeric($organizationUnitId)) {
            $humanResourceBuilder->where('human_resources.organization_unit_id', $organizationUnitId);
        }
        if (!empty($titleEn)) {
            $humanResourceBuilder->where('human_resources.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $humanResourceBuilder->where('human_resources.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $humanResources */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $humanResources = $humanResourceBuilder->paginate($pageSize);
            $paginateData = (object)$humanResources->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $humanResources = $humanResourceBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $humanResources->toArray()['data'] ?? $humanResources->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneHumanResource(int $id, Carbon $startTime): array
    {
        /** @var Builder $humanResourceBuilder */
        $humanResourceBuilder = HumanResource::select([
            'human_resources.id',
            'human_resources.title_en',
            'human_resources.title',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.parent_id',
            'human_res_2.title_en as parent_title_en',
            'human_res_2.title as parent_title',
            'human_resources.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title',
            'human_resources.organization_unit_id',
            'organization_units.title_en as organization_unit_title_en',
            'organization_units.title as organization_unit_title',
            'human_resources.rank_id',
            'ranks.title_en as rank_title_en',
            'ranks.title as rank_title',
            'human_resources.status',
            'human_resources.row_status',
            'human_resources.created_by',
            'human_resources.updated_by',
            'human_resources.created_at',
            'human_resources.updated_at'
        ]);

        $humanResourceBuilder->join('organizations', function ($join) {
            $join->on('human_resources.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');

        });
        $humanResourceBuilder->join('organization_units', function ($join) {
            $join->on('human_resources.organization_unit_id', '=', 'organization_units.id')
                ->whereNull('organization_units.deleted_at');
        });
        $humanResourceBuilder->leftJoin('ranks', function ($join) {
            $join->on('human_resources.rank_id', '=', 'ranks.id')
                ->whereNull('ranks.deleted_at');
        });
        $humanResourceBuilder->leftJoin('human_resources as human_res_2', function ($join) {
            $join->on('human_resources.parent_id', '=', 'human_res_2.id')
                ->whereNull('human_res_2.deleted_at');
        });
        $humanResourceBuilder->where('human_resources.id', $id);


        /** @var HumanResource $humanResource */
        $humanResource = $humanResourceBuilder->first();

        return [
            "data" => $humanResource ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    /**
     * @param array $data
     * @return HumanResource
     */
    public function store(array $data): HumanResource
    {
        $humanResource = new HumanResource();
        $humanResource->fill($data);
        $humanResource->save();
        return $humanResource;
    }

    /**
     * @param HumanResource $humanResource
     * @param array $data
     * @return HumanResource
     */
    public function update(HumanResource $humanResource, array $data): HumanResource
    {
        $humanResource->fill($data);
        $humanResource->save();
        return $humanResource;
    }

    /**
     * @param HumanResource $humanResource
     * @return bool
     */
    public function destroy(HumanResource $humanResource): bool
    {
        return $humanResource->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedHumanResourceList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title');
        $page_size = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $humanResourceBuilder */
        $humanResourceBuilder = HumanResource::onlyTrashed()->select([
            'human_resources.id',
            'human_resources.title_en',
            'human_resources.title',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.parent_id',
            'human_res_2.title_en as parent',
            'human_resources.organization_id',
            'organizations.title_en as organization_name',
            'human_resources.organization_unit_id',
            'organization_units.title_en as organization_unit_name',
            'human_resources.human_resource_template_id',
            'human_resource_templates.title_en as human_resource_template_name',
            'human_resources.rank_id',
            'ranks.title_en as rank_title_en',
            'human_resources.status',
            'human_resources.row_status',
            'human_resources.created_by',
            'human_resources.updated_by',
            'human_resources.created_at',
            'human_resources.updated_at',
        ]);

        $humanResourceBuilder->join('human_resource_templates', 'human_resources.human_resource_template_id', '=', 'human_resource_templates.id');
        $humanResourceBuilder->join('organizations', 'human_resources.organization_id', '=', 'organizations.id');
        $humanResourceBuilder->join('organization_units', 'human_resources.organization_unit_id', '=', 'organization_units.id');
        $humanResourceBuilder->leftJoin('ranks', 'human_resources.rank_id', '=', 'ranks.id');
        $humanResourceBuilder->leftJoin('human_resources as human_res_2', 'human_resources.parent_id', '=', 't2.id');
        $humanResourceBuilder->orderBy('human_resource_templates.id', $order);

        if (!empty($titleEn)) {
            $humanResourceBuilder->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $humanResourceBuilder->where('human_resource_templates.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $humanResources */

        if ($paginate || $page_size) {
            $page_size = $page_size ?: 10;
            $humanResources = $humanResourceBuilder->paginate($page_size);
            $paginateData = (object)$humanResources->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $humanResources = $humanResourceBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $humanResources->toArray()['data'] ?? $humanResources->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param HumanResource $humanResource
     * @return bool
     */
    public function restore(HumanResource $humanResource): bool
    {
        return $humanResource->restore();
    }

    /**
     * @param HumanResource $humanResource
     * @return bool
     */
    public function forceDelete(HumanResource $humanResource): bool
    {
        return $humanResource->forceDelete();
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
                'message' => 'Row status must be either 1 or 0'
            ]
        ];
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max: 300',
                'min:2'
            ],
            'title' => [
                'required',
                'string',
                'max: 600',
                'min:2'
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id'
            ],
            'organization_unit_id' => [
                'required',
                'int',
                'exists:organization_units,id'
            ],
            'parent_id' => [
                'nullable',
                'int',
                'exists:human_resources,id'
            ],
            'rank_id' => [
                'nullable',
                'int',
                'exists:ranks,id'
            ],
            'display_order' => [
                'required',
                'int',
                'min:0',
            ],
            'is_designation' => [
                'required',
                'int',
            ],
            'status' => [
                'int',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
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
            'title_en' => 'nullable|max:300|min:2',
            'title' => 'nullable|max:600|min:2',
            'page' => 'integer|gt:0',
            'organization_id' => 'integer|exists:organizations,id',
            'organization_unit_id' => 'integer|exists:organization_units,id',
            'page_size' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
