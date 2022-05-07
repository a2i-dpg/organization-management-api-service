<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeAnalysis;
use App\Models\FourIRInitiativeAnalysisResearchTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


/**
 * Class FourIRInitiativeAnalysisService
 * @package App\Services
 */
class FourIRInitiativeAnalysisService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrInitiativeAnalysisList(array $request, Carbon $startTime): array
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";
        $researcherName = $request['researcher_name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrInitiativeAnalysisBuilder */
        $fourIrInitiativeAnalysisBuilder = FourIRInitiativeAnalysis::select(
            [
                'four_ir_initiative_analysis.id',
                'four_ir_initiative_analysis.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_initiative_analysis.researcher_name',
                'four_ir_initiative_analysis.researcher_name_en',
                'four_ir_initiative_analysis.organization_name',
                'four_ir_initiative_analysis.organization_name_en',
                'four_ir_initiative_analysis.research_method',
                'four_ir_initiative_analysis.file_path',
                'four_ir_initiative_analysis.accessor_type',
                'four_ir_initiative_analysis.accessor_id',
                'four_ir_initiative_analysis.row_status',
                'four_ir_initiative_analysis.created_by',
                'four_ir_initiative_analysis.updated_by',
                'four_ir_initiative_analysis.created_at',
                'four_ir_initiative_analysis.updated_at'
            ]
        )->acl();

        $fourIrInitiativeAnalysisBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_analysis.four_ir_initiative_id');

        $fourIrInitiativeAnalysisBuilder->orderBy('four_ir_initiative_analysis.id', $order);

        if(!empty($fourIrInitiativeId)){
            $fourIrInitiativeAnalysisBuilder->where('four_ir_initiative_analysis.four_ir_initiative_id', $fourIrInitiativeId);
        }

        if (!empty($researcherName)) {
            $fourIrInitiativeAnalysisBuilder->where(function ($builder) use ($researcherName){
                $builder->where('four_ir_initiative_analysis.researcher_name', 'like', '%' . $researcherName . '%');
                $builder->orWhere('four_ir_initiative_analysis.researcher_name_en', 'like', '%' . $researcherName . '%');
            });
        }

        if (is_numeric($rowStatus)) {
            $fourIrInitiativeAnalysisBuilder->where('four_ir_initiative_analysis.row_status', $rowStatus);
        }

        /** @var Collection $fourIrInitiativeAnalysis */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrInitiativeAnalysis = $fourIrInitiativeAnalysisBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrInitiativeAnalysis->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrInitiativeAnalysis = $fourIrInitiativeAnalysisBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrInitiativeAnalysis->toArray()['data'] ?? $fourIrInitiativeAnalysis->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRInitiativeAnalysis
     */
    public function getOneFourIrInitiativeAnalysis(int $id): FourIRInitiativeAnalysis
    {
        /** @var FourIRInitiativeAnalysis|Builder $fourIrInitiativeAnalysisBuilder */
        $fourIrInitiativeAnalysisBuilder = FourIRInitiativeAnalysis::select(
            [
                'four_ir_initiative_analysis.id',
                'four_ir_initiative_analysis.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_initiative_analysis.researcher_name',
                'four_ir_initiative_analysis.researcher_name_en',
                'four_ir_initiative_analysis.organization_name',
                'four_ir_initiative_analysis.organization_name_en',
                'four_ir_initiative_analysis.research_method',
                'four_ir_initiative_analysis.file_path',
                'four_ir_initiative_analysis.accessor_type',
                'four_ir_initiative_analysis.accessor_id',
                'four_ir_initiative_analysis.row_status',
                'four_ir_initiative_analysis.created_by',
                'four_ir_initiative_analysis.updated_by',
                'four_ir_initiative_analysis.created_at',
                'four_ir_initiative_analysis.updated_at'
            ]
        );
        $fourIrInitiativeAnalysisBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_analysis.four_ir_initiative_id');

        $fourIrInitiativeAnalysisBuilder->where('four_ir_initiative_analysis.id', '=', $id);

        return $fourIrInitiativeAnalysisBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @param array|null $excelRows
     * @return FourIRInitiativeAnalysis
     */
    public function store(array $data, array|null $excelRows): FourIRInitiativeAnalysis
    {
        /** Update form step & completion step first */
        $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

        $payload = [];

        if($initiative->form_step < FourIRInitiative::FORM_STEP_PROJECT_ANALYSIS){
            $payload['form_step'] = FourIRInitiative::FORM_STEP_PROJECT_ANALYSIS;
        }
        if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_SIXTEEN){
            $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_SIXTEEN;
        }

        $initiative->fill($payload);
        $initiative->save();

        /** Now store Initiative analysis */
        $fourIrInitiativeAnalysis = new FourIRInitiativeAnalysis();
        return $this->storeResearchTeam($fourIrInitiativeAnalysis, $data, $excelRows);
    }

    /**
     * @param FourIRInitiativeAnalysis $fourIrInitiativeAnalysis
     * @return void
     */
    public function deletePreviousResearchTeamForUpdate(FourIRInitiativeAnalysis $fourIrInitiativeAnalysis){
        $fourIrInitiativeAnalysisResearchTeams = FourIRInitiativeAnalysisResearchTeam::where('four_ir_initiative_analysis_id', $fourIrInitiativeAnalysis->id)
            ->get();
        foreach ($fourIrInitiativeAnalysisResearchTeams as $team){
            $team->delete();
        }
    }

    /**
     * @param FourIRInitiativeAnalysis $fourIrInitiativeAnalysis
     * @param array $data
     * @param array|null $excelRows
     * @return FourIRInitiativeAnalysis
     */
    public function update(FourIRInitiativeAnalysis $fourIrInitiativeAnalysis, array $data, array|null $excelRows): FourIRInitiativeAnalysis
    {
        return $this->storeResearchTeam($fourIrInitiativeAnalysis, $data, $excelRows);
    }

    /**
     * @param FourIRInitiativeAnalysis $fourIrInitiativeAnalysis
     * @param array $data
     * @param array|null $excelRows
     * @return FourIRInitiativeAnalysis
     */
    private function storeResearchTeam(FourIRInitiativeAnalysis $fourIrInitiativeAnalysis, array $data, ?array $excelRows): FourIRInitiativeAnalysis
    {
        $fourIrInitiativeAnalysis->fill($data);
        $fourIrInitiativeAnalysis->save();

        if (!empty($excelRows)) {
            foreach ($excelRows as $row) {
                $row['four_ir_initiative_analysis_id'] = $fourIrInitiativeAnalysis->id;
                $row['accessor_type'] = $data['accessor_type'];
                $row['accessor_id'] = $data['accessor_id'];

                $fourIrInitiativeAnalysisResearchTeam = new FourIRInitiativeAnalysisResearchTeam();
                $fourIrInitiativeAnalysisResearchTeam->fill($row);
                $fourIrInitiativeAnalysisResearchTeam->save();
            }
        }

        return $fourIrInitiativeAnalysis;
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

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_SHOWCASING, ValidationException::withMessages([
                'Complete Showcasing step first.[24000]'
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
            'researcher_name' => [
                'required',
                'string'
            ],
            'researcher_name_en' => [
                'nullable',
                'string'
            ],
            'organization_name' => [
                'required',
                'string'
            ],
            'organization_name_en' => [
                'nullable',
                'string'
            ],
            'research_method' => [
                'nullable',
                'string'
            ],
            'file_path' => [
                'nullable',
                'string'
            ],
            'team_file' => [
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
            '*.organization_name' => [
                'required',
                'string'
            ],
            '*.organization_name_en' => [
                'nullable',
                'string'
            ],
            '*.designation' => [
                'required',
                'string'
            ],
            '*.email' => [
                'required',
                'email',
            ],
            '*.mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
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
            'researcher_name' => 'nullable|string',
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
