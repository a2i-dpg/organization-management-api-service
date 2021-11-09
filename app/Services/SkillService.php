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
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getSkillList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $skillBuilder */
        $skillBuilder = Skill::select(
            [
                'skills.id',
                'skills.title_en',
                'skills.title',
            ]
        );
        $skillBuilder->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $skillBuilder->where('skills.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $skillBuilder->where('skills.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $skills */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $skills = $skillBuilder->paginate($pageSize);
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
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
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
                'skills.id',
                'skills.title_en',
                'skills.title'
            ]
        );

        $skillBuilder->where('skills.id', '=', $id);

        /** @var Skill $skill */
        $skill = $skillBuilder->first();

        return [
            "data" => $skill ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now())
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
     * @param Carbon $startTime
     * @return array
     */
    public function getTrashedSkillList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $title = $request->query('title');
        $pageSize = $request->query('page_size', BaseModel::DEFAULT_PAGE_SIZE);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Builder $skillBuilder */
        $skillBuilder = Skill::onlyTrashed()->select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title',
            ]
        );

        $skillBuilder->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $skillBuilder->where('skills.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $skillBuilder->where('skills.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $skills */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $skills = $skillBuilder->paginate($pageSize);
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
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param Skill $skill
     * @return bool
     */
    public function restore(Skill $skill): bool
    {
        return $skill->restore();
    }

    /**
     * @param Skill $skill
     * @return bool
     */
    public function forceDelete(Skill $skill): bool
    {
        return $skill->forceDelete();
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2',
            ],
            'title' => [
                'required',
                'string',
                'max: 600',
                'min:2'
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:300|min:2',
            'title' => 'nullable|min:600|min:2',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                'nullable',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]
        ], $customMessage);
    }
}
