<?php

namespace App\Services;

use App\Models\HumanResource;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        /** @var Builder $humanResourceBuilder */
        $humanResourceBuilder = HumanResource::select([
            'human_resources.id',
            'human_resources.title_en',
            'human_resources.title_bn',
            'human_resources.display_order',
            'human_resources.is_designation',
            'human_resources.parent_id',
            't2.title_en as parent',
            'organization_units.title_en as organization_unit_name',
            'human_resources.organization_id',
            'human_resources.organization_unit_id',
            'organizations.title_en as organization_name',
            'human_resources.human_resource_template_id',
            'human_resource_templates.title_en as human_resource_template_name',
            'human_resources.rank_id',
            'human_resources.status',
            'human_resources.row_status',
            'ranks.title_en as rank_title_en',
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

        if ($paginate) {
            $humanResources = $humanResourceBuilder->paginate(10);
            $paginateData = (object)$humanResources->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $humanResources = $humanResourceBuilder->get();
        }

        $data = $humanResources->toArray();

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
            't2.title_en as parent',
            'organization_units.title_en as organization_unit_name',
            'human_resources.organization_id',
            'human_resources.organization_unit_id',
            'organizations.title_en as organization_name',
            'human_resources.human_resource_template_id',
            'human_resource_templates.title_en as human_resource_template_name',
            'human_resources.rank_id',
            'human_resources.status',
            'human_resources.row_status',
            'ranks.title_en as rank_title_en',
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
        $humanResourceBuilder->where('human_resources.id', $id);


        /** @var HumanResource $humanResource */
        $humanResource = $humanResourceBuilder->first();

        $links = [];
        if ($humanResource) {
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
     * @return bool
     */
    public function destroy(HumanResource $humanResource): bool
    {
        return $humanResource->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
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
                Rule::in([HumanResource::ROW_STATUS_ACTIVE, HumanResource::ROW_STATUS_INACTIVE]),
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
