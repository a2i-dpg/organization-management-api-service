<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRProjectCs;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;


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
        $fourIrProjectId = $request['four_ir_project_id'] ?? "";
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
                'four_ir_project_cs.four_ir_project_id',
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
            $fourIrProjectCsBuilder->where('four_ir_project_cs.four_ir_project_id', $fourIrProjectId);
        }

        if (!empty($developerOrganizationName)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.developer_organization_name', 'like', '%' . $developerOrganizationName . '%');
        }
        if (!empty($developerOrganizationNameEn)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.developer_organization_name_en', 'like', '%' . $developerOrganizationNameEn . '%');
        }

        if (!empty($sectorName)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.organization_name', 'like', '%' . $sectorName . '%');
        }
        if (!empty($sectorNameEn)) {
            $fourIrProjectCsBuilder->where('four_ir_project_cs.organization_name_en', 'like', '%' . $sectorNameEn . '%');
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
                'four_ir_project_cs.project_name',
                'four_ir_project_cs.project_name_en',
                'four_ir_project_cs.organization_name',
                'four_ir_project_cs.organization_name_en',
                'four_ir_project_cs.occupation_id',
                'four_ir_project_cs.details',
                'four_ir_project_cs.start_date',
                'four_ir_project_cs.budget',
                'four_ir_project_cs.project_code',
                'four_ir_project_cs.file_path',
                'four_ir_project_cs.tasks',
                'four_ir_project_cs.completion_step',
                'four_ir_project_cs.form_step',
                'four_ir_project_cs.accessor_type',
                'four_ir_project_cs.accessor_id',
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
        $data['project_code'] = Uuid::uuid4()->toString();
        $data['completion_step'] = FourIRProjectCs::COMPLETION_STEP_ONE;
        $data['form_step'] = FourIRProjectCs::FORM_STEP_PROJECT_INITIATION;

        $fourIrProject = new FourIRProjectCs();
        $fourIrProject->fill($data);
        $fourIrProject->save();
        return $fourIrProject;
    }

    /**
     * @param FourIRProjectCs $fourIrProject
     * @param array $data
     * @return FourIRProjectCs
     */
    public function update(FourIRProjectCs $fourIrProject, array $data): FourIRProjectCs
    {
        $fourIrProject->fill($data);
        $fourIrProject->save();
        return $fourIrProject;
    }

    /**
     * @param FourIRProjectCs $fourIrProject
     * @return bool
     */
    public function destroy(FourIRProjectCs $fourIrProject): bool
    {
        return $fourIrProject->delete();
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
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            'project_name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'project_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'organization_name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'organization_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'occupation_id' => [
                'required',
                'int',
                'exists:occupations,id,deleted_at,NULL'
            ],
            'details' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'start_date' => [
                'required',
                'date_format:Y-m-d'
            ],
            'budget' => [
                'required',
                'numeric'
            ],
            'file_path' => [
                'required',
                'string',
                'max:500',
            ],
            'tasks' => [
                'required',
                'array',
                'min:1'
            ],
            'tasks.*' => [
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
