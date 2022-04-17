<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRGuideline;
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
     * @param array $data
     * @return FourIRGuideline
     */
    public function store(array $data): FourIRGuideline
    {
        $guideline = FourIRGuideline::firstOrNew($data);
        $guideline->save();
        return $guideline;
    }

    /**
     * @param FourIRGuideline $guideline
     * @param array $data
     * @return FourIRGuideline
     */
    public function update(FourIRGuideline $guideline, array $data): FourIRGuideline
    {
        $guideline->fill($data);
        $guideline->save();
        return $guideline;
    }

    /**
     * @param FourIRGuideline $guideline
     * @return bool
     */
    public function destroy(FourIRGuideline $guideline): bool
    {
        return $guideline->delete();
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
