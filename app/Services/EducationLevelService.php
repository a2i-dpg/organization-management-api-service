<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\EducationLevel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class EducationLevelService
{

    public function getEducationLevelList(array $request, Carbon $startTime): array
    {

        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rowStatus = $request['row_status'] ?? "";


        /** @var Builder|EducationLevel $educationLevelBuilder */
        $educationLevelBuilder = EducationLevel::select([
            'education_levels.id',
            'education_levels.code',
            'education_levels.title_en',
            'education_levels.title',
            'education_levels.row_status',
            'education_levels.created_at',
            'education_levels.updated_at',
        ]);

        $educationLevelBuilder->orderBy('education_levels.id', $order);


        if (!empty($titleEn)) {
            $educationLevelBuilder->where('education_levels.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $educationLevelBuilder->where('education_levels.title', 'like', '%' . $title . '%');
        }
        if (is_numeric($rowStatus)) {
            $educationLevelBuilder->where('education_levels.row_status', $rowStatus);
        }

        /** @var Collection $educationLevels */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $educationLevels = $educationLevelBuilder->paginate($pageSize);
            $paginateData = (object)$educationLevelBuilder->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $educationLevels = $educationLevelBuilder->get();
        }


        $response['order'] = $order;
        $response['data'] = $educationLevels->toArray()['data'] ?? $educationLevels->toArray();
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
