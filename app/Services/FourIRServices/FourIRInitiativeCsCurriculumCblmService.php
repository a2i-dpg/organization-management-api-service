<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIrCsCurriculumCblmExpert;
use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeCsCurriculumCblm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;


/**
 * Class RankService
 * @package App\Services
 */
class FourIRInitiativeCsCurriculumCblmService
{
    /**
     * @param array $request
     * @return FourIRInitiativeCsCurriculumCblm
     */
    public function getOneFourIrProjectCs(array $request): FourIRInitiativeCsCurriculumCblm
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";

        /** @var FourIRInitiativeCsCurriculumCblm|Builder $fourIrInitiativeCsCurriculumCblmBuilder */
        $fourIrInitiativeCsCurriculumCblmBuilder = FourIRInitiativeCsCurriculumCblm::select(
            [
                'four_ir_initiative_cs_curriculum_cblm.id',
                'four_ir_initiative_cs_curriculum_cblm.four_ir_initiative_id',
                'four_ir_initiative_cs_curriculum_cblm.type',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_initiative_cs_curriculum_cblm.level_from',
                'four_ir_initiative_cs_curriculum_cblm.level_to',
                'four_ir_initiative_cs_curriculum_cblm.approved_by',
                'four_ir_initiative_cs_curriculum_cblm.developed_organization_name',
                'four_ir_initiative_cs_curriculum_cblm.developed_organization_name_en',
                'four_ir_initiative_cs_curriculum_cblm.sector_name',
                'four_ir_initiative_cs_curriculum_cblm.supported_organization_name',
                'four_ir_initiative_cs_curriculum_cblm.supported_organization_name_en',
                'four_ir_initiative_cs_curriculum_cblm.file_path',
                'four_ir_initiative_cs_curriculum_cblm.comments',
                'four_ir_initiative_cs_curriculum_cblm.row_status',
                'four_ir_initiative_cs_curriculum_cblm.created_by',
                'four_ir_initiative_cs_curriculum_cblm.updated_by',
                'four_ir_initiative_cs_curriculum_cblm.created_at',
                'four_ir_initiative_cs_curriculum_cblm.updated_at'
            ]
        );

        $fourIrInitiativeCsCurriculumCblmBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_cs_curriculum_cblm.four_ir_initiative_id');

        if (!empty($fourIrInitiativeId)) {
            $fourIrInitiativeCsCurriculumCblmBuilder->where('four_ir_initiative_cs_curriculum_cblm.four_ir_initiative_id', $fourIrInitiativeId);
        }

        $fourIrInitiativeCsCurriculumCblmBuilder->with('csCurriculumCblmExperts');

        return $fourIrInitiativeCsCurriculumCblmBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRInitiativeCsCurriculumCblm
     */
    public function store(array $data): FourIRInitiativeCsCurriculumCblm
    {
        /** Update Form step & completion step in initiative table */
        $this->updateInitiativeStepper($data);

        /** Create CsCurriculumCblm */
        $fourIrInitiativeCsCurriculumCblm = new FourIRInitiativeCsCurriculumCblm();
        $fourIrInitiativeCsCurriculumCblm->fill($data);
        $fourIrInitiativeCsCurriculumCblm->save();

        /** Now store CsCurriculumCblm Experts */
        $experts = $data['experts'];

        foreach ($experts as $expert){
            $expert['four_ir_initiative_cs_curriculum_cblm_id'] = $fourIrInitiativeCsCurriculumCblm->id;
            $expert['accessor_type'] = $fourIrInitiativeCsCurriculumCblm->accessor_type;
            $expert['accessor_id'] = $fourIrInitiativeCsCurriculumCblm->accessor_id;

            $fourIrInitiativeCsCurriculumCblm = new FourIrCsCurriculumCblmExpert();
            $fourIrInitiativeCsCurriculumCblm->fill($expert);
            $fourIrInitiativeCsCurriculumCblm->save();
        }

        return $fourIrInitiativeCsCurriculumCblm;
    }

    /**
     * @param array $data
     * @return void
     */
    private function updateInitiativeStepper(array $data){
        $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

        $payload = [];

        if($data['type'] == FourIRInitiativeCsCurriculumCblm::TYPE_CS){
            if($initiative->form_step < FourIRInitiative::FORM_STEP_CS){
                $payload['form_step'] = FourIRInitiative::FORM_STEP_CS;
            }
            if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_FOUR){
                $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_FOUR;
            }
        } else if($data['type'] == FourIRInitiativeCsCurriculumCblm::TYPE_CURRICULUM){
            if($initiative->form_step < FourIRInitiative::FORM_STEP_CURRICULUM){
                $payload['form_step'] = FourIRInitiative::FORM_STEP_CURRICULUM;
            }
            if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_FIVE){
                $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_FIVE;
            }
        } else {
            if($initiative->form_step < FourIRInitiative::FORM_STEP_CBLM){
                $payload['form_step'] = FourIRInitiative::FORM_STEP_CBLM;
            }
            if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_SIX){
                $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_SIX;
            }
        }

        $initiative->fill($payload);
        $initiative->save();
    }

    /**
     * @param FourIRInitiativeCsCurriculumCblm $fourIrInitiativeCsCurriculumCblm
     * @param array $data
     * @return FourIRInitiativeCsCurriculumCblm
     */
    public function update(FourIRInitiativeCsCurriculumCblm $fourIrInitiativeCsCurriculumCblm, array $data): FourIRInitiativeCsCurriculumCblm
    {
        $fourIrInitiativeCsCurriculumCblm->fill($data);
        $fourIrInitiativeCsCurriculumCblm->save();

        /** First delete all previous experts */
        $fourIrInitiativeCsCurriculumCblmExperts = FourIrCsCurriculumCblmExpert::where('four_ir_initiative_cs_curriculum_cblm_id', $fourIrInitiativeCsCurriculumCblm->id)
                ->get();
        foreach ($fourIrInitiativeCsCurriculumCblmExperts as $expert){
            $expert->delete();
        }

        /** Now insert new experts */
        $experts = $data['experts'];

        foreach ($experts as $expert){
            $expert['four_ir_initiative_cs_curriculum_cblm_id'] = $fourIrInitiativeCsCurriculumCblm->id;
            $expert['accessor_type'] = $fourIrInitiativeCsCurriculumCblm->accessor_type;
            $expert['accessor_id'] = $fourIrInitiativeCsCurriculumCblm->accessor_id;

            $fourIrInitiativeCsCurriculumCblm = new FourIrCsCurriculumCblmExpert();
            $fourIrInitiativeCsCurriculumCblm->fill($expert);
            $fourIrInitiativeCsCurriculumCblm->save();
        }

        return $fourIrInitiativeCsCurriculumCblm;
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

        if(!empty($data['four_ir_initiative_id']) && !empty($data['type'])){
            $fourIrInitiative = FourIRInitiative::findOrFail('four_ir_initiative_id');

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
                "This form step is not allowed as the initiative was set for Not Skill Provider!"
            ]));

            if($data['type'] == FourIRInitiativeCsCurriculumCblm::TYPE_CS){
                throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_TNA, ValidationException::withMessages([
                    'Complete Tna report step first.[24000]'
                ]));
            } else if($data['type'] == FourIRInitiativeCsCurriculumCblm::TYPE_CURRICULUM){
                throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_CS, ValidationException::withMessages([
                    'Complete CS step first.[24000]'
                ]));
            } else {
                throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_CURRICULUM, ValidationException::withMessages([
                    'Complete Curriculum step first.[24000]'
                ]));
            }
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
            'type' => [
                'required',
                'int',
                Rule::in(FourIRInitiativeCsCurriculumCblm::TYPES)
            ],

            'experts' => [
                'required',
                'array',
                'min:1'
            ],
            'experts.*.name' => [
                'required',
                'string'
            ],
            'experts.*.name_en' => [
                'nullable',
                'string'
            ],
            'experts.*.designation' => [
                'required',
                'string'
            ],
            'experts.*.organization' => [
                'required',
                'string'
            ],
            'experts.*.organization_en' => [
                'nullable',
                'string'
            ],
            'experts.*.mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'experts.*.email' => [
                'required',
                'email',
            ],

            'level_from' => [
                Rule::requiredIf(function () use ($data) {
                    return $data['type'] == FourIRInitiativeCsCurriculumCblm::TYPE_CS;
                }),
                'nullable',
                'int',
                Rule::in(FourIRInitiativeCsCurriculumCblm::LEVELS)
            ],
            'level_to' => [
                Rule::requiredIf(function () use ($data) {
                    return $data['type'] == FourIRInitiativeCsCurriculumCblm::TYPE_CS;
                }),
                'nullable',
                'int',
                Rule::in(FourIRInitiativeCsCurriculumCblm::LEVELS)
            ],
            'approved_by' => [
                'required',
                'int',
                Rule::in(FourIRInitiativeCsCurriculumCblm::APPROVED_BYS)
            ],
            'developed_organization_name' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'developed_organization_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'sector_name' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'supported_organization_name' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'supported_organization_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'file_path' => [
                'nullable',
                'string',
                'max:500',
            ],
            'comments' => [
                'nullable',
                'string',
                'max:1000',
                'min:2'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($data, $rules, $customMessage);
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
            'start_date' => 'nullable|date',
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
