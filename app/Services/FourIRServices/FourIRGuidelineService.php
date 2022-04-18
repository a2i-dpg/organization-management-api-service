<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRGuideline;
use App\Models\FourIRProject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


/**
 * Class FourIRGuidelineService
 * @package App\Services\FourIRProjectServices
 */
class FourIRGuidelineService
{

    /**
     * @param int $id
     * @return FourIRGuideline
     */
    public function getOneGuideline(int $id): FourIRGuideline
    {
        /** @var FourIRGuideline|Builder $fourIrGuidelineBuilder */
        $fourIrGuidelineBuilder = FourIRGuideline::select(
            [
                'four_ir_guidelines.id',
                'four_ir_guidelines.four_ir_project_id',
                'four_ir_guidelines.guideline_file_path',
                'four_ir_guidelines.guideline_details',
                'four_ir_guidelines.row_status',
                'four_ir_guidelines.created_by',
                'four_ir_guidelines.updated_by',
                'four_ir_guidelines.created_at',
                'four_ir_guidelines.updated_at',
            ]
        );

        $fourIrGuidelineBuilder->where('four_ir_guidelines.id', '=', $id);

        return $fourIrGuidelineBuilder->firstOrFail();
    }


    /**
     * @param array $data
     * @return FourIRGuideline
     */
    public function store(array $data): FourIRGuideline
    {
        return FourIRGuideline::updateOrCreate($data);
    }


    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'four_ir_project_id' => [
                'integer',
                'exists:four_ir_projects,id,deleted_at,NULL',
            ],
            'guideline_file_path' => [
                'nullable',
                'string'
            ],
            'guideline_details' => [
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
        return Validator::make($request->all(), $rules, $customMessage);
    }
}
