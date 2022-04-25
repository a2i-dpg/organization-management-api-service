<?php


namespace App\Services\FourIRResourceService;

use App\Models\BaseModel;
use App\Models\FourIRResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


/**
 * Class FourIRResourceService
 * @package App\Services\FourIRResourceService
 */
class FourIRResourceService
{

    /**
     * @param int $id
     * @return FourIRResource
     */
    public function getOneResource(int $id): FourIRResource
    {
        /** @var FourIRResource|Builder $fourIrResourceBuilder */
        $fourIrResourceBuilder = FourIRResource::select(
            [
                'four_ir_resources.id',
                'four_ir_resources.four_ir_project_id',
                'four_ir_resources.file_path',
                'four_ir_resources.row_status',
                'four_ir_resources.created_by',
                'four_ir_resources.updated_by',
                'four_ir_resources.created_at',
                'four_ir_resources.updated_at',
            ]
        );

        $fourIrResourceBuilder->where('four_ir_resources.id', '=', $id);

        return $fourIrResourceBuilder->firstOrFail();
    }


    /**
     * @param array $data
     * @return FourIRResource
     */
    public function store(array $data): FourIRResource
    {
        return FourIRResource::updateOrCreate($data);
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
                'required',
                'integer',
                'exists:four_ir_projects,id,deleted_at,NULL',
            ],
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'file_path' => [
                Rule::requiredIf(function () use ($request) {
                    return empty($request->input('guideline_details'));
                }),
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
