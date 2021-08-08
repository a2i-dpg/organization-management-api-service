<?php

namespace App\Services;

use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class SkillService
 * @package App\Services
 */
class SkillService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getSkillList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Skill|Builder $skills */
        $skills = Skill::select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title_bn',
                'skills.organization_id',
                'organizations.title_en as organization_title_en',
                'skills.description',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at',
                'skills.created_by',
                'skills.updated_by',
            ]
        );
        $skills->LeftJoin('organizations', 'skills.organization_id', '=', 'organizations.id');
        $skills->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $skills->where('skills.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $skills->where('skills.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $skills = $skills->paginate(10);
            $paginateData = (object)$skills->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $skills = $skills->get();
        }

        $data = [];
        foreach ($skills as $skill) {
            $links['read'] = route('api.v1.skills.read', ['id' => $skill->id]);
            $links['update'] = route('api.v1.skills.update', ['id' => $skill->id]);
            $links['delete'] = route('api.v1.skills.destroy', ['id' => $skill->id]);
            $skill['_links'] = $links;
            $data[] = $skill->toArray();
        }

        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => [
                'paginate' => $paginateLink,
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.skills.get-list')
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
    public function getOneSkill(int $id, Carbon $startTime): array
    {
        /** @var Skill|Builder $skill */
        $skill = Skill::select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title_bn',
                'skills.organization_id',
                'organizations.title_en as organization_title_en',
                'skills.description',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at',
                'skills.created_by',
                'skills.updated_by',
            ]
        );
        $skill->LeftJoin('organizations', 'skills.organization_id', '=', 'organizations.id');
        $skill->where('skills.id', '=', $id);
        $skill = $skill->first();

        $links = [];
        if (!empty($skill)) {
            $links['update'] = route('api.v1.skills.update', ['id' => $id]);
            $links['delete'] = route('api.v1.skills.destroy', ['id' => $id]);
        }
        return [
            "data" => $skill ?: null,
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
     * @return Skill
     */
    public function store(array $data): Skill
    {
        $skill = new Skill();
        $skill->fill($data);
        $skill->save();
        return $skill;
    }

    /**
     * @param Skill $skill
     * @param array $data
     * @return Skill
     */
    public function update(Skill $skill, array $data): Skill
    {
        $skill->fill($data);
        $skill->save();
        return $skill;
    }

    /**
     * @param Skill $skill
     * @return bool
     */
    public function destroy(Skill $skill): bool
    {
        return $skill->delete();
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:191',
                'min:2',
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 600',
                'min:2'
            ],
            'organization_id' => [
                'nullable',
                'int',
                'exists:organizations,id',
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([Skill::ROW_STATUS_ACTIVE, Skill::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}
