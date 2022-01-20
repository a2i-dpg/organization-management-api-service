<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\ExamDegree;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ExamDegreeService
{

    public function getExamDegreeList(array $request, Carbon $startTime): array
    {

        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $educationLevelId = $request['education_level_id'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rowStatus = $request['row_status'] ?? "";


        /** @var Builder|ExamDegree $examDegreeBuilder */
        $examDegreeBuilder = ExamDegree::select([
            'exam_degrees.id',
            'exam_degrees.title_en',
            'exam_degrees.title',
            'exam_degrees.education_level_id',
            'education_levels.title as education_level_title',
            'education_levels.title_en as education_level_title',
            'exam_degrees.row_status',
            'exam_degrees.row_status',
            'exam_degrees.created_at',
            'exam_degrees.updated_at',
        ]);

        $examDegreeBuilder->join('education_levels', function ($join) {
            $join->on('exam_degrees.education_level_id', '=', 'education_levels.id')
                ->whereNull('education_levels.deleted_at');
        });


        $examDegreeBuilder->orderBy('exam_degrees.id', $order);

        if (!empty($titleEn)) {
            $examDegreeBuilder->where('exam_degrees.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $examDegreeBuilder->where('exam_degrees.title', 'like', '%' . $title . '%');
        }
        if (is_numeric($rowStatus)) {
            $examDegreeBuilder->where('exam_degrees.row_status', $rowStatus);
        }
        if (is_numeric($educationLevelId)) {
            $examDegreeBuilder->where('exam_degrees.education_level_id', $educationLevelId);
        }
        /** @var Collection $examDegrees */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $examDegrees = $examDegreeBuilder->paginate($pageSize);
            $paginateData = (object)$examDegrees->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $examDegrees = $examDegreeBuilder->get();
        }

        $response['order'] = $order;

        $response['data'] = $examDegrees->toArray()['data'] ?? $examDegrees->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
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
            'title' => 'nullable|max:600|min:2',
            'education_level_id' => 'nullable|integer',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                'nullable',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "integer",
                Rule::in(BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE),
            ],
        ], $customMessage);
    }

}
