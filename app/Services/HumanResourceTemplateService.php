<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\HumanResource;
use App\Models\HumanResourceTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HumanResourceTemplateService
 * @package App\Services
 */
class HumanResourceTemplateService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getHumanResourceTemplateList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $organizationId = $request['organization_id'] ?? "";
        $organizationUnitTypeId = $request['organization_unit_type_id'] ?? "";


        /** @var Builder $humanResourceTemplateBuilder */
        $humanResourceTemplateBuilder = HumanResourceTemplate::select([
            'human_resource_templates.id',
            'human_resource_templates.title_en',
            'human_resource_templates.title',
            'human_resource_templates.display_order',
            'human_resource_templates.is_designation',
            'human_resource_templates.parent_id',
            'human_res_tem_2.title_en as parent_title_en',
            'human_res_tem_2.title as parent_title',
            'human_resource_templates.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title',
            'human_resource_templates.organization_unit_type_id',
            'organization_unit_types.title_en as organization_unit_type_title_en',
            'organization_unit_types.title as organization_unit_type_title',
            'human_resource_templates.rank_id',
            'ranks.title_en as rank_title_en',
            'ranks.title as rank_title',
            'human_resource_templates.status',
            'human_resource_templates.row_status',
            'human_resource_templates.created_by',
            'human_resource_templates.updated_by',
            'human_resource_templates.created_at',
            'human_resource_templates.updated_at',

        ])->byOrganization('human_resource_templates');

        $humanResourceTemplateBuilder->join('organizations', function ($join) use ($rowStatus) {
            $join->on('human_resource_templates.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('organizations.row_status', $rowStatus);
            }*/
        });
        $humanResourceTemplateBuilder->join('organization_unit_types', function ($join) use ($rowStatus) {
            $join->on('human_resource_templates.organization_unit_type_id', '=', 'organization_unit_types.id')
                ->whereNull('organization_unit_types.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('organization_unit_types.row_status', $rowStatus);
            }*/
        });
        $humanResourceTemplateBuilder->leftJoin('ranks', function ($join) use ($rowStatus) {
            $join->on('human_resource_templates.rank_id', '=', 'ranks.id')
                ->whereNull('ranks.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('ranks.row_status', $rowStatus);
            }*/
        });
        $humanResourceTemplateBuilder->leftJoin('human_resource_templates as human_res_tem_2', function ($join) use ($rowStatus) {
            $join->on('human_resource_templates.parent_id', '=', 'human_res_tem_2.id')
                ->whereNull('human_res_tem_2.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('human_res_tem_2.row_status', $rowStatus);
            }
        });

        $humanResourceTemplateBuilder->orderBy('human_resource_templates.id', $order);

        if (is_numeric($rowStatus)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.row_status', $rowStatus);
        }

        if (is_numeric($organizationId)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.organization_id', $organizationId);
        }

        if (is_numeric($organizationUnitTypeId)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.organization_unit_type_id', $organizationUnitTypeId);
        }

        if (!empty($titleEn)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $humanResourceTemplates */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $humanResourceTemplates = $humanResourceTemplateBuilder->paginate($pageSize);
            $paginateData = (object)$humanResourceTemplates->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $humanResourceTemplates = $humanResourceTemplateBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $humanResourceTemplates->toArray()['data'] ?? $humanResourceTemplates->toArray();
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
    public function getOneHumanResourceTemplate(int $id, Carbon $startTime): array
    {
        /** @var Builder $humanResourceTemplateBuilder */
        $humanResourceTemplateBuilder = HumanResourceTemplate::select([
            'human_resource_templates.id',
            'human_resource_templates.title_en',
            'human_resource_templates.title',
            'human_resource_templates.display_order',
            'human_resource_templates.is_designation',
            'human_resource_templates.parent_id',
            'human_res_tem_2.title_en as parent_title_en',
            'human_res_tem_2.title as parent_title',
            'human_resource_templates.organization_id',
            'organizations.title_en as organization_title_en',
            'organizations.title as organization_title',
            'human_resource_templates.organization_unit_type_id',
            'organization_unit_types.title_en as organization_unit_type_title_en',
            'organization_unit_types.title as organization_unit_type_title',
            'human_resource_templates.rank_id',
            'ranks.title_en as rank_title_en',
            'ranks.title as rank_title',
            'human_resource_templates.status',
            'human_resource_templates.row_status',
            'human_resource_templates.created_by',
            'human_resource_templates.updated_by',
            'human_resource_templates.created_at',
            'human_resource_templates.updated_at',
        ]);
        $humanResourceTemplateBuilder->join('organizations', function ($join) {
            $join->on('human_resource_templates.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at');

        });
        $humanResourceTemplateBuilder->join('organization_unit_types', function ($join) {
            $join->on('human_resource_templates.organization_unit_type_id', '=', 'organization_unit_types.id')
                ->whereNull('organization_unit_types.deleted_at');

        });
        $humanResourceTemplateBuilder->leftJoin('ranks', function ($join) {
            $join->on('human_resource_templates.rank_id', '=', 'ranks.id')
                ->whereNull('ranks.deleted_at');
        });
        $humanResourceTemplateBuilder->leftJoin('human_resource_templates as human_res_tem_2', function ($join) {
            $join->on('human_resource_templates.parent_id', '=', 'human_res_tem_2.id')
                ->whereNull('human_res_tem_2.deleted_at');
        });

        $humanResourceTemplateBuilder->where('human_resource_templates.id', $id);

        /** @var HumanResourceTemplate $humanResourceTemplate */
        $humanResourceTemplate = $humanResourceTemplateBuilder->first();

        return [
            "data" => $humanResourceTemplate ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    /**
     * create a human resource template data
     * @param array $data
     * @return HumanResourceTemplate
     */
    public function store(array $data): HumanResourceTemplate
    {
        $humanResourceTemplate = new HumanResourceTemplate();
        $humanResourceTemplate->fill($data);
        $humanResourceTemplate->save();
        return $humanResourceTemplate;
    }

    /**
     * @param HumanResourceTemplate $humanResourceTemplate
     * @param array $data
     * @return HumanResourceTemplate
     */
    public function update(HumanResourceTemplate $humanResourceTemplate, array $data): HumanResourceTemplate
    {
        $humanResourceTemplate->fill($data);
        $humanResourceTemplate->save();
        return $humanResourceTemplate;
    }

    /**
     * @param HumanResourceTemplate $humanResourceTemplate
     * @return bool
     */
    public function destroy(HumanResourceTemplate $humanResourceTemplate): bool
    {
        return $humanResourceTemplate->delete();
    }

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedHumanResourceTemplateList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $title = $request->query('title');
        $pageSize = $request->query('page_size', BaseModel::DEFAULT_PAGE_SIZE);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $humanResourceTemplateBuilder */
        $humanResourceTemplateBuilder = HumanResourceTemplate::onlyTrashed()->select([
            'human_resource_templates.id',
            'human_resource_templates.title_en',
            'human_resource_templates.title',
            'human_resource_templates.display_order',
            'human_resource_templates.is_designation',
            'human_resource_templates.parent_id',
            'human_res_tem_2.title_en as parent_title_en',
            'human_res_tem_2.title as parent_title',
            'human_resource_templates.organization_id',
            'organizations.title_en as organization_title',
            'human_resource_templates.organization_unit_type_id',
            'organization_unit_types.title_en as organization_unit_type_title',
            'human_resource_templates.rank_id',
            'ranks.title_en as rank_title_en',
            'human_resource_templates.status',
            'human_resource_templates.row_status',
            'human_resource_templates.created_by',
            'human_resource_templates.updated_by',
            'human_resource_templates.created_at',
            'human_resource_templates.updated_at',
        ]);

        $humanResourceTemplateBuilder->join('organizations', 'human_resource_templates.organization_id', '=', 'organizations.id');
        $humanResourceTemplateBuilder->join('organization_unit_types', 'human_resource_templates.organization_unit_type_id', '=', 'organization_unit_types.id');
        $humanResourceTemplateBuilder->leftJoin('ranks', 'human_resource_templates.rank_id', '=', 'ranks.id');
        $humanResourceTemplateBuilder->leftJoin('human_resource_templates as human_res_tem_2', 'human_resource_templates.parent_id', '=', 'human_res_tem_2.id');
        $humanResourceTemplateBuilder->orderBy('human_resource_templates.id', $order);

        if (!empty($titleEn)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $humanResourceTemplates */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $humanResourceTemplates = $humanResourceTemplateBuilder->paginate($pageSize);
            $paginateData = (object)$humanResourceTemplates->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $humanResourceTemplates = $humanResourceTemplateBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $humanResourceTemplates->toArray()['data'] ?? $humanResourceTemplates->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param HumanResourceTemplate $humanResourceTemplate
     * @return bool
     */
    public function restore(HumanResourceTemplate $humanResourceTemplate): bool
    {
        return $humanResourceTemplate->restore();
    }

    /**
     * @param HumanResourceTemplate $humanResourceTemplate
     * @return bool
     */
    public function forceDelete(HumanResourceTemplate $humanResourceTemplate): bool
    {
        return $humanResourceTemplate->forceDelete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max: 400',
                'min: 2'
            ],
            'title' => [
                'required',
                'string',
                'max: 800',
                'min: 2'
            ],
            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id,deleted_at,NULL',
            ],
            'organization_unit_type_id' => [
                'required',
                'integer',
                'exists:organization_unit_types,id,deleted_at,NULL',
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:human_resource_templates,id,deleted_at,NULL',
                function($attr,$value,$failed) use ($data){
                    if(!empty($data['organization_unit_type_id'] && empty($data['parent_id']))){
                        $humanResourceTemplateWithParentIdNull = HumanResourceTemplate::where('organization_unit_type_id',$data['organization_unit_type_id'])->where('parent_id', null)->first();
                        if($humanResourceTemplateWithParentIdNull){
                            $failed('Parent item already added for this organization unit type');
                        }
                    }
                }
            ],
            'rank_id' => [
                'nullable',
                'integer',
                'exists:ranks,id,deleted_at,NULL',
            ],
            'display_order' => [
                'required',
                'integer',
                'min:0'
            ],
            'is_designation' => [
                'required',
                'integer'
            ],
            'status' => [
                'integer',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                'integer',
                Rule::in([HumanResourceTemplate::ROW_STATUS_ACTIVE, HumanResourceTemplate::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($data, $rules, $customMessage);
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
            'title_en' => 'nullable|max:400|min:2',
            'title' => 'nullable|max:800|min:2',
            'page' => 'integer|gt:0',
            'page_size' => 'integer|gt:0',
            'organization_id' => 'nullable|integer|gt:0',
            'organization_unit_type_id' => 'nullable|integer|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([HumanResourceTemplate::ROW_STATUS_ACTIVE, HumanResourceTemplate::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

