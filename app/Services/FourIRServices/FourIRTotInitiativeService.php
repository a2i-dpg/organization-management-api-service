<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeTot;
use App\Models\FourIRInitiativeTotOrganizersParticipant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class FourIRTotInitiativeService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return Model|Builder
     */
    public function getFourIrProjectTOtList(array $request, Carbon $startTime): Builder|Model
    {
        $fourIrProjectId = $request['four_ir_initiative_id'] ?? "";

        /** @var Builder $fourIrProjectTotBuilder */
        $fourIrProjectTotBuilder = FourIRInitiativeTot::select([
            'four_ir_initiative_tots.id',
            'four_ir_initiative_tots.four_ir_initiative_id',

            'four_ir_initiatives.name as initiative_name',
            'four_ir_initiatives.name_en as initiative_name_en',
            'four_ir_initiatives.is_skill_provide',
            'four_ir_initiatives.completion_step',
            'four_ir_initiatives.form_step',

            'four_ir_initiative_tots.accessor_type',
            'four_ir_initiative_tots.accessor_id',
            'four_ir_initiative_tots.participants',
            'four_ir_initiative_tots.master_trainer',
            'four_ir_initiative_tots.date',
            'four_ir_initiative_tots.venue',
            'four_ir_initiative_tots.file_path',
            'four_ir_initiative_tots.row_status',
            'four_ir_initiative_tots.created_by',
            'four_ir_initiative_tots.updated_by',
            'four_ir_initiative_tots.created_at',
            'four_ir_initiative_tots.updated_at'
        ]);

        $fourIrProjectTotBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_tots.four_ir_initiative_id');

        $fourIrProjectTotBuilder->with('organizerParticipants');

        if (is_numeric($fourIrProjectId)) {
            $fourIrProjectTotBuilder->where('four_ir_initiative_tots.four_ir_initiative_id', $fourIrProjectId);
        }

        return $fourIrProjectTotBuilder->firstOrFail();
    }


    /**
     * @param array $data
     * @param array|null $excelRows
     * @return FourIRInitiativeTot
     */
    public function store(array $data, array|null $excelRows): FourIRInitiativeTot
    {
        /** Update initiative stepper */
        $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

        $payload = [];

        if($initiative->form_step < FourIRInitiative::FORM_STEP_TOT){
            $payload['form_step'] = FourIRInitiative::FORM_STEP_TOT;
        }
        if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_EIGHT){
            $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_EIGHT;
        }

        $initiative->fill($payload);
        $initiative->save();

        /** Now store Organizer & participants */
        $fourIrProjectTOt = app(FourIRInitiativeTot::class);
        return $this->storeOrganizerParticipants($fourIrProjectTOt, $data, $excelRows);
    }

    /**
     * @param FourIRInitiativeTot $fourIrInitiativeTot
     * @return void
     */
    public function deletePreviousOrganizerParticipantsForUpdate(FourIRInitiativeTot $fourIrInitiativeTot): void
    {
        $fourIrInitiativeTotOrganizerParticipants = FourIRInitiativeTotOrganizersParticipant::where('four_ir_initiative_tot_id', $fourIrInitiativeTot->id)
            ->get();
        foreach ($fourIrInitiativeTotOrganizerParticipants as $organizerParticipant) {
            $organizerParticipant->delete();
        }
    }

    /**
     * @param FourIRInitiativeTot $fourIrProjectTOt
     * @param array $data
     * @param array|null $excelRows
     * @return FourIRInitiativeTot
     */
    public function update(FourIRInitiativeTot $fourIrProjectTOt, array $data, array|null $excelRows): FourIRInitiativeTot
    {
        return $this->storeOrganizerParticipants($fourIrProjectTOt, $data, $excelRows);
    }

    /**
     * @param FourIRInitiativeTot $fourIrProjectTOt
     * @param array $data
     * @param array|null $excelRows
     * @return FourIRInitiativeTot
     */
    private function storeOrganizerParticipants(FourIRInitiativeTot $fourIrProjectTOt, array $data, ?array $excelRows): FourIRInitiativeTot
    {
        $fourIrProjectTOt->fill($data);
        $fourIrProjectTOt->save();

        $organizers = $data['organizers'];
        foreach ($organizers as $organizer) {
            $organizer['four_ir_initiative_tot_id'] = $fourIrProjectTOt->id;
            $organizer['type'] = FourIRInitiativeTot::TYPE_ORGANIZER;
            $organizer['accessor_type'] = $data['accessor_type'];
            $organizer['accessor_id'] = $data['accessor_id'];

            $fourIrTotOrganizerParticipant = new FourIRInitiativeTotOrganizersParticipant();
            $fourIrTotOrganizerParticipant->fill($organizer);
            $fourIrTotOrganizerParticipant->save();
        }

        $coOrganizers = $data['co_organizers'];
        foreach ($coOrganizers as $coOrganizer) {
            $coOrganizer['four_ir_initiative_tot_id'] = $fourIrProjectTOt->id;
            $coOrganizer['type'] = FourIRInitiativeTot::TYPE_CO_ORGANIZER;
            $coOrganizer['accessor_type'] = $data['accessor_type'];
            $coOrganizer['accessor_id'] = $data['accessor_id'];

            $fourIrTotOrganizerParticipant = new FourIRInitiativeTotOrganizersParticipant();
            $fourIrTotOrganizerParticipant->fill($coOrganizer);
            $fourIrTotOrganizerParticipant->save();
        }

        if (!empty($excelRows)) {
            foreach ($excelRows as $row) {
                $row['four_ir_initiative_tot_id'] = $fourIrProjectTOt->id;
                $row['type'] = FourIRInitiativeTot::TYPE_PARTICIPANT;
                $row['accessor_type'] = $data['accessor_type'];
                $row['accessor_id'] = $data['accessor_id'];

                $fourIrTotOrganizerParticipant = new FourIRInitiativeTotOrganizersParticipant();
                $fourIrTotOrganizerParticipant->fill($row);
                $fourIrTotOrganizerParticipant->save();
            }
        }

        return $fourIrProjectTOt;
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

        if(!empty($data['four_ir_initiative_id'])){
            $fourIrInitiative = FourIRInitiative::findOrFail('four_ir_initiative_id');

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
                "This form step is not allowed as the initiative was set for Not Skill Provider!"
            ]));

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_RESOURCE_MANAGEMENT, ValidationException::withMessages([
                'Complete Resource Management step first.[24000]'
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

            'master_trainer_name' => [
                'required',
                'string'
            ],
            'master_trainer_name_en' => [
                'nullable',
                'string'
            ],
            'master_trainer_mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'master_trainer_address' => [
                'required',
                'string'
            ],
            'master_trainer_address_en' => [
                'nullable',
                'string'
            ],
            'master_trainer_email' => [
                'required',
                'email',
            ],

            'organizers' => [
                'required',
                'array',
                'min:1'
            ],
            'organizers.*.name' => [
                'required',
                'string'
            ],
            'organizers.*.name_en' => [
                'nullable',
                'string'
            ],
            'organizers.*.mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'organizers.*.address' => [
                'required',
                'string'
            ],
            'organizers.*.address_en' => [
                'nullable',
                'string'
            ],
            'organizers.*.email' => [
                'required',
                'email',
            ],

            'co_organizers' => [
                'required',
                'array',
                'min:1'
            ],
            'co_organizers.*.name' => [
                'required',
                'string'
            ],
            'co_organizers.*.name_en' => [
                'nullable',
                'string'
            ],
            'co_organizers.*.mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'co_organizers.*.address' => [
                'required',
                'string'
            ],
            'co_organizers.*.address_en' => [
                'nullable',
                'string'
            ],
            'co_organizers.*.email' => [
                'required',
                'email',
            ],
            'participants_file' => [
                'required',
                'mimes:xlsx, csv, xls'
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
     * @param array $excelData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function excelDataValidator(array $excelData): \Illuminate\Contracts\Validation\Validator
    {
        /** $excelData owns an array. So use * as prefix */
        $rules = [
            '*.name' => [
                'required',
                'string'
            ],
            '*.name_en' => [
                'nullable',
                'string'
            ],
            '*.mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            '*.address' => [
                'required',
                'string'
            ],
            '*.address_en' => [
                'nullable',
                'string'
            ],
            '*.email' => [
                'required',
                'email',
            ],
        ];
        return Validator::make($excelData, $rules);
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
