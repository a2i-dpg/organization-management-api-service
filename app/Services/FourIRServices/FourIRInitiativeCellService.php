<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiativeCell;
use App\Models\FourIRInitiativeTeamMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class FourIRInitiativeCellService
 * @package App\Services
 */
class FourIRInitiativeCellService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrInitiativeCellList(array $request, Carbon $startTime): array
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";
        $name = $request['name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrInitiativeCellBuilder */
        $fourIrInitiativeCellBuilder = FourIRInitiativeCell::select(
            [
                'four_ir_initiative_cells.id',
                'four_ir_initiative_cells.name',
                'four_ir_initiative_cells.address',
                'four_ir_initiative_cells.email',
                'four_ir_initiative_cells.phone_code',
                'four_ir_initiative_cells.mobile_number',
                'four_ir_initiative_cells.designation',
                'four_ir_initiative_cells.row_status',
                'four_ir_initiative_cells.created_by',
                'four_ir_initiative_cells.updated_by',
                'four_ir_initiative_cells.created_at',
                'four_ir_initiative_cells.updated_at'
            ]
        )->acl();

        $fourIrInitiativeCellBuilder->orderBy('four_ir_initiative_cells.id', $order);

        if (!empty($fourIrInitiativeId)) {
            $fourIrInitiativeCellBuilder->where('four_ir_initiative_cells.four_ir_initiative_id', 'like', '%' . $fourIrInitiativeId . '%');
        }
        if (!empty($name)) {
            $fourIrInitiativeCellBuilder->where('four_ir_initiative_cells.name', 'like', '%' . $name . '%');
        }
        if (is_numeric($rowStatus)) {
            $fourIrInitiativeCellBuilder->where('four_ir_initiative_cells.row_status', $rowStatus);
        }

        /** @var Collection $fourIrInitiatives */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrInitiatives = $fourIrInitiativeCellBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrInitiatives->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrInitiatives = $fourIrInitiativeCellBuilder->get();
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
     * @return FourIRInitiativeCell
     */
    public function getOneFourIrInitiativeCell(int $id): FourIRInitiativeCell
    {
        /** @var FourIRInitiativeCell|Builder $fourIrInitiativeCellBuilder */
        $fourIrInitiativeCellBuilder = FourIRInitiativeCell::select(
            [
                'four_ir_initiative_cells.id',
                'four_ir_initiative_cells.name',
                'four_ir_initiative_cells.address',
                'four_ir_initiative_cells.email',
                'four_ir_initiative_cells.phone_code',
                'four_ir_initiative_cells.mobile_number',
                'four_ir_initiative_cells.designation',
                'four_ir_initiative_cells.row_status',
                'four_ir_initiative_cells.created_by',
                'four_ir_initiative_cells.updated_by',
                'four_ir_initiative_cells.created_at',
                'four_ir_initiative_cells.updated_at'
            ]
        );
        $fourIrInitiativeCellBuilder->where('four_ir_initiative_cells.id', '=', $id);

        return $fourIrInitiativeCellBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRInitiativeCell
     */
    public function store(array $data): FourIRInitiativeCell
    {
        $fourIrInitiativeCell = new FourIRInitiativeCell();
        $fourIrInitiativeCell->fill($data);
        $fourIrInitiativeCell->save();
        return $fourIrInitiativeCell;
    }

    /**
     * @param FourIRInitiativeCell $fourIrInitiativeCell
     * @param array $data
     * @return FourIRInitiativeCell
     */
    public function update(FourIRInitiativeCell $fourIrInitiativeCell, array $data): FourIRInitiativeCell
    {
        $fourIrInitiativeCell->fill($data);
        $fourIrInitiativeCell->save();
        return $fourIrInitiativeCell;
    }

    /**
     * @param FourIRInitiativeCell $fourIrInitiativeCell
     * @return bool
     */
    public function destroy(FourIRInitiativeCell $fourIrInitiativeCell): bool
    {
        return $fourIrInitiativeCell->delete();
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
                    $mentoringTeam = FourIRInitiativeTeamMember::where('four_ir_initiative_id', $request->input('four_ir_initiative_id'))
                        ->where('team_type', FourIRInitiativeTeamMember::EXPERT_TEAM_TYPE)
                        ->first();
                        if(empty($mentoringTeam)){
                            $failed('Complete Mentoring step first.[24000]');
                        }
                        },
                'exists:four_ir_initiatives,id,deleted_at,NULL',
            ],
            'name' => [
                'required',
                'string'
            ],
            'address' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'email',
                'max: 320'
            ],
            'phone_code' => [
                'nullable',
                'string',
                'max: 3',
                'min:2'
            ],
            'mobile_number' => [
                'required',
                'string',
                'max: 20',
                BaseModel::MOBILE_REGEX,
            ],
            'designation' => [
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
            'name' => 'nullable|int',
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
