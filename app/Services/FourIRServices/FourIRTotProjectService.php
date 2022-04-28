<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRProjectCs;
use App\Models\FourIRProjectTot;
use App\Models\FourIRResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Collection\Collection;
use Symfony\Component\HttpFoundation\Response;

class FourIRTotProjectService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectTOtList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_project_id'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectTotBuilder */
        $fourIrProjectTotBuilder = FourIRProjectTot::select([
            'four_ir_project_tots.id',
            'four_ir_project_tots.four_ir_project_id',
            'four_ir_project_tots.accessor_type',
            'four_ir_project_tots.accessor_id',
            'four_ir_project_tots.participants',
            'four_ir_project_tots.master_trainer',
            'four_ir_project_tots.date',
            'four_ir_project_tots.venue',
            'four_ir_project_tots.file_path',
            'four_ir_project_tots.row_status',
            'four_ir_project_tots.created_by',
            'four_ir_project_tots.updated_by',
            'four_ir_project_tots.created_at',
            'four_ir_project_tots.updated_at'
        ])->acl();
        $fourIrProjectTotBuilder->orderBy('four_ir_project_tots.id', $order);

        if (is_numeric($fourIrProjectId)) {
            $fourIrProjectTotBuilder->where('four_ir_project_tots.four_ir_project_id', $fourIrProjectId);
        }
        if (is_numeric($rowStatus)) {
            $fourIrProjectTotBuilder->where('four_ir_project_tots.row_status', $rowStatus);
        }

        /** @var  Collection $fourIrProjectTots */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjectTots = $fourIrProjectTotBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjectTots->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjectTots = $fourIrProjectTotBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrProjectTots->toArray()['data'] ?? $fourIrProjectTots->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;

    }



    /**
     * @param int $id
     * @return FourIRProjectTot
     */
    public function getOneFourIrProjectCs(int $id): FourIRProjectTot
    {
        /** @var FourIRProjectTot|Builder $fourIrProjectTotBuilder */
        $fourIrProjectTotBuilder = FourIRProjectTot::select([
            'four_ir_project_tots.id',
            'four_ir_project_tots.four_ir_project_id',
            'four_ir_project_tots.accessor_type',
            'four_ir_project_tots.accessor_id',
            'four_ir_project_tots.participants',
            'four_ir_project_tots.master_trainer',
            'four_ir_project_tots.date',
            'four_ir_project_tots.venue',
            'four_ir_project_tots.file_path',
            'four_ir_project_tots.row_status',
            'four_ir_project_tots.created_by',
            'four_ir_project_tots.updated_by',
            'four_ir_project_tots.created_at',
            'four_ir_project_tots.updated_at'
        ]);
        $fourIrProjectTotBuilder->where('four_ir_project_tots.id', '=', $id);

        return $fourIrProjectTotBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRProjectTot
     */
    public function store(array $data): FourIRProjectTot
    {
        $fourIrProjectTOt = app(FourIRProjectTot::class);
        $fourIrProjectTOt->fill($data);
        $fourIrProjectTOt->save();
        return $fourIrProjectTOt;
    }

    /**
     * @param FourIRProjectTot $fourIRProjectTot
     * @param array $data
     * @return FourIRProjectTot
     */
    public function update(FourIRProjectTot $fourIRProjectTot, array $data): FourIRProjectTot
    {
        $fourIRProjectTot->fill($data);
        $fourIRProjectTot->save();
        return $fourIRProjectTot;
    }


    /**
     * @param FourIRProjectTot $fourIRProjectTot
     * @return bool
     */
    public function destroy(FourIRProjectTot $fourIRProjectTot): bool
    {
        return $fourIRProjectTot->delete();
    }

    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if (!empty($request->input('four_ir_project_id'))) {
            $tnaReport = FourIRResource::where('four_ir_project_id', $request->input('four_ir_project_id'))->first();
            throw_if(empty($tnaReport), ValidationException::withMessages([
                "four_ir_project_id" => "First complete Four IR Project Resource Management!"
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
            'participants' => [
                'required',
                'string',
            ],
            'master_trainer' => [
                'required',
                'string',
                'max:350',
                'min:2'
            ],
            'date' => [
                'required',
                'date',
            ],
            'venue' => [
                'nullable',
                'string',
                'max:500',
                'min:2',
            ],
            'file_path' => [
                'nullable',
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
            'date' => 'nullable|date',
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
