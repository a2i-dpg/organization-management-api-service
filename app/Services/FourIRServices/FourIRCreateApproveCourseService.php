<?php


namespace App\Services\FourIRServices;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;


/**
 * Class FourIRCreateApproveCourseService
 * @package App\Services
 */
class FourIRCreateApproveCourseService
{
    /**
     * @param array $request
     * @return array
     */
    public function getFourIrInitiativeList(array $request): array
    {
        $response = ServiceToServiceCall::getFourIrCourseList($request);

        $courseList = $response['data'];

        foreach ($courseList as &$course){
            $fourIrInitiative = FourIRInitiative::find($course['four_ir_initiative_id']);
            $course['is_skill_provide'] = $fourIrInitiative->is_skill_provide;
            $course['completion_step'] = $fourIrInitiative->completion_step;
            $course['form_step'] = $fourIrInitiative->form_step;
        }

        $response['data'] = $courseList;

        return $response;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getOneFourIrInitiative(int $id): array
    {
        $response = ServiceToServiceCall::getFourIrCourseByCourseId($id);

        $course = $response['data'];

        $fourIrInitiative = FourIRInitiative::find($course['four_ir_initiative_id']);
        $course['is_skill_provide'] = $fourIrInitiative->is_skill_provide;
        $course['completion_step'] = $fourIrInitiative->completion_step;
        $course['form_step'] = $fourIrInitiative->form_step;

        $response['data'] = $course;

        return $response;
    }

    /**
     * @param array $data
     * @return array
     */
    public function store(array $data): array
    {
        /** Update initiative stepper */
        $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

        $payload = [];

        if($initiative->form_step < FourIRInitiative::FORM_STEP_SCALE_UP){
            $payload['form_step'] = FourIRInitiative::FORM_STEP_SCALE_UP;
        }
        if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_FIFTEEN){
            $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_FIFTEEN;
        }

        $initiative->fill($payload);
        $initiative->save();

        /** Now store Four Ir Course */
        return ServiceToServiceCall::createFourIrCourse($data);
    }

    /**
     * @param array $data
     * @param int $courseId
     * @return array
     */
    public function update(array $data, int $courseId): array
    {
        return ServiceToServiceCall::updateFourIrCourse($data, $courseId);
    }

    /**
     * @param int $courseId
     * @return array
     */
    public function approveFourIrCourse(int $courseId): array
    {
        return ServiceToServiceCall::approveFourIrCourse($courseId);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws Throwable
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        if (!empty($data['skills'])) {
            $data["skills"] = isset($data['skills']) && is_array($data['skills']) ? $data['skills'] : explode(',', $data['skills']);
        }
        $customMessage = [
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        if(!empty($data['four_ir_initiative_id'])){
            $fourIrInitiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
                "This form step is not allowed as the initiative was set for Not Skill Provider!"
            ]));

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_TOT, ValidationException::withMessages([
                'Complete Tot step first.[24000]'
            ]));
        }

        $rules = [
            'four_ir_initiative_id' => [
                'required',
                'integer',
                'exists:four_ir_initiatives,id,deleted_at,NULL',
            ],
            'industry_association_id' => [
                "nullable",
                "int"
            ],
            'branch_id' => [
                'nullable',
                'int',
            ],
            'program_id' => [
                'nullable',
                'int',
            ],
            'title' => [
                'required',
                'string',
                'max:1000',
                'min:2'
            ],
            "level" => [
                'required',
                'int',
                Rule::in(BaseModel::COURSE_LEVELS)
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:255',
                'min:2'
            ],
            'course_fee' => [
                'sometimes',
                'required',
                'numeric',
            ],
            'duration' => [
                'nullable',
                'numeric',
            ],
            'overview' => [
                'nullable',
                'string'
            ],
            'overview_en' => [
                'nullable',
                'string'
            ],
            'target_group' => [
                'nullable',
                'string',
                'max: 1000',
            ],
            'target_group_en' => [
                'nullable',
                'string',
                'max: 500',
            ],
            'objectives' => [
                'nullable',
                'string'
            ],
            'objectives_en' => [
                'nullable',
                'string'
            ],
            'lessons' => [
                'nullable',
                'string'
            ],
            'lessons_en' => [
                'nullable',
                'string'
            ],
            "language_medium" => [
                "required",
                Rule::in(BaseModel::COURSE_LANGUAGE_MEDIUMS)
            ],
            'training_methodology' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'training_methodology_en' => [
                'nullable',
                'string',
                'max:600',
            ],
            'evaluation_system' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'evaluation_system_en' => [
                'nullable',
                'string',
                'max:500',
            ],
            'prerequisite' => [
                'nullable',
                'string'
            ],
            'prerequisite_en' => [
                'nullable',
                'string'
            ],
            'eligibility' => [
                'nullable',
                'string',
            ],
            'eligibility_en' => [
                'nullable',
                'string',
            ],
            'cover_image' => [
                'nullable',
                'string'
            ],
            'application_form_settings' => [
                'nullable',
                'string',
            ],
            "skills" => [
                "required",
                "array",
                "min:1",
                "max:10"
            ],
            "skills.*" => [
                "required",
                'integer',
                "distinct",
                "min:1",
                "exists:skills,id,deleted_at,NULL",
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
        ];

        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
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
            'four_ir_initiative_id' => 'required|int',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',
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
