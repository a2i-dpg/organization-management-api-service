<?php

namespace App\Services;

use App\Models\HumanResourceTemplate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;

/**
 * Class HumanResourceTemplateService
 * @package App\Services
 */
class HumanResourceTemplateService
{
    public function getHumanResourceTemplateList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var HumanResourceTemplate|Builder $humanResourceTemplates */
        $humanResourceTemplates = HumanResourceTemplate::select([
            'human_resource_templates.id',
            'human_resource_templates.title_en',
            'human_resource_templates.title_bn',
            'human_resource_templates.display_order',
            'human_resource_templates.is_designation',
            'human_resource_templates.skill_ids as skills',
            'human_resource_templates.created_at',
            'human_resource_templates.updated_at',
            'human_resource_templates.title_en as parent',
            'organizations.title_en as organization_title',
            'organization_unit_types.title_en as organization_unit_type_title',
            'ranks.id as rank_title',
        ]);

        $humanResourceTemplates->join('organizations', 'human_resource_templates.organization_id', '=', 'organizations.id');
        $humanResourceTemplates->join('organization_unit_types', 'human_resource_templates.organization_unit_type_id', '=', 'organization_unit_types.id');
        $humanResourceTemplates->leftJoin('ranks', 'human_resource_templates.rank_id', '=', 'ranks.id');
        $humanResourceTemplates->leftJoin('human_resource_templates as t2', 'human_resource_templates.parent_id', '=', 't2.id');
        $humanResourceTemplates->orderBy('human_resource_templates.id', $order);

        if (!empty($titleEn)) {
            $humanResourceTemplates->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $humanResourceTemplates->where('human_resource_templates.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $humanResourceTemplates = $humanResourceTemplates->paginate(10);
            $paginateData = (object)$humanResourceTemplates->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $humanResourceTemplates = $humanResourceTemplates->get();
        }

        $data = [];
        foreach ($humanResourceTemplates as $humanResourceTemplate) {
            $links['read'] = route('api.v1.human-resource-templates.read', ['id' => $humanResourceTemplate->id]);
            $links['update'] = route('api.v1.human-resource-templates.update', ['id' => $humanResourceTemplate->id]);
            $links['delete'] = route('api.v1.human-resource-templates.destroy', ['id' => $humanResourceTemplate->id]);
            $humanResourceTemplate['_links'] = $links;
            $data[] = $humanResourceTemplate->toArray();
        }

        return [
            "data" => $data? : null,
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
                    '_link' => route('api.v1.human-resource-templates.get-list')
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
    public function getOneHumanResourceTemplate(int $id, Carbon $startTime): array
    {
        /** @var HumanResourceTemplate|Builder $humanResourceTemplate */
        $humanResourceTemplate = HumanResourceTemplate::select([
            'human_resource_templates.id',
            'human_resource_templates.title_en',
            'human_resource_templates.title_bn',
            'human_resource_templates.display_order',
            'human_resource_templates.is_designation',
            'human_resource_templates.skill_ids as skills',
            'human_resource_templates.created_at',
            'human_resource_templates.updated_at',
            'human_resource_templates.title_en as parent',
            'organizations.title_en as organization_title',
            'organization_unit_types.title_en as organization_unit_type_title',
            'ranks.id as rank_title',
        ]);

        $humanResourceTemplate->join('organizations', 'human_resource_templates.organization_id', '=', 'organizations.id');
        $humanResourceTemplate->join('organization_unit_types', 'human_resource_templates.organization_unit_type_id', '=', 'organization_unit_types.id');
        $humanResourceTemplate->leftJoin('ranks', 'human_resource_templates.rank_id', '=', 'ranks.id');
        $humanResourceTemplate->leftJoin('human_resource_templates as t2', 'human_resource_templates.parent_id', '=', 't2.id');
        $humanResourceTemplate->where('human_resource_templates.id', $id);
        $humanResourceTemplate = $humanResourceTemplate->first();

        $links = [];
        if (!empty($humanResourceTemplate)) {
            $links['update'] = route('api.v1.human-resource-templates.update', ['id' => $id]);
            $links['delete'] = route('api.v1.human-resource-templates.destroy', ['id' => $id]);
        }

        return [
            "data" => $humanResourceTemplate ?: null,
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
     * @return HumanResourceTemplate
     */
    public function destroy(HumanResourceTemplate  $humanResourceTemplate): HumanResourceTemplate
    {
        $humanResourceTemplate->row_status = HumanResourceTemplate::ROW_STATUS_DELETED;
        $humanResourceTemplate->save();
        return $humanResourceTemplate;
    }

    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max: 191'
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 191'
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
            'skill_id' => [
                'nullable',
                'array'
            ],
            'skill_id.*' => [
                'nullable',
                'int',
                'distinct'
            ]
        ];
        return Validator::make($request->all(), $rules);
    }
}

