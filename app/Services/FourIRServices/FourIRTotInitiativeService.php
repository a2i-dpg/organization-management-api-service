<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeTot;
use App\Models\FourIRInitiativeTotMastersTrainersParticipant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FourIRTotInitiativeService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectTOtList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_initiative_id'] ?? "";
        $masterTrainerName = $request['master_trainer_name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrInitiativeTotBuilder */
        $fourIrInitiativeTotBuilder = FourIRInitiativeTot::select([
            'four_ir_initiative_tots.id',
            'four_ir_initiative_tots.four_ir_initiative_id',

            'four_ir_initiatives.name as initiative_name',
            'four_ir_initiatives.name_en as initiative_name_en',
            'four_ir_initiatives.is_skill_provide',
            'four_ir_initiatives.completion_step',
            'four_ir_initiatives.form_step',

            'four_ir_initiative_tots.organiser_name',
            'four_ir_initiative_tots.organiser_mobile',
            'four_ir_initiative_tots.organiser_address',
            'four_ir_initiative_tots.organiser_address_en',
            'four_ir_initiative_tots.co_organiser_name',
            'four_ir_initiative_tots.co_organiser_mobile',
            'four_ir_initiative_tots.co_organiser_address',
            'four_ir_initiative_tots.co_organiser_address_en',

            'four_ir_initiative_tots.accessor_type',
            'four_ir_initiative_tots.accessor_id',
            'four_ir_initiative_tots.row_status',
            'four_ir_initiative_tots.created_by',
            'four_ir_initiative_tots.updated_by',
            'four_ir_initiative_tots.created_at',
            'four_ir_initiative_tots.updated_at'
        ])->acl();

        $fourIrInitiativeTotBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_tots.four_ir_initiative_id');

        if (is_numeric($fourIrProjectId)) {
            $fourIrInitiativeTotBuilder->where('four_ir_initiative_tots.four_ir_initiative_id', $fourIrProjectId);
        }

        if (!empty($masterTrainerName)) {
            $fourIrInitiativeTotBuilder->where(function ($builder) use ($masterTrainerName){
                $builder->where('four_ir_initiative_tots.master_trainer_name', 'like', '%' . $masterTrainerName . '%');
                $builder->orWhere('four_ir_initiative_tots.master_trainer_name_en', 'like', '%' . $masterTrainerName . '%');
            });
        }

        if (is_numeric($rowStatus)) {
            $fourIrInitiativeTotBuilder->where('four_ir_initiative_tots.row_status', $rowStatus);
        }

        $fourIrInitiativeTotBuilder->orderBy('four_ir_initiative_tots.id', $order);

        /** @var Collection $fourIrInitiativeTots */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrInitiativeTots = $fourIrInitiativeTotBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrInitiativeTots->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrInitiativeTots = $fourIrInitiativeTotBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrInitiativeTots->toArray()['data'] ?? $fourIrInitiativeTots->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRInitiativeTot
     */
    public function getOneFourIrInitiativeAnalysis(int $id): FourIRInitiativeTot
    {
        /** @var FourIRInitiativeTot|Builder $fourIrInitiativeTotBuilder */
        $fourIrInitiativeTotBuilder = FourIRInitiativeTot::select(
            [
                'four_ir_initiative_tots.id',
                'four_ir_initiative_tots.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_initiative_tots.organiser_name',
                'four_ir_initiative_tots.organiser_mobile',
                'four_ir_initiative_tots.organiser_address',
                'four_ir_initiative_tots.organiser_email',
                'four_ir_initiative_tots.organiser_address_en',
                'four_ir_initiative_tots.co_organiser_name',
                'four_ir_initiative_tots.co_organiser_mobile',
                'four_ir_initiative_tots.co_organiser_address',
                'four_ir_initiative_tots.co_organiser_email',
                'four_ir_initiative_tots.co_organiser_address_en',

                'four_ir_initiative_tots.accessor_type',
                'four_ir_initiative_tots.accessor_id',
                'four_ir_initiative_tots.row_status',
                'four_ir_initiative_tots.created_by',
                'four_ir_initiative_tots.updated_by',
                'four_ir_initiative_tots.created_at',
                'four_ir_initiative_tots.updated_at'
            ]
        );
        $fourIrInitiativeTotBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_tots.four_ir_initiative_id');

        $fourIrInitiativeTotBuilder->with('masterTrainers');

        $fourIrInitiativeTotBuilder->where('four_ir_initiative_tots.id', '=', $id);

        return $fourIrInitiativeTotBuilder->firstOrFail();
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
        return $this->storeMasterTrainers($fourIrProjectTOt, $data, $excelRows);
    }

    /**
     * @param FourIRInitiativeTot $fourIrInitiativeTot
     * @return void
     */
    public function deletePreviousOrganizerParticipantsForUpdate(FourIRInitiativeTot $fourIrInitiativeTot): void
    {
        $fourIrInitiativeTotOrganizerParticipants = FourIRInitiativeTotMastersTrainersParticipant::where('four_ir_initiative_tot_id', $fourIrInitiativeTot->id)
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
        return $this->storeMasterTrainers($fourIrProjectTOt, $data, $excelRows);
    }

    /**
     * @param FourIRInitiativeTot $fourIrProjectTOt
     * @param array $data
     * @param array|null $excelRows
     * @return FourIRInitiativeTot
     */
    private function storeMasterTrainers(FourIRInitiativeTot $fourIrProjectTOt, array $data, ?array $excelRows): FourIRInitiativeTot
    {
        $fourIrProjectTOt->fill($data);
        $fourIrProjectTOt->save();

        $masterTrainers = $data['master_trainers'] ?? [];
        foreach ($masterTrainers as $masterTrainer) {
            $masterTrainer['four_ir_initiative_tot_id'] = $fourIrProjectTOt->id;
            $masterTrainer['type'] = FourIRInitiativeTot::TYPE_ORGANIZER;
            $masterTrainer['accessor_type'] = $data['accessor_type'];
            $masterTrainer['accessor_id'] = $data['accessor_id'];

            $fourIrTotMasterTrainerParticipant = new FourIRInitiativeTotMastersTrainersParticipant();
            $fourIrTotMasterTrainerParticipant->fill($masterTrainer);
            $fourIrTotMasterTrainerParticipant->save();
        }

        $coOrganizers = $data['co_organizers'] ?? [];
        foreach ($coOrganizers as $coOrganizer) {
            $coOrganizer['four_ir_initiative_tot_id'] = $fourIrProjectTOt->id;
            $coOrganizer['type'] = FourIRInitiativeTot::TYPE_CO_ORGANIZER;
            $coOrganizer['accessor_type'] = $data['accessor_type'];
            $coOrganizer['accessor_id'] = $data['accessor_id'];

            $fourIrTotOrganizerParticipant = new FourIRInitiativeTotMastersTrainersParticipant();
            $fourIrTotOrganizerParticipant->fill($coOrganizer);
            $fourIrTotOrganizerParticipant->save();
        }

        if (!empty($excelRows)) {
            foreach ($excelRows as $row) {
                $row['four_ir_initiative_tot_id'] = $fourIrProjectTOt->id;
                $row['type'] = FourIRInitiativeTot::TYPE_PARTICIPANT;
                $row['accessor_type'] = $data['accessor_type'];
                $row['accessor_id'] = $data['accessor_id'];

                $fourIrTotOrganizerParticipant = new FourIRInitiativeTotMastersTrainersParticipant();
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
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $data['master_trainers']=json_decode( $data['master_trainers'],true);
        if(!empty($data['four_ir_initiative_id'])){
            $fourIrInitiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

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

            'organiser_name' => [
                'required',
                'string'
            ],
            'organiser_name_en' => [
                'nullable',
                'string'
            ],
            'organiser_mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'organiser_address' => [
                'required',
                'string'
            ],
            'organiser_address_en' => [
                'nullable',
                'string'
            ],
            'organiser_email' => [
                'required',
                'email',
            ],
            'co_organiser_name' => [
                'required',
                'string'
            ],
            'co_organiser_name_en' => [
                'nullable',
                'string'
            ],
            'co_organiser_mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'co_organiser_address' => [
                'required',
                'string'
            ],
            'co_organiser_address_en' => [
                'nullable',
                'string'
            ],
            'co_organiser_email' => [
                'required',
                'email',
            ],
            'master_trainers' => [
                'required',
                'array',
                'min:1'
            ],
            'master_trainers.*' => [
                'required',
                'array'
            ],
            'master_trainers.*.name' => [
                'required',
                'string'
            ],
            'master_trainers.*.name_en' => [
                'nullable',
                'string'
            ],
            'master_trainers.*.mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'master_trainers.*.address' => [
                'required',
                'string'
            ],
            'master_trainers.*.address_en' => [
                'nullable',
                'string'
            ],
            'master_trainers.*.email' => [
                'required',
                'email',
            ],
            'participants_file' => [
                'nullable',
                'mimes:xlsx, csv, xls'
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
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'four_ir_initiative_id' => 'required|int',
            'master_trainer_name' => 'nullable|string',
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
