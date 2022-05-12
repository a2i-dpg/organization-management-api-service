<?php


namespace App\Services\FourIRServices;

use App\Imports\FourIrTnaMethodsImport;
use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeTnaFormat;
use App\Models\FourIRTnaFormatMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


/**
 * Class FourIRInitiativeTnaFormatService
 * @package App\Services
 */
class FourIRInitiativeTnaFormatService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectTnaFormatList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_initiative_id'];
        $order = $request['order'] ?? "ASC";

        /** @var Builder $FourIRProjectTnaFormatBuilder */
        $fourIrProjectTnaFormatBuilder = FourIRInitiativeTnaFormat::select(
            [
                'four_ir_initiative_tna_formats.id',
                'four_ir_initiative_tna_formats.four_ir_initiative_id',
                'four_ir_initiative_tna_formats.method_type',
                'four_ir_initiative_tna_formats.workshop_numbers',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.tna_file_path',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_initiative_tna_formats.row_status',
                'four_ir_initiative_tna_formats.created_by',
                'four_ir_initiative_tna_formats.updated_by',
                'four_ir_initiative_tna_formats.created_at',
                'four_ir_initiative_tna_formats.updated_at'
            ]
        );

        $fourIrProjectTnaFormatBuilder->orderBy('four_ir_initiative_tna_formats.id', $order);

        $fourIrProjectTnaFormatBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_tna_formats.four_ir_initiative_id');

        if (!empty($fourIrProjectId)) {
            $fourIrProjectTnaFormatBuilder->where('four_ir_initiative_tna_formats.four_ir_initiative_id', 'like', '%' . $fourIrProjectId . '%');
        }

        /** @var Collection $fourIrProjects */
        $fourIrProjects = $fourIrProjectTnaFormatBuilder->get();

        $response['order'] = $order;
        $response['data'] = $fourIrProjects->toArray()['data'] ?? $fourIrProjects->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];
        return $response;
    }

    /**
     * @param FourIRInitiative $fourIrInitiative
     * @param array $data
     * @return void
     */
    public function store(FourIRInitiative $fourIrInitiative, array $data)
    {
        $payload = [];
        if (!empty($data['file_path'])) {
            $payload['tna_file_path'] = $data['file_path'];
        }

        if ($fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_TNA) {
            $payload['form_step'] = FourIRInitiative::FORM_STEP_TNA;
        }

        if ($fourIrInitiative->completion_step < FourIRInitiative::COMPLETION_STEP_THREE) {
            $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_THREE;
        }

        $fourIrInitiative->fill($payload);
        $fourIrInitiative->save();
    }

    /**
     * @param $file
     * @param int $tnaMethod
     * @return array|null
     * @throws ValidationException
     */
    public function excelDataValidate($file, int $tnaMethod): null|array
    {
        $excelData = Excel::toCollection(new FourIrTnaMethodsImport(), $file)->toArray();

        $rows = null;
        if (!empty($excelData) && !empty($excelData[0])) {
            $rows = $excelData[0];
            $this->excelDataValidator($rows, $tnaMethod)->validate();
        }
        return $rows;
    }

    /**
     * @param array $data
     * @param array|null $rows
     * @param int $tnaMethod
     * @return void
     */
    public function tnaFormatMethodStore(array $data, array|null $rows, int $tnaMethod): void
    {
        /** First, Create TNA format */
        $tnaFormat = new FourIRInitiativeTnaFormat();
        $payload = [
            "four_ir_initiative_id" => $data['four_ir_initiative_id'],
            "accessor_type" => $data['accessor_type'],
            "accessor_id" => $data['accessor_id'],
            "row_status" => $data['row_status'] ?? BaseModel::ROW_STATUS_ACTIVE
        ];

        if (!empty($tnaMethod)) {
            $payload["method_type"] = $tnaMethod;
        }
        if (!empty($data[FourIRInitiativeTnaFormat::TNA_METHODS_WORKSHOP_NUMBER_KEYS[$tnaMethod]])) {
            $payload["workshop_numbers"] = $data[FourIRInitiativeTnaFormat::TNA_METHODS_WORKSHOP_NUMBER_KEYS[$tnaMethod]];
        }

        FourIRInitiativeTnaFormat::updateOrCreate([
            "four_ir_initiative_id" => $data['four_ir_initiative_id'],
            "method_type" => $tnaMethod
        ],
            $payload
        );

        if (!empty($rows)) {
            /** Now create TNA format methods from Excel rows */
            foreach ($rows as $rowData) {
                DB::beginTransaction();
                try {
                    $rowData['four_ir_initiative_tna_format_id'] = $tnaFormat->id;
                    $fourIRTnaFormatMethod = new FourIRTnaFormatMethod();
                    $fourIRTnaFormatMethod->fill($rowData);
                    $fourIRTnaFormatMethod->save();
                    DB::commit();
                } catch (Throwable $e) {
                    Log::info("Error occurred. Inside catch block. Error is: " . json_encode($e->getMessage()));
                    DB::rollBack();
                }
            }
        }

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

        if (!empty($data['four_ir_initiative_id'])) {
            $fourIrInitiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);
            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
                "This form step is not allowed as the initiative was set for Not Skill Provider!"
            ]));
            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_EXPERT_TEAM, ValidationException::withMessages([
                'Complete Expert team step first.[24000]'
            ]));
        }

        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'four_ir_initiative_id' => [
                'required',
                'int',
                'exists:four_ir_initiatives,id,deleted_at,NULL'
            ],
            'workshop_method_workshop_numbers' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['workshop_method_file']) && empty($data['operation_type']);
                }),
                'nullable',
                'int'
            ],
            'workshop_method_file' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['workshop_method_workshop_numbers']) && empty($data['operation_type']);
                }),
                'nullable',
                'mimes:xlsx, csv, xls'
            ],

            'fgd_workshop_numbers' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['fgd_workshop_file']) && empty($data['operation_type']);
                }),
                'nullable',
                'int'
            ],
            'fgd_workshop_file' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['fgd_workshop_numbers']) && empty($data['operation_type']);
                }),
                'nullable',
                'mimes:xlsx, csv, xls'
            ],

            'industry_visit_workshop_numbers' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['industry_visit_file']) && empty($data['operation_type']);
                }),
                'nullable',
                'int'
            ],
            'industry_visit_file' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['industry_visit_workshop_numbers']) && empty($data['operation_type']);
                }),
                'nullable',
                'mimes:xlsx, csv, xls'
            ],

            'desktop_research_workshop_numbers' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['desktop_research_file']) && empty($data['operation_type']);
                }),
                'nullable',
                'int'
            ],
            'desktop_research_file' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['desktop_research_workshop_numbers']) && empty($data['operation_type']);
                }),
                'nullable',
                'mimes:xlsx, csv, xls'
            ],

            'existing_report_review_workshop_numbers' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['existing_report_review_file']) && empty($data['operation_type']);
                }),
                'nullable',
                'int'
            ],
            'existing_report_review_file' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['existing_report_review_workshop_numbers']) && empty($data['operation_type']);
                }),
                'nullable',
                'mimes:xlsx, csv, xls'
            ],

            'others_workshop_numbers' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['others_file']) && empty($data['operation_type']);
                }),
                'nullable',
                'int'
            ],
            'others_file' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['others_workshop_numbers']) && empty($data['operation_type']);
                }),
                'nullable',
                'mimes:xlsx, csv, xls'
            ],

            'file_path' => [
                'nullable',
                'string'
            ],
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
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
        return Validator::make($request->all(), [
            'four_ir_initiative_id' => 'required|int'
        ]);
    }

    /**
     * @param array $excelData
     * @param int $tnaMethod
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function excelDataValidator(array $excelData, int $tnaMethod): \Illuminate\Contracts\Validation\Validator
    {
        $fileKeyName = FourIRInitiativeTnaFormat::TNA_METHODS_FILE_KEYS[$tnaMethod];
        $customMessage = [
            'required' => 'This field of ' . $fileKeyName . ' is required.[50000]',
            'string' => 'This field of ' . $fileKeyName . ' must be a string.[60000]',
            'date_format' => 'This field of ' . $fileKeyName . ' does not match the format Y-m-d.[16000]',
            'after' => 'This field of ' . $fileKeyName . ' must after its start_date.[3000]',
        ];

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
            '*.start_date' => [
                'required',
                'date-format:Y-m-d'
            ],
            '*.end_date' => [
                'required',
                'date-format:Y-m-d',
                'after:*.start_date'
            ],
            '*.venue' => [
                'required',
                'string',
            ],
        ];
        return Validator::make($excelData, $rules, $customMessage);
    }
}
