<?php


namespace App\Services;


use App\Models\HumanResource;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HumanResourceService
{
    public function getHumanResourceList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

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
            $paginate_data = (object)$humanResources->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $humanResources = $humanResources->get();
        }

        $data = [];
        foreach ($humanResources as $humanResource) {
            $_links['read'] = route('api.v1.human-resources.read', ['id' => $humanResource->id]);
            $_links['update'] = route('api.v1.human-resources.update', ['id' => $humanResource->id]);
            $_links['delete'] = route('api.v1.human-resources.destroy', ['id' => $humanResource->id]);
            $humanResource['_links'] = $_links;
            $data[] = $humanResource->toArray();
        }

        return [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => [
                'paginate' => $paginate_link,

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

    public function getOneHumanResource($id): array
    {
        $startTime = Carbon::now();
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
        $humanResources->where('human_resources.id', $id);
        $humanResources->first();

        $links = [];
        if (!empty($humanResources)) {
            $links['update'] = route('api.v1.human-resources.update', ['id' => $id]);
            $links['delete'] = route('api.v1.human-resources.destroy', ['id' => $id]);
        }
        return [
            "data" => $humanResources ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
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

    public function validator(Request $request): Validator
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
            'skill_id' => [
                'nullable',
                'array'
            ],
            'skill_id.*' => [
                'nullable',
                'int',
                'distinct'
            ],
            'status' => [
                'nullable',
                'int',
                Rule::in([HumanResource::ROW_STATUS_ACTIVE, HumanResource::ROW_STATUS_INACTIVE, HumanResource::ROW_STATUS_DELETED]),
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

    }

}
