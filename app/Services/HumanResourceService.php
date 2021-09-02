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
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";

        /** @var Builder $humanResourceBuilder */
        $humanResourceBuilder = HumanResource::select([
            'human_resources.id',
            'human_resources.title_en',
            'human_resources.title_bn',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.parent_id',
            't2.title_en as parent_title_en',
            't2.title_bn as parent_title_bn',
            'human_resources.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title_bn as organization_title_bn',
            'human_resources.organization_unit_id',
            'organization_units.title_en as organization_unit_title_en',
            'organization_units.title_bn as organization_unit_title_bn',
            'human_resources.human_resource_template_id',
            'human_resource_templates.title_en as human_resource_template_title_en',
            'human_resource_templates.title_bn as human_resource_template_title_bn',
            'human_resources.rank_id',
            'ranks.title_en as rank_title_en',
            'ranks.title_bn as rank_title_bn',
            'human_resources.status',
            'human_resources.row_status',
            'human_resources.created_by',
            'human_resources.updated_by',
            'human_resources.created_at',
            'human_resources.updated_at'
        ]);

        $humanResourceBuilder->join('human_resource_templates', function ($join) use ($rowStatus) {
            $join->on('human_resources.human_resource_template_id', '=', 'human_resource_templates.id')
                ->whereNull('human_resource_templates.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('human_resource_templates.row_status', $rowStatus);
            }
        });
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
        $humanResourceBuilder->leftJoin('human_resources as t2', function ($join) use ($rowStatus) {
            $join->on('human_resources.parent_id', '=', 't2.id')
                ->whereNull('t2.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('t2.row_status', $rowStatus);
            }
        });
        $humanResourceBuilder->orderBy('human_resource_templates.id', $order);

        if (is_numeric($rowStatus)) {
            $humanResourceBuilder->where('human_resources.row_status', $rowStatus);
        }
        if (!empty($titleEn)) {
            $humanResourceBuilder->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $humanResourceBuilder->where('human_resource_templates.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $humanResources */

        if (is_numeric($paginate) || is_numeric( $pageSize)) {
            $pageSize =  $pageSize ?: 10;
            $humanResources = $humanResourceBuilder->paginate( $pageSize);
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
            'human_resources.title_bn',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.parent_id',
            't2.title_en as parent_title_en',
            't2.title_bn as parent_title_bn',
            'human_resources.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title_bn as organization_title_bn',
            'human_resources.organization_unit_id',
            'organization_units.title_en as organization_unit_title_en',
            'organization_units.title_bn as organization_unit_title_bn',
            'human_resources.human_resource_template_id',
            'human_resource_templates.title_en as human_resource_template_title_en',
            'human_resource_templates.title_bn as human_resource_template_title_bn',
            'human_resources.rank_id',
            'ranks.title_en as rank_title_en',
            'ranks.title_bn as rank_title_bn',
            'human_resources.status',
            'human_resources.row_status',
            'human_resources.created_by',
            'human_resources.updated_by',
            'human_resources.created_at',
            'human_resources.updated_at'
        ]);

        $humanResourceBuilder->join('human_resource_templates', function ($join) {
            $join->on('human_resources.human_resource_template_id', '=', 'human_resource_templates.id')
                ->whereNull('human_resource_templates.deleted_at');

        });
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
        $humanResourceBuilder->leftJoin('human_resources as t2', function ($join) {
            $join->on('human_resources.parent_id', '=', 't2.id')
                ->whereNull('t2.deleted_at');
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
        $titleBn = $request->query('title_bn');
        $page_size = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $humanResourceBuilder */
        $humanResourceBuilder = HumanResource::onlyTrashed()->select([
            'human_resources.id',
            'human_resources.title_en',
            'human_resources.title_bn',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.parent_id',
            't2.title_en as parent',
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
        $humanResourceBuilder->leftJoin('human_resources as t2', 'human_resources.parent_id', '=', 't2.id');
        $humanResourceBuilder->orderBy('human_resource_templates.id', $order);

        if (!empty($titleEn)) {
            $humanResourceBuilder->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $humanResourceBuilder->where('human_resource_templates.title_bn', 'like', '%' . $titleBn . '%');
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
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max: 191',
                'min:2'
            ],
            'title_bn' => [
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
            'human_resource_template_id' => [
                'nullable',
                'int',
                'exists:human_resource_templates,id'
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
            'skill_ids' => [
                'nullable',
                'array'
            ],
            'skill_ids.*' => [
                'nullable',
                'int',
                'distinct'
            ],
            'status' => [
                'int',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
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
            'order.in' => 'Order must be within ASC or DESC',
            'row_status.in' => 'Row status must be within 1 or 0'
        ];
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page' => 'numeric|gt:0',
            'page_size' => 'numeric',
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
