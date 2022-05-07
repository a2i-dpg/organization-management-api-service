<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRScaleUp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


/**
 * Class FourIRScaleUpService
 * @package App\Services
 */
class FourIRScaleUpService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourShowcasingList(array $request, Carbon $startTime): array
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";
        $projectName = $request['project_name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectCellBuilder */
        $fourIrScaleUpBuilder = FourIRScaleUp::select(
            [
                'four_ir_scale_ups.id',
                'four_ir_scale_ups.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_scale_ups.project_name',
                'four_ir_scale_ups.project_name_en',
                'four_ir_scale_ups.previous_budget',
                'four_ir_scale_ups.implement_timeline',
                'four_ir_scale_ups.implement_area',
                'four_ir_scale_ups.project_documents',
                'four_ir_scale_ups.tentitive_budget',
                'four_ir_scale_ups.beneficiary_target',
                'four_ir_scale_ups.description',
                'four_ir_scale_ups.accessor_type',
                'four_ir_scale_ups.accessor_id',
                'four_ir_scale_ups.row_status',
                'four_ir_scale_ups.created_by',
                'four_ir_scale_ups.updated_by',
                'four_ir_scale_ups.created_at',
                'four_ir_scale_ups.updated_at'
            ]
        )->acl();

        $fourIrScaleUpBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_scale_ups.four_ir_initiative_id');

        $fourIrScaleUpBuilder->orderBy('four_ir_scale_ups.id', $order);

        if(!empty($fourIrInitiativeId)){
            $fourIrScaleUpBuilder->where('four_ir_scale_ups.four_ir_initiative_id', $fourIrInitiativeId);
        }

        if (!empty($projectName)) {
            $fourIrScaleUpBuilder->where(function ($builder) use ($projectName){
                $builder->where('four_ir_scale_ups.project_name', 'like', '%' . $projectName . '%');
                $builder->orWhere('four_ir_scale_ups.project_name_en', 'like', '%' . $projectName . '%');
            });
        }

        if (is_numeric($rowStatus)) {
            $fourIrScaleUpBuilder->where('four_ir_scale_ups.row_status', $rowStatus);
        }

        /** @var Collection $fourIrScaleUps */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrScaleUps = $fourIrScaleUpBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrScaleUps->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrScaleUps = $fourIrScaleUpBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrScaleUps->toArray()['data'] ?? $fourIrScaleUps->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRScaleUp
     */
    public function getOneFourIrShowcasing(int $id): FourIRScaleUp
    {
        /** @var FourIRScaleUp|Builder $fourIrScaleUpBuilder */
        $fourIrScaleUpBuilder = FourIRScaleUp::select(
            [
                'four_ir_scale_ups.id',
                'four_ir_scale_ups.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_scale_ups.project_name',
                'four_ir_scale_ups.project_name_en',
                'four_ir_scale_ups.previous_budget',
                'four_ir_scale_ups.implement_timeline',
                'four_ir_scale_ups.implement_area',
                'four_ir_scale_ups.project_documents',
                'four_ir_scale_ups.tentitive_budget',
                'four_ir_scale_ups.beneficiary_target',
                'four_ir_scale_ups.description',
                'four_ir_scale_ups.accessor_type',
                'four_ir_scale_ups.accessor_id',
                'four_ir_scale_ups.row_status',
                'four_ir_scale_ups.created_by',
                'four_ir_scale_ups.updated_by',
                'four_ir_scale_ups.created_at',
                'four_ir_scale_ups.updated_at'
            ]
        );
        $fourIrScaleUpBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_scale_ups.four_ir_initiative_id');

        $fourIrScaleUpBuilder->where('four_ir_scale_ups.id', '=', $id);

        return $fourIrScaleUpBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRScaleUp
     */
    public function store(array $data): FourIRScaleUp
    {
        /** Update form step & completion step first */
        $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

        $payload = [];

        if($initiative->form_step < FourIRInitiative::FORM_STEP_SCALE_UP){
            $payload['form_step'] = FourIRInitiative::FORM_STEP_SCALE_UP;
        }
        if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_SEVENTEEN){
            $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_SEVENTEEN;
        }

        $initiative->fill($payload);
        $initiative->save();

        /** Now store showcasing */
        $fourIrScaleUp = new FourIRScaleUp();
        $fourIrScaleUp->fill($data);
        $fourIrScaleUp->save();
        return $fourIrScaleUp;
    }

    /**
     * @param FourIRScaleUp $fourIrScaleUp
     * @param array $data
     * @return FourIRScaleUp
     */
    public function update(FourIRScaleUp $fourIrScaleUp, array $data): FourIRScaleUp
    {
        $fourIrScaleUp->fill($data);
        $fourIrScaleUp->save();
        return $fourIrScaleUp;
    }

    /**
     * @param FourIRScaleUp $fourIrScaleUp
     * @return bool
     */
    public function destroy(FourIRScaleUp $fourIrScaleUp): bool
    {
        return $fourIrScaleUp->delete();
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

        if(!empty($data['four_ir_initiative_id'])){
            $fourIrInitiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
                "This form step is not allowed as the initiative was set for Not Skill Provider!"
            ]));

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_PROJECT_ANALYSIS, ValidationException::withMessages([
                'Complete Project Analysis step first.[24000]'
            ]));
        }

        $rules = [
            'four_ir_initiative_id'=>[
                'required',
                'int',
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
            'project_name' => [
                'required',
                'string'
            ],
            'project_name_en' => [
                'nullable',
                'string'
            ],
            'previous_budget' => [
                'required',
                'numeric'
            ],
            'implement_timeline' => [
                'required',
                'string'
            ],
            'implement_area' => [
                'required',
                'string'
            ],
            'project_documents' => [
                'required',
                'string'
            ],
            'tentitive_budget' => [
                'required',
                'numeric'
            ],
            'beneficiary_target' => [
                'required',
                'string'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
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
            'project_name' => 'nullable|string',
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
