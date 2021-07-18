<?php


namespace App\Services;


use App\Helpers\Classes\AuthHelper;
use App\Helpers\Classes\DatatableHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Module\GovtStakeholder\App\Models\HumanResource;
use Yajra\DataTables\Facades\DataTables;

class HumanResourceService
{
    public function createHumanResource(array $data): HumanResource
    {
        return HumanResource::create($data);
    }

    public function updateHumanResource(HumanResource $humanResource, array $data): bool
    {
        return $humanResource->update($data);
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

    public function getListDataForDatatable(\Illuminate\Http\Request $request): JsonResponse
    {
        $authUser = AuthHelper::getAuthUser();
        /** @var Builder|HumanResource $humanResource */

        $humanResource = HumanResource::select([
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

        $humanResource->join('human_resource_templates', 'human_resources.human_resource_template_id', '=', 'human_resource_templates.id');
        $humanResource->join('organizations', 'human_resources.organization_id', '=', 'organizations.id');
        $humanResource->join('organization_units', 'human_resources.organization_unit_id', '=', 'organization_units.id');
        $humanResource->leftJoin('ranks', 'human_resources.rank_id', '=', 'ranks.id');
        $humanResource->leftJoin('human_resources as t2', 'human_resources.parent_id', '=', 't2.id');

        return DataTables::eloquent($humanResource)
            ->addColumn('action', DatatableHelper::getActionButtonBlock(static function (HumanResource $humanResource) use ($authUser) {
                $str = '';
                if ($authUser->can('view', $humanResource)) {
                    $str .= '<a href="' . route('govt_stakeholder::admin.human-resources.show', $humanResource->id) . '" class="btn btn-outline-info btn-sm"> <i class="fas fa-eye"></i> ' . __('generic.read_button_label') . ' </a>';
                }

                if ($authUser->can('update', $humanResource)) {
                    $str .= '<a href="' . route('govt_stakeholder::admin.human-resources.edit', $humanResource->id) . '" class="btn btn-outline-warning btn-sm"> <i class="fas fa-edit"></i> ' . __('generic.edit_button_label') . ' </a>';
                }
                if ($authUser->can('delete', $humanResource)) {
                    $str .= '<a href="#" data-action="' . route('govt_stakeholder::admin.human-resources.destroy', $humanResource->id) . '" class="btn btn-outline-danger btn-sm delete"> <i class="fas fa-trash"></i> ' . __('generic.delete_button_label') . '</a>';
                }

                return $str;
            }))
            ->editColumn('is_designation', function (HumanResource $humanResource) {
                if ($humanResource->is_designation) {
                    return 'Yes';
                } else {
                    return 'No';
                }
            })
            ->rawColumns(['action'])
            ->toJson();
    }


}
