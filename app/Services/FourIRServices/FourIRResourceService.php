<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRResource;
use App\Models\FourIRSector;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


/**
 * Class FourIRResourceService
 * @package App\Services\FourIRResourceService
 */
class FourIRResourceService
{
    /**
     * @param int $fourIrId
     * @return Model|Builder
     */
    public function getOneFourIRResource(int $fourIrId): Builder|Model
    {

        /** @var Builder $fourIrResourceBuilder */
        $fourIrResourceBuilder = FourIRResource::select(
            [
                'four_ir_resources.id',
                'four_ir_resources.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_resources.approve_by',
                'four_ir_resources.is_developed_financial_proposal',
                'four_ir_resources.total_amount',
                'four_ir_resources.comment',
                'four_ir_resources.file_path',
                'four_ir_resources.row_status',
                'four_ir_resources.created_by',
                'four_ir_resources.updated_by',
                'four_ir_resources.created_at',
                'four_ir_resources.updated_at'
            ]
        );

        $fourIrResourceBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_resources.four_ir_initiative_id');

        $fourIrResourceBuilder->where('four_ir_resources.id', $fourIrId);


        return $fourIrResourceBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @param FourIRResource|null $fourIRResource
     * @return FourIRResource
     */
    public function store(array $data, FourIRResource|null $fourIRResource): FourIRResource
    {
        if (empty($fourIRResource)) {
            /** Update initiative stepper */
            $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

            $payload = [];

            if ($initiative->form_step < FourIRInitiative::FORM_STEP_RESOURCE_MANAGEMENT) {
                $payload['form_step'] = FourIRInitiative::FORM_STEP_RESOURCE_MANAGEMENT;
            }
            if ($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_SEVEN) {
                $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_SEVEN;
            }

            $initiative->fill($payload);
            $initiative->save();

            /** Create new instance to store */
            $fourIRResource = new FourIRResource();
        }

        $fourIRResource->fill($data);
        $fourIRResource->save();

        return $fourIRResource;
    }

    /**
     * @param FourIRResource $sector
     * @param array $data
     * @return FourIRResource
     */
    public function update(FourIRResource $sector, array $data): FourIRResource
    {
        $sector->fill($data);
        $sector->save();
        return $sector;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws Throwable
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if (!empty($data['four_ir_initiative_id'])) {
            $fourIrInitiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
                "This form step is not allowed as the initiative was set for Not Skill Provider!"
            ]));

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_CBLM, ValidationException::withMessages([
                'Complete CBLM step first.[24000]'
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
            'is_developed_financial_proposal' => [
                'required',
                'int',
                Rule::in(BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE)
            ],
            'total_amount' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['is_developed_financial_proposal'];
                }),
                'nullable',
                'int',
            ],
            'file_path' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['is_developed_financial_proposal'];
                }),
                'nullable',
                'string'
            ],
            'approve_by' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['is_developed_financial_proposal'];
                }),
                'nullable',
                'string'
            ],
            'comment' => [
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
        return Validator::make($data, $rules, $customMessage);
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
            'approval_status' => 'nullable|int',
            'budget_approval_status' => 'nullable|int',
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

    /**
     * @param $filter
     * @param Carbon $startTime
     * @return array
     */
    public function getResourceList($request, Carbon $startTime): array
    {

        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";
        $approvalStatus = $request['approval_status'] ?? "";
        $budgetApprovalStatus = $request['budget_approval_status'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Builder $sectorBuilder */
        $fourIrResourceBuilder = FourIRResource::select([

            'four_ir_resources.id',
            'four_ir_resources.four_ir_initiative_id',

            'four_ir_initiatives.name as initiative_name',
            'four_ir_initiatives.name_en as initiative_name_en',
            'four_ir_initiatives.is_skill_provide',
            'four_ir_initiatives.completion_step',
            'four_ir_initiatives.form_step',

            'four_ir_resources.approve_by',
            'four_ir_resources.is_developed_financial_proposal',
            'four_ir_resources.total_amount',
            'four_ir_resources.comment',
            'four_ir_resources.file_path',
            'four_ir_resources.row_status',
            'four_ir_resources.created_by',
            'four_ir_resources.updated_by',
            'four_ir_resources.created_at',
            'four_ir_resources.updated_at'
        ])->acl();

        $fourIrResourceBuilder->orderBy('four_ir_resources.id', $order);


        if (is_numeric($rowStatus)) {
            $fourIrResourceBuilder->where('four_ir_resources.row_status', $rowStatus);
        }

        $fourIrResourceBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_resources.four_ir_initiative_id');

        if (!empty($fourIrInitiativeId)) {
            $fourIrResourceBuilder->where('four_ir_resources.four_ir_initiative_id', $fourIrInitiativeId);
        }
        if (!empty($approvalStatus)) {
            $fourIrResourceBuilder->where('four_ir_resources.approval_status', $approvalStatus);
        }
        if (!empty($budgetApprovalStatus)) {
            $fourIrResourceBuilder->where('four_ir_resources.budget_approval_status', $budgetApprovalStatus);
        }


        /** @var Collection $occupations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $occupations = $sectorBuilder->paginate($pageSize);
            $paginateData = (object)$occupations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $resource = $fourIrResourceBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $resource->toArray()['data'] ?? $resource->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }
}
