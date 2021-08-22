<?php

namespace App\Services;

use App\Models\BaseModel;
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
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getHumanResourceTemplateList(Request $request, Carbon $startTime): array
    {
        $response = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $humanResourceTemplateBuilder */
        $humanResourceTemplateBuilder = HumanResourceTemplate::select([
            'human_resource_templates.id',
            'human_resource_templates.title_en',
            'human_resource_templates.title_bn',
            'human_resource_templates.display_order',
            'human_resource_templates.is_designation',
            'human_resource_templates.parent_id',
            't2.title_en as parent',
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
        $humanResourceTemplateBuilder->leftJoin('human_resource_templates as t2', 'human_resource_templates.parent_id', '=', 't2.id');
        $humanResourceTemplateBuilder->orderBy('human_resource_templates.id', $order);

        if (!empty($titleEn)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $humanResourceTemplateBuilder->where('human_resource_templates.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $humanResourceTemplates */

        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $humanResourceTemplates = $humanResourceTemplateBuilder->paginate($limit);
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
        $response['response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "started" => $startTime->format('H i s'),
            "finished" => Carbon::now()->format('H i s'),
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
            'human_resource_templates.title_bn',
            'human_resource_templates.display_order',
            'human_resource_templates.is_designation',
            'human_resource_templates.parent_id',
            't2.title_en as parent',
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
        $humanResourceTemplateBuilder->leftJoin('human_resource_templates as t2', 'human_resource_templates.parent_id', '=', 't2.id');
        $humanResourceTemplateBuilder->where('human_resource_templates.id', $id);

        /** @var HumanResourceTemplate $humanResourceTemplate */
        $humanResourceTemplate = $humanResourceTemplateBuilder->first();

        return [
            "data" => $humanResourceTemplate ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
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
                'min: 2'
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 500',
                'min: 2'
            ],
            'organization_id' => [
                'required',
                'int',
                'exists:organizations,id'
            ],
            'organization_unit_type_id' => [
                'required',
                'int',
                'exists:organization_unit_types,id'
            ],
            'parent_id' => [
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
            'status' => [
                'int',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'int',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}

