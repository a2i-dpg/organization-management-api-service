<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRCreateAndApprove;
use App\Models\FourIRProject;
use App\Models\FourIRResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class FourIRResourceService
 * @package App\Services\FourIRResourceService
 */
class FourIRResourceService
{

    public function getFourIRResourceList(array $request, Carbon $startTime): array
    {

        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrResourceBuilder */
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

        $fourIrResourceBuilder->orderBy('four_ir_resources.id', $order);

        if (is_numeric($rowStatus)) {
            $fourIrResourceBuilder->where('four_ir_resources.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjects */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjects = $fourIrResourceBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjects->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjects = $fourIrResourceBuilder->get();
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
        $fourIrResource = new FourIRResource();
        $fourIrResource->fill($data);
        $fourIrResource->save();
        return $fourIrResource;
    }

    /**
     * @param FourIRResource $fourIrResource
     * @param array $data
     * @return FourIRResource
     */

    public function update(FourIRResource $fourIrResource, array $data): FourIRResource
    {
        $fourIrResource->fill($data);
        $fourIrResource->save();
        return $fourIrResource;
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

        if(!empty($request->input('four_ir_project_id'))){
            $createAndApprove = FourIRCreateAndApprove::where('four_ir_project_id', $request->input('four_ir_project_id'))->first();
            throw_if(empty($createAndApprove), ValidationException::withMessages([
                "four_ir_project_id" => "First complete Four IR  Create And Approve !"
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
