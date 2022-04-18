<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\FourIrProject;
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
class FourIrProjectService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectList(array $request, Carbon $startTime): array
    {
        $projectName = $request['project_name'] ?? "";
        $projectNameEn = $request['project_name_en'] ?? "";
        $organizationName = $request['organization_name'] ?? "";
        $organizationNameEn = $request['organization_name_en'] ?? "";
        $startDate = $request['start_date'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectBuilder */
        $fourIrProjectBuilder = FourIrProject::select(
            [
                'four_ir_projects.id',
                'four_ir_projects.project_name',
                'four_ir_projects.project_name_en',
                'four_ir_projects.organization_name',
                'four_ir_projects.organization_name_en',
                'four_ir_projects.occupation_id',
                'four_ir_projects.start_date',
                'four_ir_projects.budget',
                'four_ir_projects.project_code',
                'four_ir_projects.file_path',
                'four_ir_projects.tasks',
                'four_ir_projects.completion_step',
                'four_ir_projects.form_step',
                'four_ir_projects.accessor_type',
                'four_ir_projects.accessor_id',
                'four_ir_projects.row_status',
                'four_ir_projects.created_by',
                'four_ir_projects.updated_by',
                'four_ir_projects.created_at',
                'four_ir_projects.updated_at'
            ]
        )->acl();

        $fourIrProjectBuilder->orderBy('four_ir_projects.id', $order);

        if (!empty($projectName)) {
            $fourIrProjectBuilder->where('four_ir_projects.project_name', 'like', '%' . $projectName . '%');
        }
        if (!empty($projectNameEn)) {
            $fourIrProjectBuilder->where('four_ir_projects.project_name_en', 'like', '%' . $projectNameEn . '%');
        }

        if (!empty($organizationName)) {
            $fourIrProjectBuilder->where('four_ir_projects.organization_name', 'like', '%' . $organizationName . '%');
        }
        if (!empty($organizationNameEn)) {
            $fourIrProjectBuilder->where('four_ir_projects.organization_name_en', 'like', '%' . $organizationNameEn . '%');
        }

        if (!empty($startDate)) {
            $fourIrProjectBuilder->whereDate('four_ir_projects.organization_id', $startDate);
        }

        if (is_numeric($rowStatus)) {
            $fourIrProjectBuilder->where('four_ir_projects.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjects */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjects = $fourIrProjectBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjects->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjects = $fourIrProjectBuilder->get();
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
     * @return FourIrProject
     */
    public function getOneFourIrProject(int $id): FourIrProject
    {
        /** @var FourIrProject|Builder $fourIrProjectBuilder */
        $fourIrProjectBuilder = FourIrProject::select(
            [
                'four_ir_projects.id',
                'four_ir_projects.project_name',
                'four_ir_projects.project_name_en',
                'four_ir_projects.organization_name',
                'four_ir_projects.organization_name_en',
                'four_ir_projects.occupation_id',
                'four_ir_projects.details',
                'four_ir_projects.start_date',
                'four_ir_projects.budget',
                'four_ir_projects.project_code',
                'four_ir_projects.file_path',
                'four_ir_projects.tasks',
                'four_ir_projects.completion_step',
                'four_ir_projects.form_step',
                'four_ir_projects.accessor_type',
                'four_ir_projects.accessor_id',
                'four_ir_projects.row_status',
                'four_ir_projects.created_by',
                'four_ir_projects.updated_by',
                'four_ir_projects.created_at',
                'four_ir_projects.updated_at'
            ]
        );
        $fourIrProjectBuilder->where('four_ir_projects.id', '=', $id);

        return $fourIrProjectBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIrProject
     */
    public function store(array $data): FourIrProject
    {
        $data['project_code'] = Uuid::uuid4();
        $data['completion_step'] = FourIrProject::COMPLETION_STEP_ONE;
        $data['form_step'] = FourIrProject::FORM_STEP_PROJECT_INITIATION;

        $fourIrProject = new FourIrProject();
        $fourIrProject->fill($data);
        $fourIrProject->save();
        return $fourIrProject;
    }

    /**
     * @param FourIrProject $fourIrProject
     * @param array $data
     * @return FourIrProject
     */
    public function update(FourIrProject $fourIrProject, array $data): FourIrProject
    {
        $fourIrProject->fill($data);
        $fourIrProject->save();
        return $fourIrProject;
    }

    /**
     * @param FourIrProject $fourIrProject
     * @return bool
     */
    public function destroy(FourIrProject $fourIrProject): bool
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
