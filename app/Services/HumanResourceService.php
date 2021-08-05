<?php

namespace App\Services;

use App\Models\HumanResource;
use App\Models\HumanResourceTemplate;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class HumanResourceService
{
    public function getHumanResourceList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var HumanResource|Builder $humanResources */
        $humanResources = HumanResource::select([
            'human_resources.id',
            'human_resources.title_en',
            'human_resources.title_bn',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.skill_ids as skills',
            'human_resources.created_at',
            'human_resources.updated_at',
            'human_resources.title_en as parent',
            'organization_units.title_en as organization_unit_name',
            'organizations.title_en as organization_name',
            'human_resource_templates.title_en as human_resource_template_name',
            'ranks.id as rank_title',
        ]);

        $humanResources->join('human_resource_templates', 'human_resources.human_resource_template_id', '=', 'human_resource_templates.id');
        $humanResources->join('organizations', 'human_resources.organization_id', '=', 'organizations.id');
        $humanResources->join('organization_units', 'human_resources.organization_unit_id', '=', 'organization_units.id');
        $humanResources->leftJoin('ranks', 'human_resources.rank_id', '=', 'ranks.id');
        $humanResources->leftJoin('human_resources as t2', 'human_resources.parent_id', '=', 't2.id');
        $humanResources->orderBy('human_resource_templates.id', $order);

        if (!empty($titleEn)) {
            $humanResources->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $humanResources->where('human_resource_templates.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $humanResources = $humanResources->paginate(10);
            $paginateData = (object)$humanResources->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $humanResources = $humanResources->get();
        }

        $data = [];
        foreach ($humanResources as $humanResource) {
            $links['read'] = route('api.v1.human-resources.read', ['id' => $humanResource->id]);
            $links['update'] = route('api.v1.human-resources.update', ['id' => $humanResource->id]);
            $links['delete'] = route('api.v1.human-resources.destroy', ['id' => $humanResource->id]);
            $humanResource['_links'] = $links;
            $data[] = $humanResource->toArray();
        }

        return [
            "data" => $data,
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
                    '_link' => route('api.v1.human-resources.get-list')
                ],
            ],
            "_page" => $page,
            "_order" => $order
        ];
    }

    public function getOneHumanResource(int $id, Carbon $startTime): array
    {
        /** @var HumanResource|Builder $humanResource */
        $humanResource = HumanResource::select([
            'human_resources.id',
            'human_resources.title_en',
            'human_resources.title_bn',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.skill_ids',
            'human_resources.parent_id',
            'human_resources.title_en as parent',
            'organization_units.title_en as organization_unit_name',
            'human_resources.organization_id',
            'human_resources.organization_unit_id',
            'organizations.title_en as organization_name',
            'human_resources.human_resource_template_id',
            'human_resource_templates.title_en as human_resource_template_name',
            'human_resources.rank_id',
            'human_resources.row_status',
            'ranks.title_en as rank_title_en',
            'human_resources.created_by',
            'human_resources.updated_by',
            'human_resources.created_at',
            'human_resources.updated_at',
        ]);

        $humanResource->join('human_resource_templates', 'human_resources.human_resource_template_id', '=', 'human_resource_templates.id');
        $humanResource->join('organizations', 'human_resources.organization_id', '=', 'organizations.id');
        $humanResource->join('organization_units', 'human_resources.organization_unit_id', '=', 'organization_units.id');
        $humanResource->leftJoin('ranks', 'human_resources.rank_id', '=', 'ranks.id');
        $humanResource->leftJoin('human_resources as t2', 'human_resources.parent_id', '=', 't2.id');
        $humanResource->where('human_resources.id', $id);
        $humanResource = $humanResource->first();

        $links = [];
        if (!empty($humanResource)) {
            $links['update'] = route('api.v1.human-resources.update', ['id' => $id]);
            $links['delete'] = route('api.v1.human-resources.destroy', ['id' => $id]);
        }

        return [
            "data" => $humanResource ?: null,
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
     * @return HumanResource
     */
    public function destroy(HumanResource $humanResource): HumanResource
    {
        $humanResource->row_status = HumanResourceTemplate::ROW_STATUS_DELETED;
        $humanResource->save();
        $humanResource->delete();
        return $humanResource;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request,int $id = null): Validator
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
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([HumanResource::ROW_STATUS_ACTIVE, HumanResource::ROW_STATUS_INACTIVE, HumanResource::ROW_STATUS_DELETED]),
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
