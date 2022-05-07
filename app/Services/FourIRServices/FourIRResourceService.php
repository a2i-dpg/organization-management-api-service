<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;


/**
 * Class FourIRResourceService
 * @package App\Services\FourIRResourceService
 */
class FourIRResourceService
{

    /**
     * @param array $request
     * @return Model|Builder
     */
    public function getFourIRResourceList(array $request): Builder|Model
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";

        /** @var Builder $fourIrResourceBuilder */
        $fourIrResourceBuilder = FourIRResource::select(
            [
                'four_ir_resources.id',
                'four_ir_resources.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_resources.approval_status',
                'four_ir_resources.budget_approval_status',
                'four_ir_resources.given_budget',
                'four_ir_resources.file_path',
                'four_ir_resources.row_status',
                'four_ir_resources.created_by',
                'four_ir_resources.updated_by',
                'four_ir_resources.created_at',
                'four_ir_resources.updated_at'
            ]
        );

        $fourIrResourceBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_resources.four_ir_initiative_id');

        if (is_numeric($fourIrInitiativeId)) {
            $fourIrResourceBuilder->where('four_ir_resources.four_ir_initiative_id', $fourIrInitiativeId);
        }

        return $fourIrResourceBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @param FourIRResource|null $fourIRResource
     * @return FourIRResource
     */
    public function store(array $data, FourIRResource|null $fourIRResource): FourIRResource
    {
        if(empty($fourIRResource)) {
            /** Update initiative stepper */
            $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

            $payload = [];

            if($initiative->form_step < FourIRInitiative::FORM_STEP_RESOURCE_MANAGEMENT){
                $payload['form_step'] = FourIRInitiative::FORM_STEP_RESOURCE_MANAGEMENT;
            }
            if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_SEVEN){
                $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_SEVEN;
            }

            $initiative->fill($payload);
            $initiative->save();

            /** Create new instance to store */
            $fourIRResource = new FourIRResource();
        }
        $fourIRResource->fill($data);
        $fourIRResource->save();

        return $fourIRResource;
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
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if(!empty($data['four_ir_initiative_id'])){
            $fourIrInitiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
                "This form step is not allowed as the initiative was set for Not Skill Provider!"
            ]));

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_CBLM, ValidationException::withMessages([
                'Complete CBLM step first.[24000]'
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
            'approval_status' => [
                'required',
                'int',
                Rule::in(BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE)
            ],
            'budget_approval_status' => [
                'required',
                'int',
                Rule::in(BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE)
            ],
            'given_budget' => [
                'required',
                'numeric'
            ],
            'file_path' => [
                'nullable',
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
        ];
        return Validator::make($data, $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'four_ir_initiative_id' => 'required|int'
        ]);
    }
}
