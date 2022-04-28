<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRFileLog;
use App\Models\FourIRInitiative;
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
class FourIrInitiativeService
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
        $fourIrProjectBuilder = FourIRInitiative::select(
            [
                'four_ir_initiatives.id',
                'four_ir_initiatives.four_ir_tagline_id',
                'four_ir_initiatives.project_name',
                'four_ir_initiatives.project_name_en',
                'four_ir_initiatives.organization_name',
                'four_ir_initiatives.organization_name_en',
                'four_ir_initiatives.four_ir_occupation_id',
                'four_ir_initiatives.start_date',
                'four_ir_initiatives.budget',
                'four_ir_initiatives.project_code',
                'four_ir_initiatives.file_path',
                'four_ir_initiatives.tasks',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',
                'four_ir_initiatives.accessor_type',
                'four_ir_initiatives.accessor_id',
                'four_ir_initiatives.row_status',
                'four_ir_initiatives.created_by',
                'four_ir_initiatives.updated_by',
                'four_ir_initiatives.created_at',
                'four_ir_initiatives.updated_at'
            ]
        )->acl();

        $fourIrProjectBuilder->orderBy('four_ir_initiatives.id', $order);

        if (!empty($projectName)) {
            $fourIrProjectBuilder->where('four_ir_initiatives.project_name', 'like', '%' . $projectName . '%');
        }
        if (!empty($projectNameEn)) {
            $fourIrProjectBuilder->where('four_ir_initiatives.project_name_en', 'like', '%' . $projectNameEn . '%');
        }

        if (!empty($organizationName)) {
            $fourIrProjectBuilder->where('four_ir_initiatives.organization_name', 'like', '%' . $organizationName . '%');
        }
        if (!empty($organizationNameEn)) {
            $fourIrProjectBuilder->where('four_ir_initiatives.organization_name_en', 'like', '%' . $organizationNameEn . '%');
        }

        if (!empty($startDate)) {
            $fourIrProjectBuilder->whereDate('four_ir_initiatives.organization_id', $startDate);
        }

        if (is_numeric($rowStatus)) {
            $fourIrProjectBuilder->where('four_ir_initiatives.row_status', $rowStatus);
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
     * @return FourIRInitiative
     */
    public function getOneFourIrProject(int $id): FourIRInitiative
    {
        /** @var FourIRInitiative|Builder $fourIrProjectBuilder */
        $fourIrProjectBuilder = FourIRInitiative::select(
            [
                'four_ir_initiatives.id',
                'four_ir_initiatives.four_ir_tagline_id',
                'four_ir_initiatives.project_name',
                'four_ir_initiatives.project_name_en',
                'four_ir_initiatives.organization_name',
                'four_ir_initiatives.organization_name_en',
                'four_ir_initiatives.four_ir_occupation_id',
                'four_ir_initiatives.details',
                'four_ir_initiatives.start_date',
                'four_ir_initiatives.budget',
                'four_ir_initiatives.project_code',
                'four_ir_initiatives.file_path',
                'four_ir_initiatives.tasks',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',
                'four_ir_initiatives.accessor_type',
                'four_ir_initiatives.accessor_id',
                'four_ir_initiatives.row_status',
                'four_ir_initiatives.created_by',
                'four_ir_initiatives.updated_by',
                'four_ir_initiatives.created_at',
                'four_ir_initiatives.updated_at'
            ]
        );
        $fourIrProjectBuilder->where('four_ir_initiatives.id', '=', $id);

        return $fourIrProjectBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRInitiative
     */
    public function store(array $data): FourIRInitiative
    {
        $data['project_code'] = Uuid::uuid4()->toString();
        $data['completion_step'] = FourIRInitiative::COMPLETION_STEP_ONE;
        $data['form_step'] = FourIRInitiative::FORM_STEP_PROJECT_INITIATION;

        $fourIrProject = new FourIRInitiative();
        $fourIrProject->fill($data);
        $fourIrProject->save();
        return $fourIrProject;
    }

    /**
     * @param FourIRInitiative $fourIrProject
     * @param array $data
     * @return FourIRInitiative
     */
    public function update(FourIRInitiative $fourIrProject, array $data): FourIRInitiative
    {
        $fourIrProject->fill($data);
        $fourIrProject->save();
        return $fourIrProject;
    }

    /**
     * @param FourIRInitiative $fourIrProject
     * @return bool
     */
    public function destroy(FourIRInitiative $fourIrProject): bool
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
            'four_ir_tagline_id' => [
                Rule::requiredIf(function () use ($id) {
                    return is_null($id);
                }),
                'nullable',
                'int',
                'exists:four_ir_taglines,id,deleted_at,NULL'
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
            'four_ir_occupation_id' => [
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
                'nullable',
                'string',
                'max:300',
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
            'four_ir_tagline_id' => 'required|int',
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
