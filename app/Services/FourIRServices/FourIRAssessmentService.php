<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRAssessment;
use App\Models\FourIRCourseDevelopment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Collection\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FourIRAssessmentService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrAssessmentList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_project_id'] ?? "";
        $courseName = $request['course_name'] ?? "";
        $examineName = $request['examine_name'] ?? "";
        $examinerName = $request['examiner_name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrAssessmentBuilder */
        $fourIrAssessmentBuilder = FourIRAssessment::select([
            'four_ir_assessments.id',
            'four_ir_assessments.four_ir_project_id',
            'four_ir_assessments.course_name',
            'four_ir_assessments.course_name_en',
            'four_ir_assessments.examine_name',
            'four_ir_assessments.examine_name_en',
            'four_ir_assessments.examiner_name',
            'four_ir_assessments.examiner_name_en',
            'four_ir_assessments.file_path',
            'four_ir_assessments.accessor_type',
            'four_ir_assessments.accessor_id',
            'four_ir_assessments.row_status',
            'four_ir_assessments.created_by',
            'four_ir_assessments.updated_by',
            'four_ir_assessments.created_at',
            'four_ir_assessments.updated_at'
        ])->acl();
        $fourIrAssessmentBuilder->orderBy('four_ir_assessments.id', $order);

        if (is_numeric($fourIrProjectId)) {
            $fourIrAssessmentBuilder->where('four_ir_assessments.four_ir_project_id', $fourIrProjectId);
        }

        if (!empty($courseName)) {
            $fourIrAssessmentBuilder->where(function ($builder) use ($courseName) {
                $builder->where('four_ir_assessments.course_name', 'like', '%' . $courseName . '%');
                $builder->orWhere('four_ir_assessments.course_name_en', 'like', '%' . $courseName . '%');
            });
        }
        if (!empty($examineName)) {
            $fourIrAssessmentBuilder->where(function ($builder) use ($examineName) {
                $builder->where('four_ir_assessments.examine_name', 'like', '%' . $examineName . '%');
                $builder->orWhere('four_ir_assessments.examine_name_en', 'like', '%' . $examineName . '%');
            });
        }
        if (!empty($examinerName)) {
            $fourIrAssessmentBuilder->where(function ($builder) use ($examinerName) {
                $builder->where('four_ir_assessments.examiner_name', 'like', '%' . $examinerName . '%');
                $builder->orWhere('four_ir_assessments.examiner_name_en', 'like', '%' . $examinerName . '%');
            });
        }

        if (is_numeric($rowStatus)) {
            $fourIrAssessmentBuilder->where('four_ir_assessments.row_status', $rowStatus);
        }

        /** @var  Collection $fourIrAssessments */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrAssessments = $fourIrAssessmentBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrAssessments->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrAssessments = $fourIrAssessmentBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrAssessments->toArray()['data'] ?? $fourIrAssessments->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRAssessment
     */
    public function getOneFourIrAssessment(int $id): FourIRAssessment
    {
        /** @var FourIRAssessment|Builder $fourIrAssessmentBuilder */
        $fourIrAssessmentBuilder = FourIRAssessment::select([
            'four_ir_assessments.id',
            'four_ir_assessments.four_ir_project_id',
            'four_ir_assessments.course_name',
            'four_ir_assessments.course_name_en',
            'four_ir_assessments.examine_name',
            'four_ir_assessments.examine_name_en',
            'four_ir_assessments.examiner_name',
            'four_ir_assessments.examiner_name_en',
            'four_ir_assessments.file_path',
            'four_ir_assessments.accessor_type',
            'four_ir_assessments.accessor_id',
            'four_ir_assessments.row_status',
            'four_ir_assessments.created_by',
            'four_ir_assessments.updated_by',
            'four_ir_assessments.created_at',
            'four_ir_assessments.updated_at'
        ]);
        $fourIrAssessmentBuilder->where('four_ir_assessments.id', '=', $id);

        return $fourIrAssessmentBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRAssessment
     */
    public function store(array $data): FourIRAssessment
    {
        $fourIrAssessment = app(FourIRAssessment::class);
        $fourIrAssessment->fill($data);
        $fourIrAssessment->save();
        return $fourIrAssessment;
    }

    /**
     * @param FourIRAssessment $fourIRAssessment
     * @param array $data
     * @return FourIRAssessment
     */
    public function update(FourIRAssessment $fourIRAssessment, array $data): FourIRAssessment
    {
        $fourIRAssessment->fill($data);
        $fourIRAssessment->save();
        return $fourIRAssessment;
    }


    /**
     * @param FourIRAssessment $fourIRAssessment
     * @return bool
     */
    public function destroy(FourIRAssessment $fourIRAssessment): bool
    {
        return $fourIRAssessment->delete();
    }

    /**
     * @throws Throwable
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if (!empty($request->input('four_ir_project_id'))) {
            $tnaReport = FourIRCourseDevelopment::where('four_ir_project_id', $request->input('four_ir_project_id'))->first();
            throw_if(empty($tnaReport), ValidationException::withMessages([
                "four_ir_project_id" => "First complete Four IR Course development!"
            ]));
        }

        $rules = [
            'four_ir_project_id' => [
                'required',
                'integer',
                'exists:four_ir_projects,id,deleted_at,NULL',
            ],
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'course_name' => [
                'required',
                'string',
                'max:200'
            ],
            'course_name_en' => [
                'nullable',
                'string',
                'max:200'
            ],
            'examine_name' => [
                'required',
                'string',
                'max:200'
            ],
            'examine_name_en' => [
                'nullable',
                'string',
                'max:200'
            ],
            'examiner_name' => [
                'required',
                'string',
                'max:200'
            ],
            'examiner_name_en' => [
                'nullable',
                'string',
                'max:200'
            ],
            'file_path' => [
                'nullable',
                'string',
                'max:500',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
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
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'four_ir_project_id' => 'required|int',
            'course_name' => 'nullable',
            'examine_name' => 'nullable',
            'examiner_name' => 'nullable',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
            'date' => 'nullable|date',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

}
