<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRCourseDevelopment;
use App\Models\FourIREnrollmentApproval;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Application;
use Ramsey\Collection\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FourIRSkillDevelopmentService
{
    public function getFourIRCourseDevelopmentList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_initiative_id'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var FourIRCourseDevelopment|Builder $fourIrCourseDevelopmentBuilder */
        $fourIrCourseDevelopmentBuilder = FourIRCourseDevelopment::select([
            'four_ir_course_developments.id',
            'four_ir_course_developments.four_ir_initiative_id',
            'four_ir_course_developments.accessor_type',
            'four_ir_course_developments.accessor_id',
            'four_ir_course_developments.training_center_details',
            'four_ir_course_developments.training_center_details_en',
            'four_ir_course_developments.training_details',
            'four_ir_course_developments.training_details_en',
            'four_ir_course_developments.start_date',
            'four_ir_course_developments.end_date',
            'four_ir_course_developments.training_launch_date',
            'four_ir_course_developments.row_status',
            'four_ir_course_developments.created_by',
            'four_ir_course_developments.updated_by',
            'four_ir_course_developments.created_at',
            'four_ir_course_developments.updated_at'
        ])->acl();

        $fourIrCourseDevelopmentBuilder->orderBy('four_ir_course_developments.id', $order);

        if (is_numeric($fourIrProjectId)) {
            $fourIrCourseDevelopmentBuilder->where('four_ir_course_developments.four_ir_initiative_id', $fourIrProjectId);
        }
        if (is_numeric($rowStatus)) {
            $fourIrCourseDevelopmentBuilder->where('four_ir_course_developments.row_status', $rowStatus);
        }

        /** @var  Collection $fourIrCourseDevelopments */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrCourseDevelopments = $fourIrCourseDevelopmentBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrCourseDevelopments->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrCourseDevelopments = $fourIrCourseDevelopmentBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrCourseDevelopments->toArray()['data'] ?? $fourIrCourseDevelopments->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;

    }


    /**
     * @param int $id
     * @return FourIRCourseDevelopment
     */
    public function getOneFourIRCourseDevelopment(int $id): FourIRCourseDevelopment
    {
        /** @var FourIRCourseDevelopment|Builder $fourIrCourseDevelopmentBuilder */
        $fourIrCourseDevelopmentBuilder = FourIRCourseDevelopment::select([
            'four_ir_course_developments.id',
            'four_ir_course_developments.four_ir_initiative_id',
            'four_ir_course_developments.accessor_type',
            'four_ir_course_developments.accessor_id',
            'four_ir_course_developments.training_center_details',
            'four_ir_course_developments.training_center_details_en',
            'four_ir_course_developments.training_details',
            'four_ir_course_developments.training_details_en',
            'four_ir_course_developments.start_date',
            'four_ir_course_developments.end_date',
            'four_ir_course_developments.training_launch_date',
            'four_ir_course_developments.row_status',
            'four_ir_course_developments.created_by',
            'four_ir_course_developments.updated_by',
            'four_ir_course_developments.created_at',
            'four_ir_course_developments.updated_at'
        ])->acl();

        $fourIrCourseDevelopmentBuilder->where('four_ir_course_developments.id', '=', $id);

        return $fourIrCourseDevelopmentBuilder->firstOrFail();
    }


    public function store(array $data): FourIRCourseDevelopment
    {
        $fourIRCourseDevelopment = app()->make(FourIRCourseDevelopment::class);
        $fourIRCourseDevelopment->fill($data);
        $fourIRCourseDevelopment->save();
        return $fourIRCourseDevelopment;

    }

    /**
     * @param FourIRCourseDevelopment $fourIRCourseDevelopment
     * @param array $data
     * @return FourIRCourseDevelopment
     */
    public function update(FourIRCourseDevelopment $fourIRCourseDevelopment, array $data): FourIRCourseDevelopment
    {
        $fourIRCourseDevelopment->fill($data);
        $fourIRCourseDevelopment->save();
        return $fourIRCourseDevelopment;
    }

    /**
     * @param FourIRCourseDevelopment $fourIRCourseDevelopment
     * @return bool
     */
    public function destroy(FourIRCourseDevelopment $fourIRCourseDevelopment): bool
    {
        return $fourIRCourseDevelopment->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws Throwable
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if (!empty($request->input('four_ir_initiative_id'))) {
            $enrollmentApproval = FourIREnrollmentApproval::where('four_ir_initiative_id', $request->input('four_ir_initiative_id'))->first();
            throw_if(empty($enrollmentApproval), ValidationException::withMessages([
                "four_ir_initiative_id" => "First complete Four IR Enrollment Approval"
            ]));
        }
        $rules = [
            'four_ir_initiative_id' => [
                'required',
                'integer',
                'exists:four_ir_initiatives,id,deleted_at,NULL',
            ],
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'training_center_details' => [
                'required',
                'string',
            ],
            'training_center_details_en' => [
                'nullable',
                'string',
            ],
            'training_details' => [
                'required',
                'string',
            ],
            'training_details_en' => [
                'nullable',
                'string',
            ],
            'start_date' => [
                'required',
                'date-format:Y-m-d',
            ],
            'end_date' => [
                'required',
                'date-format:Y-m-d',
            ],
            'training_launch_date' => [
                'required',
                'date-format:Y-m-d',
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
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'four_ir_initiative_id' => 'required|int',
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
