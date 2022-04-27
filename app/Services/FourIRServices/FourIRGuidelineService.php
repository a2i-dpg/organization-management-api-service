<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRGuideline;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class FourIRGuidelineService
 * @package App\Services\FourIRProjectServices
 */
class FourIRGuidelineService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIRGuidelineList(array $request, Carbon $startTime): array
    {
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrGuidelineBuilder */
        $fourIrGuidelineBuilder = FourIRGuideline::select(
            [
                'four_ir_guidelines.id',
                'four_ir_guidelines.file_path',
                'four_ir_guidelines.guideline_details',
                'four_ir_guidelines.row_status',
                'four_ir_guidelines.created_by',
                'four_ir_guidelines.updated_by',
                'four_ir_guidelines.created_at',
                'four_ir_guidelines.updated_at',
            ]
        )->acl();

        $fourIrGuidelineBuilder->orderBy('four_ir_guidelines.id', $order);

        if (is_numeric($rowStatus)) {
            $fourIrGuidelineBuilder->where('four_ir_guidelines.row_status', $rowStatus);
        }

        /** @var Collection $fourIrTaglines */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrTaglines = $fourIrGuidelineBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrTaglines->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrTaglines = $fourIrGuidelineBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrTaglines->toArray()['data'] ?? $fourIrTaglines->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

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
                'four_ir_guidelines.file_path',
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
        $fourIrGuideline = new FourIRGuideline();
        $fourIrGuideline->fill($data);
        $fourIrGuideline->save();
        return $fourIrGuideline;
    }

    /**
     * @param FourIRGuideline $fourIrGuideline
     * @param array $data
     * @return FourIRGuideline
     */
    public function update(FourIRGuideline $fourIrGuideline, array $data): FourIRGuideline
    {
        $fourIrGuideline->fill($data);
        $fourIrGuideline->save();
        return $fourIrGuideline;
    }

    /**
     * @param FourIRGuideline $fourIrGuideline
     * @return bool
     */
    public function destroy(FourIRGuideline $fourIrGuideline): bool
    {
        return $fourIrGuideline->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]',
            'file_path.required' => 'At least file path or details should be filled up. [50000]',
            'guideline_details.required' => 'At least file path or details should be filled up. [50000]',
        ];
        $rules = [
            'file_path' => [
                Rule::requiredIf(function () use ($request) {
                    return empty($request->input('guideline_details'));
                }),
                'nullable',
                'string'
            ],
            'guideline_details' => [
                Rule::requiredIf(function () use($request) {
                    return empty($request->input('file_path'));
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
            'name' => 'nullable|max:600|min:2',
            'name_en' => 'nullable|max:300|min:2',
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
