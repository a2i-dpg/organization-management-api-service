<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRJobPlacementStatus;
use App\Models\FourIRShowcasing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class FourIRShowcasingService
 * @package App\Services
 */
class FourIRShowcasingService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourShowcasingList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_initiative_id'] ?? "";
        $occupationName = $request['occupation_name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectCellBuilder */
        $fourIrShowcasingBuilder = FourIRShowcasing::select(
            [
                'four_ir_showcasings.id',
                'four_ir_showcasings.occupation_name',
                'four_ir_showcasings.occupation_name_en',
                'four_ir_showcasings.date',
                'four_ir_showcasings.start_time',
                'four_ir_showcasings.end_time',
                'four_ir_showcasings.venue',
                'four_ir_showcasings.venue_en',
                'four_ir_showcasings.invite_others',
                'four_ir_showcasings.row_status',
                'four_ir_showcasings.created_by',
                'four_ir_showcasings.updated_by',
                'four_ir_showcasings.created_at',
                'four_ir_showcasings.updated_at'
            ]
        )->acl();

        $fourIrShowcasingBuilder->orderBy('four_ir_showcasings.id', $order);

        if (!empty($fourIrProjectId)) {
            $fourIrShowcasingBuilder->where('four_ir_showcasings.four_ir_initiative_id', 'like', '%' . $fourIrProjectId . '%');
        }
        if (!empty($occupationName)) {
            $fourIrShowcasingBuilder->where('four_ir_showcasings.occupation_name', 'like', '%' . $occupationName . '%');
        }
        if (is_numeric($rowStatus)) {
            $fourIrShowcasingBuilder->where('four_ir_showcasings.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjects */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjects = $fourIrShowcasingBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjects->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjects = $fourIrShowcasingBuilder->get();
        }

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
     * @param int $id
     * @return FourIRShowcasing
     */
    public function getOneFourIrShowcasing(int $id): FourIRShowcasing
    {
        /** @var FourIRShowcasing|Builder $fourIrShowcasingBuilder */
        $fourIrShowcasingBuilder = FourIRShowcasing::select(
            [
                'four_ir_showcasings.id',
                'four_ir_showcasings.occupation_name',
                'four_ir_showcasings.occupation_name_en',
                'four_ir_showcasings.date',
                'four_ir_showcasings.start_time',
                'four_ir_showcasings.end_time',
                'four_ir_showcasings.venue',
                'four_ir_showcasings.venue_en',
                'four_ir_showcasings.invite_others',
                'four_ir_showcasings.row_status',
                'four_ir_showcasings.created_by',
                'four_ir_showcasings.updated_by',
                'four_ir_showcasings.created_at',
                'four_ir_showcasings.updated_at'
            ]
        );
        $fourIrShowcasingBuilder->where('four_ir_showcasings.id', '=', $id);

        return $fourIrShowcasingBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRShowcasing
     */
    public function store(array $data): FourIRShowcasing
    {

        $fourIrShowcasing = new FourIRShowcasing();
        $fourIrShowcasing->fill($data);
        $fourIrShowcasing->save();
        return $fourIrShowcasing;
    }

    /**
     * @param FourIRShowcasing $fourIrShowcasing
     * @param array $data
     * @return FourIRShowcasing
     */
    public function update(FourIRShowcasing $fourIrShowcasing, array $data): FourIRShowcasing
    {
        $fourIrShowcasing->fill($data);
        $fourIrShowcasing->save();
        return $fourIrShowcasing;
    }

    /**
     * @param FourIRShowcasing $fourIrShowcasing
     * @return bool
     */
    public function destroy(FourIRShowcasing $fourIrShowcasing): bool
    {
        return $fourIrShowcasing->delete();
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
            'four_ir_initiative_id'=>[
                'required',
                'int',
                function ($attr, $value, $failed) use ($request) {
                    $mentoringTeam = FourIRJobPlacementStatus::where('four_ir_initiative_id', $request->input('four_ir_initiative_id'))
                        ->first();
                    if(empty($mentoringTeam)){
                        $failed('Complete Mentoring step first.[24000]');
                    }
                },
                'exists:four_ir_initiatives,id,deleted_at,NULL',
            ],
            'occupation_name' => [
                'required',
                'string'
            ],
            'occupation_name_en' => [
                'nullable',
                'string'
            ],
            'date' => [
                'required',
                'date_format:Y-m-d'
            ],
            'start_time' => [
                'nullable',
                'date_format:H:i:s'
            ],
            'end_time' => [
                'nullable',
                'date_format:H:i:s'
            ],
            'venue' => [
                'required',
                'string'
            ],
            'venue_en' => [
                'nullable',
                'string'
            ],
            'invite_others' => [
                'required',
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
