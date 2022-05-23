<?php

namespace App\Services\FourIRServices;

use App\Facade\ServiceToServiceCall;
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
     * @param int $fourIrInitiativeId
     * @return array
     */
    public function getFourIrAssessmentList(array $request, int $fourIrInitiativeId): array
    {
        return ServiceToServiceCall::getYouthAssessmentList($request, $fourIrInitiativeId);
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
            'four_ir_assessments.four_ir_initiative_id',
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
        FourIrInitiativeService::accessor();
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if (!empty($request->input('four_ir_initiative_id'))) {
            $tnaReport = FourIRCourseDevelopment::where('four_ir_initiative_id', $request->input('four_ir_initiative_id'))->first();
            throw_if(empty($tnaReport), ValidationException::withMessages([
                "four_ir_initiative_id" => "First complete Four IR Course development!"
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
            'course_id' => 'nullable',
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
