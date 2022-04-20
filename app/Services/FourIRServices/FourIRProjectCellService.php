<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRProject;
use App\Models\FourIRProjectCell;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class RankService
 * @package App\Services
 */
class FourIrProjectCellService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectCellList(array $request, Carbon $startTime): array
    {
        $projectName = $request['project_name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectCellBuilder */
        $fourIrProjectCellBuilder = FourIRProjectCell::select(
            [
                'four_ir_project_cells.id',
                'four_ir_project_cells.name',
                'four_ir_project_cells.address',
                'four_ir_project_cells.email',
                'four_ir_project_cells.phone_code',
                'four_ir_project_cells.mobile_number',
                'four_ir_project_cells.designation',
                'four_ir_project_cells.row_status',
                'four_ir_project_cells.created_by',
                'four_ir_project_cells.updated_by',
                'four_ir_project_cells.created_at',
                'four_ir_project_cells.updated_at'
            ]
        )->acl();

        $fourIrProjectCellBuilder->orderBy('four_ir_project_cells.id', $order);

        if (!empty($projectName)) {
            $fourIrProjectCellBuilder->where('four_ir_project_cells.project_name', 'like', '%' . $projectName . '%');
        }
        if (is_numeric($rowStatus)) {
            $fourIrProjectCellBuilder->where('four_ir_project_cells.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjects */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjects = $fourIrProjectCellBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjects->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjects = $fourIrProjectCellBuilder->get();
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
     * @return FourIRProjectCell
     */
    public function getOneFourIrProjectCell(int $id): FourIRProjectCell
    {
        /** @var FourIRProjectCell|Builder $fourIrProjectCellBuilder */
        $fourIrProjectCellBuilder = FourIRProjectCell::select(
            [
                'four_ir_project_cells.id',
                'four_ir_project_cells.name',
                'four_ir_project_cells.address',
                'four_ir_project_cells.email',
                'four_ir_project_cells.phone_code',
                'four_ir_project_cells.mobile_number',
                'four_ir_project_cells.designation',
                'four_ir_project_cells.row_status',
                'four_ir_project_cells.created_by',
                'four_ir_project_cells.updated_by',
                'four_ir_project_cells.created_at',
                'four_ir_project_cells.updated_at'
            ]
        );
        $fourIrProjectCellBuilder->where('four_ir_project_cells.id', '=', $id);

        return $fourIrProjectCellBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRProjectCell
     */
    public function store(array $data): FourIRProjectCell
    {

        $fourIrProjectCell = new FourIRProjectCell();
        $fourIrProjectCell->fill($data);
        $fourIrProjectCell->save();
        return $fourIrProjectCell;
    }

    /**
     * @param FourIRProjectCell $fourIrProjectCell
     * @param array $data
     * @return FourIRProjectCell
     */
    public function update(FourIRProjectCell $fourIrProjectCell, array $data): FourIRProjectCell
    {
        $fourIrProjectCell->fill($data);
        $fourIrProjectCell->save();
        return $fourIrProjectCell;
    }

    /**
     * @param FourIRProjectCell $fourIrProjectCell
     * @return bool
     */
    public function destroy(FourIRProjectCell $fourIrProjectCell): bool
    {
        return $fourIrProjectCell->delete();
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
            'four_ir_project_id'=>[
                'required',
                'int'
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
            'project_name' => 'nullable|max:600|min:2',
            'project_name_en' => 'nullable|max:300|min:2',
            'organization_name' => 'nullable|max:600|min:2',
            'organization_name_en' => 'nullable|max:300|min:2',
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
