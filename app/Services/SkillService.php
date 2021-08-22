<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

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
        $response = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $skillBuilder */
        $skillBuilder = Skill::select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title_bn',
                'skills.description',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at',
                'skills.created_by',
                'skills.updated_by',
            ]
        );

        $skillBuilder->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $skillBuilder->where('skills.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $skillBuilder->where('skills.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $skills */

        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $skills = $skillBuilder->paginate($limit);
            $paginateData = (object)$skills->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $skills = $skillBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $skills->toArray()['data'] ?? $skills->toArray();
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
    public function getOneSkill(int $id, Carbon $startTime): array
    {
        /** @var Builder $skillBuilder */
        $skillBuilder = Skill::select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title_bn',
                'skills.description',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at',
                'skills.created_by',
                'skills.updated_by',
            ]
        );

        $skillBuilder->where('skills.id', '=', $id);

        /** @var Skill $skill */
        $skill = $skillBuilder->first();

        return [
            "data" => $skill ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
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
            'description' => [
                'nullable',
                'string',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }
}
