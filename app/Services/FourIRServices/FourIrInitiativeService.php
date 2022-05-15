<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRFileLog;
use App\Models\FourIRInitiative;
use App\Models\FourIROccupation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class RankService
 * @package App\Services
 */
class FourIrInitiativeService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrInitiativeList(array $request, Carbon $startTime): array
    {
        $initiativeName = $request['name'] ?? "";
        $organizationName = $request['organization_name'] ?? "";
        $startDate = $request['start_date'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrInitiativeBuilder */
        $fourIrInitiativeBuilder = FourIRInitiative::select(
            [
                'four_ir_initiatives.id',
                'four_ir_initiatives.four_ir_tagline_id',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.implementing_team_launching_date',
                'four_ir_initiatives.expert_team_launching_date',
                'four_ir_initiatives.name',
                'four_ir_initiatives.name_en',
                'four_ir_initiatives.organization_name',
                'four_ir_initiatives.organization_name_en',
                'four_ir_initiatives.budget',
                'four_ir_initiatives.designation',
                'four_ir_initiatives.four_ir_occupation_id',
                'four_ir_occupations.title as occupation_title',
                'four_ir_occupations.title_en as occupation_title_en',
                'four_ir_initiatives.start_date',
                'four_ir_initiatives.end_date',
                'four_ir_initiatives.file_path',
                'four_ir_initiatives.tasks',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',
                'four_ir_initiatives.initiative_code',
                'four_ir_initiatives.accessor_type',
                'four_ir_initiatives.accessor_id',
                'four_ir_initiatives.row_status',
                'four_ir_initiatives.created_by',
                'four_ir_initiatives.updated_by',
                'four_ir_initiatives.created_at',
                'four_ir_initiatives.updated_at'
            ]
        )->acl();

        $fourIrInitiativeBuilder->join('four_ir_occupations', 'four_ir_occupations.id', '=', 'four_ir_initiatives.four_ir_occupation_id');

        if (!empty($initiativeName)) {
            $fourIrInitiativeBuilder->where(function ($builder) use ($initiativeName) {
                $builder->where('four_ir_initiatives.name', 'like', '%' . $initiativeName . '%');
                $builder->orWhere('four_ir_initiatives.name_en', 'like', '%' . $initiativeName . '%');
            });
        }

        if (!empty($organizationName)) {
            $fourIrInitiativeBuilder->where(function ($builder) use ($organizationName) {
                $builder->where('four_ir_initiatives.organization_name', 'like', '%' . $organizationName . '%');
                $builder->orWhere('four_ir_initiatives.organization_name_en', 'like', '%' . $organizationName . '%');
            });
        }

        if (!empty($startDate)) {
            $fourIrInitiativeBuilder->whereDate('four_ir_initiatives.start_date', $startDate);
        }

        $fourIrInitiativeBuilder->orderBy('four_ir_initiatives.id', $order);

        if (is_numeric($rowStatus)) {
            $fourIrInitiativeBuilder->where('four_ir_initiatives.row_status', $rowStatus);
        }

        /** @var Collection $fourIrInitiatives */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrInitiatives = $fourIrInitiativeBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrInitiatives->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrInitiatives = $fourIrInitiativeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrInitiatives->toArray()['data'] ?? $fourIrInitiatives->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRInitiative
     */
    public function getOneFourIrInitiative(int $id): FourIRInitiative
    {
        /** @var FourIRInitiative|Builder $fourIrInitiativeBuilder */
        $fourIrInitiativeBuilder = FourIRInitiative::select(
            [
                'four_ir_initiatives.id',
                'four_ir_initiatives.four_ir_tagline_id',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.implementing_team_launching_date',
                'four_ir_initiatives.expert_team_launching_date',
                'four_ir_initiatives.name',
                'four_ir_initiatives.name_en',
                'four_ir_initiatives.organization_name',
                'four_ir_initiatives.organization_name_en',
                'four_ir_initiatives.budget',
                'four_ir_initiatives.designation',
                'four_ir_initiatives.four_ir_occupation_id',
                'four_ir_occupations.title as occupation_title',
                'four_ir_occupations.title_en as occupation_title_en',
                'four_ir_initiatives.start_date',
                'four_ir_initiatives.end_date',
                'four_ir_initiatives.details',
                'four_ir_initiatives.file_path',
                'four_ir_initiatives.tasks',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',
                'four_ir_initiatives.initiative_code',
                'four_ir_initiatives.accessor_type',
                'four_ir_initiatives.accessor_id',
                'four_ir_initiatives.row_status',
                'four_ir_initiatives.created_by',
                'four_ir_initiatives.updated_by',
                'four_ir_initiatives.created_at',
                'four_ir_initiatives.updated_at'
            ]
        );
        $fourIrInitiativeBuilder->where('four_ir_initiatives.id', '=', $id);

        $fourIrInitiativeBuilder->join('four_ir_occupations', 'four_ir_occupations.id', '=', 'four_ir_initiatives.four_ir_occupation_id');

        return $fourIrInitiativeBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRInitiative
     */
    public function store(array $data): FourIRInitiative
    {
        $data['initiative_code'] = Uuid::uuid4()->toString();
        $fourIrInitiative = new FourIRInitiative();
        $fourIrInitiative->fill($data);
        $fourIrInitiative->save();
        return $fourIrInitiative;
    }

    /**
     * @param FourIRInitiative $fourIrInitiative
     * @param array $data
     * @return FourIRInitiative
     */
    public function update(FourIRInitiative $fourIrInitiative, array $data): FourIRInitiative
    {

        if (sizeof($data['tasks']) == 3) {
            $data['completion_step'] = FourIRInitiative::COMPLETION_STEP_ONE;
            $data['form_step'] = FourIRInitiative::FORM_STEP_PROJECT_INITIATION;
            $fourIrInitiative->fill($data);
            $fourIrInitiative->save();
        } else {
            $fourIrInitiative->fill($data);
            $fourIrInitiative->save();
        }
        return  $fourIrInitiative;
    }
    /**
     * @param FourIRInitiative $fourIrInitiative
     * @return bool
     */
    public function destroy(FourIRInitiative $fourIrInitiative): bool
    {
        return $fourIrInitiative->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        if (!empty($data['tasks'])) {
            $data["tasks"] = isset($data['tasks']) && is_array($data['tasks']) ? $data['tasks'] : explode(',', $data['tasks']);
        }
        $rules = [
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'four_ir_tagline_id' => [
                'required',
                'int',
                'exists:four_ir_taglines,id,deleted_at,NULL'
            ],
            'name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'organization_name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'organization_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'budget' => [
                'required',
                'numeric'
            ],
            'designation' => [
                'required',
                'string',
                'max:300'
            ],
            'four_ir_occupation_id' => [
                'required',
                'int',
                'exists:four_ir_occupations,id,deleted_at,NULL'
            ],
            'start_date' => [
                'required',
                'date_format:Y-m-d'
            ],
            'end_date' => [
                'required',
                'date_format:Y-m-d',
                'after:start_date'
            ],
            'details' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'file_path' => [
                'nullable',
                'string',
                'max:300',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($data, $rules, $customMessage);
    }

    public function TaskAndSkillvalidator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        if (!empty($data['tasks'])) {
            $data["tasks"] = isset($data['tasks']) && is_array($data['tasks']) ? $data['tasks'] : explode(',', $data['tasks']);
        }
        $rules = [
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'is_skill_provide' => [
                'nullable',
                'int',
                Rule::in(FourIRInitiative::SKILL_PROVIDE_FALSE, FourIRInitiative::SKILL_PROVIDE_TRUE)
            ],
            'tasks' => [
                'nullable',
                'array'
            ],
            'tasks.*' => [
                'required',
                'int'
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
            'four_ir_tagline_id' => 'required|int',
            'name' => 'nullable|max:600|min:2',
            'organization_name' => 'nullable|max:600|min:2',
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function excelImportValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $rules = [
            'file' => 'required|mimes:xlsx, csv, xls',
            'four_ir_tagline_id' => [
                'required',
                'int',
                'exists:four_ir_taglines,id,deleted_at,NULL'
            ],
        ];
        return Validator::make($data, $rules);
    }

    /**
     * @param Request $request
     * @param array $excelData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function excelDataValidator(Request $request, array $excelData): \Illuminate\Contracts\Validation\Validator
    {
        /** $excelData owns an array. So use * as prefix */
        $rules = [
            '*.name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            '*.name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            '*.is_skill_provide' => [
                'required',
                'int',
                Rule::in(FourIRInitiative::SKILL_PROVIDE_FALSE, FourIRInitiative::SKILL_PROVIDE_TRUE)
            ],
            '*.organization_name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            '*.organization_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            '*.budget' => [
                'required',
                'numeric'
            ],
            '*.designation' => [
                'required',
                'string',
                'max:300'
            ],
            '*.four_ir_occupation_id' => [
                'required',
                'int',
                'exists:four_ir_occupations,id,deleted_at,NULL',
                Rule::unique('four_ir_initiatives', 'four_ir_occupation_id')
                    ->where(function (\Illuminate\Database\Query\Builder $query) use ($request) {
                        return $query->where('four_ir_tagline_id', $request->input('four_ir_tagline_id'))
                            ->whereNull('deleted_at');
                    }),
                'distinct'
            ],
            '*.start_date' => [
                'required',
                'date_format:Y-m-d'
            ],
            '*.end_date' => [
                'required',
                'date_format:Y-m-d',
                'after:start_date'
            ],
            '*.details' => [
                'nullable',
                'string',
                'max:1000'
            ],
            '*.file_path' => [
                'nullable',
                'string',
                'max:300',
            ],
            '*.task' => [
                'required',
                'array'
            ],
            '*.task.*' => [
                'required',
                'int'
            ],
            '*.row_status' => [
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];
        return Validator::make($excelData, $rules);
    }

    /**
     * @throws Exception
     */
    public function getBulkImporterExcelFormat(): string
    {
        $fourIrOccupationColumnCoordinate = "H1";
        $fourIrTaskColumnCoordinate = "M1";
        $fourIrSkillProvidedCoordinate = "E1";

        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1", "Name");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B1", "Name En");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C1", "Organization Name");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D1", "Organization Name En");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($fourIrSkillProvidedCoordinate, "Is Skill Provide");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("F1", "Budget");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G1", "Designation");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($fourIrOccupationColumnCoordinate, "Four Ir Occupation Id");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("I1", "Start Date");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("J1", "End Date");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("K1", "Details");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("L1", "File Path");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($fourIrTaskColumnCoordinate, "Task");

        $fourIrOccupations = "";
        foreach (FourIROccupation::all() as $value) {
            $fourIrOccupations .= $value->id . " | " . $value->title . ",";
        }

        $fourIrTasks = "";
        foreach (FourIRInitiative::TASKS as $key => $task) {
            $fourIrTasks .= $key . ' | ' . $task . ",";
        }

        $trueFalse = "1 | TRUE, 0 | FALSE";

        $this->dropDownColumnBuilder($objPHPExcel, $fourIrOccupationColumnCoordinate, $fourIrOccupations);
        $this->dropDownColumnBuilder($objPHPExcel, $fourIrTaskColumnCoordinate, $fourIrTasks);
        $this->dropDownColumnBuilder($objPHPExcel, $fourIrSkillProvidedCoordinate, $trueFalse);

        $writer = new Xlsx($objPHPExcel);
        ob_start();
        $writer->save('php://output');
        $excelData = ob_get_contents();
        ob_end_clean();
        return "data:application/vnd.ms-excel;base64," . base64_encode($excelData);
    }

    /**
     * @throws Exception
     */
    private function dropDownColumnBuilder(Spreadsheet $objPHPExcel, string $column, string $dropdownData): void
    {
        $objValidation = $objPHPExcel->setActiveSheetIndex(0)->getCell($column)->getDataValidation();
        $objValidation->setType(DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setPromptTitle('Pick from list');
        $objValidation->setPrompt('Please pick a value from the drop-down list.');
        $objValidation->setErrorTitle('Input error');
        $objValidation->setError('Value is not in list');
        $objValidation->setFormula1('"' . $dropdownData . '"');
    }

    public function explodeData(array &$data): void
    {
        foreach ($data as $mainKey => $value) {
            foreach ($value as $subKey => $subValue) {
                if (!is_array($subValue)) {
                    $explode = explode('|', $subValue);
                    if (sizeof($explode) == 2 && !empty($explode[0])) {
                        $explodedValue = trim($explode[0]);
                        if (is_numeric($explodedValue)) {
                            $explodedValue = (int)$explodedValue;
                        }
                        $data[$mainKey][$subKey] = $explodedValue;
                    }
                }

            }

        }
    }
}
