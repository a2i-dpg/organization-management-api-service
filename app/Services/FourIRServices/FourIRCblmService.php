<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRCblm;
use App\Models\FourIRProjectCurriculum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


/**
 * Class FourIRCblmService
 * @package App\Services
 */
class FourIRCblmService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrCblmList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_project_id'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrCblmBuilder */
        $fourIrCblmBuilder = FourIRCblm::select(
            [
                'four_ir_cblm.id',
                'four_ir_cblm.four_ir_project_id',
                'four_ir_cblm.file_path',
                'four_ir_cblm.row_status',
                'four_ir_cblm.created_by',
                'four_ir_cblm.updated_by',
                'four_ir_cblm.created_at',
                'four_ir_cblm.updated_at'
            ]
        )->acl();

        $fourIrCblmBuilder->orderBy('four_ir_cblm.id', $order);

        if (!empty($fourIrProjectId)) {
            $fourIrCblmBuilder->where('four_ir_cblm.four_ir_project_id', $fourIrProjectId);
        }

        if (is_numeric($rowStatus)) {
            $fourIrCblmBuilder->where('four_ir_cblm.row_status', $rowStatus);
        }

        /** @var Collection $fourIrCblms */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrCblms = $fourIrCblmBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrCblms->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrCblms = $fourIrCblmBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrCblms->toArray()['data'] ?? $fourIrCblms->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRCblm
     */
    public function getOneFourIrCblm(int $id): FourIRCblm
    {
        /** @var FourIRCblm|Builder $fourIrCblmBuilder */
        $fourIrCblmBuilder = FourIRCblm::select(
            [
                'four_ir_cblm.id',
                'four_ir_cblm.four_ir_project_id',
                'four_ir_cblm.file_path',
                'four_ir_cblm.row_status',
                'four_ir_cblm.created_by',
                'four_ir_cblm.updated_by',
                'four_ir_cblm.created_at',
                'four_ir_cblm.updated_at'
            ]
        );
        $fourIrCblmBuilder->where('four_ir_cblm.id', '=', $id);

        return $fourIrCblmBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRCblm
     */
    public function store(array $data): FourIRCblm
    {
        $fourIrCblm = new FourIRCblm();
        $fourIrCblm->fill($data);
        $fourIrCblm->save();
        return $fourIrCblm;
    }

    /**
     * @param FourIRCblm $fourIrCblm
     * @param array $data
     * @return FourIRCblm
     */
    public function update(FourIRCblm $fourIrCblm, array $data): FourIRCblm
    {
        $fourIrCblm->fill($data);
        $fourIrCblm->save();
        return $fourIrCblm;
    }

    /**
     * @param FourIRCblm $fourIrCblm
     * @return bool
     */
    public function destroy(FourIRCblm $fourIrCblm): bool
    {
        return $fourIrCblm->delete();
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

        if(!empty($request->input('four_ir_project_id'))){
            $curriculum = FourIRProjectCurriculum::where('four_ir_project_id', $request->input('four_ir_project_id'))->first();
            throw_if(empty($curriculum), ValidationException::withMessages([
                "four_ir_project_id" => "First complete Four IR Project Curriculum!"
            ]));
        }

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
                'required',
                'string',
                'max:500',
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
            'four_ir_project_id' => 'required|int',
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
