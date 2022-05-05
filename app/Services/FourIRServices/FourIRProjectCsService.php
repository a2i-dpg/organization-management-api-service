<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRProjectCs;
use App\Models\FourIRInitiativeTnaFormat;
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
 * Class RankService
 * @package App\Services
 */
class FourIRProjectCsService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectCsList(array $request, Carbon $startTime): array
    {
        $fourIrProjectId = $request['four_ir_initiative_id'] ?? "";
        $developerOrganizationName = $request['developer_organization_name'] ?? "";
        $developerOrganizationNameEn = $request['developer_organization_name_en'] ?? "";
        $sectorName = $request['sector_name'] ?? "";
        $sectorNameEn = $request['sector_name_en'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectCsBuilder */
        $fourIrProjectCsBuilder = FourIRProjectCs::select(
            [
                'four_ir_project_cs.id',
                'four_ir_project_cs.four_ir_initiative_id',
                'four_ir_project_cs.experts',
                'four_ir_project_cs.level',
                'four_ir_project_cs.approved_by',
                'four_ir_project_cs.developer_organization_name',
                'four_ir_project_cs.developer_organization_name_en',
                'four_ir_project_cs.sector_name',
                'four_ir_project_cs.sector_name_en',
                'four_ir_project_cs.supported_by',
                'four_ir_project_cs.comment',
                'four_ir_project_cs.file_path',
                'four_ir_project_cs.row_status',
                'four_ir_project_cs.created_by',
                'four_ir_project_cs.updated_by',
                'four_ir_project_cs.created_at',
                'four_ir_project_cs.updated_at'
            ]
        )->acl();

        $fourIrProjectCsBuilder->orderBy('four_ir_project_cs.id', $order);

        if (!empty($fourIrProjectId)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.four_ir_initiative_id', $fourIrProjectId);
        }

        if (!empty($developerOrganizationName)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.developer_organization_name', 'like', '%' . $developerOrganizationName . '%');
        }
        if (!empty($developerOrganizationNameEn)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.developer_organization_name_en', 'like', '%' . $developerOrganizationNameEn . '%');
        }

        if (!empty($sectorName)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.sector_name', 'like', '%' . $sectorName . '%');
        }
        if (!empty($sectorNameEn)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.sector_name_en', 'like', '%' . $sectorNameEn . '%');
        }

        if (is_numeric($rowStatus)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjects */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjects = $fourIrProjectCsBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjects->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjects = $fourIrProjectCsBuilder->get();
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
     * @return FourIRProjectCs
     */
    public function getOneFourIrProjectCs(int $id): FourIRProjectCs
    {
        /** @var FourIRProjectCs|Builder $fourIrProjectCsBuilder */
        $fourIrProjectCsBuilder = FourIRProjectCs::select(
            [
                'four_ir_project_cs.id',
                'four_ir_project_cs.four_ir_initiative_id',
                'four_ir_project_cs.experts',
                'four_ir_project_cs.level',
                'four_ir_project_cs.approved_by',
                'four_ir_project_cs.developer_organization_name',
                'four_ir_project_cs.developer_organization_name_en',
                'four_ir_project_cs.sector_name',
                'four_ir_project_cs.sector_name_en',
                'four_ir_project_cs.supported_by',
                'four_ir_project_cs.comment',
                'four_ir_project_cs.file_path',
                'four_ir_project_cs.row_status',
                'four_ir_project_cs.created_by',
                'four_ir_project_cs.updated_by',
                'four_ir_project_cs.created_at',
                'four_ir_project_cs.updated_at'
            ]
        );
        $fourIrProjectCsBuilder->where('four_ir_project_cs.id', '=', $id);

        return $fourIrProjectCsBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRProjectCs
     */
    public function store(array $data): FourIRProjectCs
    {
        $fourIrProjectCs = new FourIRProjectCs();
        $fourIrProjectCs->fill($data);
        $fourIrProjectCs->save();
        return $fourIrProjectCs;
    }

    /**
     * @param FourIRProjectCs $fourIrProjectCs
     * @param array $data
     * @return FourIRProjectCs
     */
    public function update(FourIRProjectCs $fourIrProjectCs, array $data): FourIRProjectCs
    {
        $fourIrProjectCs->fill($data);
        $fourIrProjectCs->save();
        return $fourIrProjectCs;
    }

    /**
     * @param FourIRProjectCs $fourIrProjectCs
     * @return bool
     */
    public function destroy(FourIRProjectCs $fourIrProjectCs): bool
    {
        return $fourIrProjectCs->delete();
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

        if(!empty($request->input('four_ir_initiative_id'))){
            $tnaReport = FourIRInitiativeTnaFormat::where('four_ir_initiative_id', $request->input('four_ir_initiative_id'))->first();
            throw_if(empty($tnaReport), ValidationException::withMessages([
                "four_ir_initiative_id" => "First complete Four IR Project Tna Format!"
            ]));
        }

        $rules = [
            'four_ir_initiative_id' => [
                'required',
                'integer',
                'exists:four_ir_initiatives,id,deleted_at,NULL',
            ],
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'experts' => [
                'required',
                'string',
                'max:400',
                'min:2'
            ],
            'level' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'approved_by' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'developer_organization_name' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'developer_organization_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'sector_name' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'sector_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'supported_by' => [
                'required',
                'string',
                'max:200',
                'min:2'
            ],
            'comment' => [
                'nullable',
                'string',
                'max:1000',
                'min:2'
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
            'four_ir_initiative_id' => 'required|int',
            'developer_organization_name' => 'nullable|max:300|min:2',
            'developer_organization_name_en' => 'nullable|max:300|min:2',
            'sector_name' => 'nullable|max:200|min:2',
            'sector_name_en' => 'nullable|max:200|min:2',
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
