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
    public function getFourIrInitiativeList(array $request, Carbon $startTime): array
    {
        $initiativeName = $request['name'] ?? "";
        $organizationName = $request['organization_name'] ?? "";
        $startDate = $request['start_date'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrInitiativeBuilder */
        $fourIrInitiativeBuilder = FourIRInitiative::select(
            [
                'four_ir_initiatives.id',
                'four_ir_initiatives.four_ir_tagline_id',
                'four_ir_initiatives.name',
                'four_ir_initiatives.name_en',
                'four_ir_initiatives.organization_name',
                'four_ir_initiatives.organization_name_en',
                'four_ir_initiatives.budget',
                'four_ir_initiatives.designation',
                'four_ir_initiatives.four_ir_occupation_id',
                'four_ir_occupations.title as occupation_title',
                'four_ir_occupations.title_en as occupation_title_en',
                'four_ir_initiatives.start_date',
                'four_ir_initiatives.end_date',
                'four_ir_initiatives.file_path',
                'four_ir_initiatives.tasks',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',
                'four_ir_initiatives.initiative_code',
                'four_ir_initiatives.accessor_type',
                'four_ir_initiatives.accessor_id',
                'four_ir_initiatives.row_status',
                'four_ir_initiatives.created_by',
                'four_ir_initiatives.updated_by',
                'four_ir_initiatives.created_at',
                'four_ir_initiatives.updated_at'
            ]
        )->acl();

        $fourIrInitiativeBuilder->join('four_ir_occupations', 'four_ir_occupations.id', '=', 'four_ir_initiatives.four_ir_occupation_id');

        if (!empty($initiativeName)) {
            $fourIrInitiativeBuilder->where(function ($builder) use ($initiativeName){
                $builder->where('four_ir_initiatives.name', 'like', '%' . $initiativeName . '%');
                $builder->orWhere('four_ir_initiatives.name_en', 'like', '%' . $initiativeName . '%');
            });
        }

        if (!empty($organizationName)) {
            $fourIrInitiativeBuilder->where(function ($builder) use ($organizationName){
                $builder->where('four_ir_initiatives.organization_name', 'like', '%' . $organizationName . '%');
                $builder->orWhere('four_ir_initiatives.organization_name_en', 'like', '%' . $organizationName . '%');
            });
        }

        if (!empty($startDate)) {
            $fourIrInitiativeBuilder->whereDate('four_ir_initiatives.start_date', $startDate);
        }

        $fourIrInitiativeBuilder->orderBy('four_ir_initiatives.id', $order);

        if (is_numeric($rowStatus)) {
            $fourIrInitiativeBuilder->where('four_ir_initiatives.row_status', $rowStatus);
        }

        /** @var Collection $fourIrInitiatives */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrInitiatives = $fourIrInitiativeBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrInitiatives->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrInitiatives = $fourIrInitiativeBuilder->get();
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
     * @return FourIRInitiative
     */
    public function getOneFourIrInitiative(int $id): FourIRInitiative
    {
        /** @var FourIRInitiative|Builder $fourIrInitiativeBuilder */
        $fourIrInitiativeBuilder = FourIRInitiative::select(
            [
                'four_ir_initiatives.id',
                'four_ir_initiatives.four_ir_tagline_id',
                'four_ir_initiatives.name',
                'four_ir_initiatives.name_en',
                'four_ir_initiatives.organization_name',
                'four_ir_initiatives.organization_name_en',
                'four_ir_initiatives.budget',
                'four_ir_initiatives.designation',
                'four_ir_initiatives.four_ir_occupation_id',
                'four_ir_occupations.title as occupation_title',
                'four_ir_occupations.title_en as occupation_title_en',
                'four_ir_initiatives.start_date',
                'four_ir_initiatives.end_date',
                'four_ir_initiatives.details',
                'four_ir_initiatives.file_path',
                'four_ir_initiatives.tasks',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',
                'four_ir_initiatives.initiative_code',
                'four_ir_initiatives.accessor_type',
                'four_ir_initiatives.accessor_id',
                'four_ir_initiatives.row_status',
                'four_ir_initiatives.created_by',
                'four_ir_initiatives.updated_by',
                'four_ir_initiatives.created_at',
                'four_ir_initiatives.updated_at'
            ]
        );
        $fourIrInitiativeBuilder->where('four_ir_initiatives.id', '=', $id);

        $fourIrInitiativeBuilder->join('four_ir_occupations', 'four_ir_occupations.id', '=', 'four_ir_initiatives.four_ir_occupation_id');

        return $fourIrInitiativeBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRInitiative
     */
    public function store(array $data): FourIRInitiative
    {
        $data['initiative_code'] = Uuid::uuid4()->toString();
        $data['completion_step'] = FourIRInitiative::COMPLETION_STEP_ONE;
        $data['form_step'] = FourIRInitiative::FORM_STEP_PROJECT_INITIATION;

        $fourIrInitiative = new FourIRInitiative();
        $fourIrInitiative->fill($data);
        $fourIrInitiative->save();
        return $fourIrInitiative;
    }

    /**
     * @param FourIRInitiative $fourIrInitiative
     * @param array $data
     * @return FourIRInitiative
     */
    public function update(FourIRInitiative $fourIrInitiative, array $data): FourIRInitiative
    {
        $fourIrInitiative->fill($data);
        $fourIrInitiative->save();
        return $fourIrInitiative;
    }

    /**
     * @param FourIRInitiative $fourIrInitiative
     * @return bool
     */
    public function destroy(FourIRInitiative $fourIrInitiative): bool
    {
        return $fourIrInitiative->delete();
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
                'required',
                'int',
                'exists:four_ir_taglines,id,deleted_at,NULL'
            ],
            'name' => [
                'required',
                'string',
                'max:600',
                'min:2'
            ],
            'name_en' => [
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
            'budget' => [
                'required',
                'numeric'
            ],
            'designation' => [
                'required',
                'string',
                'max:300'
            ],
            'four_ir_occupation_id' => [
                'required',
                'int',
                'exists:four_ir_occupations,id,deleted_at,NULL'
            ],
            'start_date' => [
                'required',
                'date_format:Y-m-d'
            ],
            'end_date' => [
                'required',
                'date_format:Y-m-d',
                'after:start_date'
            ],
            'details' => [
                'nullable',
                'string',
                'max:1000'
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
            'name' => 'nullable|max:600|min:2',
            'organization_name' => 'nullable|max:600|min:2',
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
