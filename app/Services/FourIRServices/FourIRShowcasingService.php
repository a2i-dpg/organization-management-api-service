<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRShowcasing;
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
 * Class FourIRShowcasingService
 * @package App\Services
 */
class FourIRShowcasingService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourShowcasingList(array $request, Carbon $startTime): array
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";
        $organizationName = $request['organization_name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectCellBuilder */
        $fourIrShowcasingBuilder = FourIRShowcasing::select(
            [
                'four_ir_showcasings.id',
                'four_ir_showcasings.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_showcasings.organization_name',
                'four_ir_showcasings.organization_name_en',
                'four_ir_showcasings.invite_other_organization',
                'four_ir_showcasings.invite_other_organization_en',
                'four_ir_showcasings.venue',
                'four_ir_showcasings.start_time',
                'four_ir_showcasings.end_time',
                'four_ir_showcasings.event_description',
                'four_ir_showcasings.file_path',
                'four_ir_showcasings.row_status',
                'four_ir_showcasings.created_by',
                'four_ir_showcasings.updated_by',
                'four_ir_showcasings.created_at',
                'four_ir_showcasings.updated_at'
            ]
        )->acl();

        $fourIrShowcasingBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_showcasings.four_ir_initiative_id');

        $fourIrShowcasingBuilder->orderBy('four_ir_showcasings.id', $order);

        if(!empty($fourIrInitiativeId)){
            $fourIrShowcasingBuilder->where('four_ir_showcasings.four_ir_initiative_id', $fourIrInitiativeId);
        }

        if (!empty($organizationName)) {
            $fourIrShowcasingBuilder->where(function ($builder) use ($organizationName){
                $builder->where('four_ir_showcasings.organization_name', 'like', '%' . $organizationName . '%');
                $builder->orWhere('four_ir_showcasings.organization_name_en', 'like', '%' . $organizationName . '%');
            });
        }

        if (is_numeric($rowStatus)) {
            $fourIrShowcasingBuilder->where('four_ir_showcasings.row_status', $rowStatus);
        }

        /** @var Collection $fourIrShowcasings */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrShowcasings = $fourIrShowcasingBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrShowcasings->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrShowcasings = $fourIrShowcasingBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrShowcasings->toArray()['data'] ?? $fourIrShowcasings->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRShowcasing
     */
    public function getOneFourIrShowcasing(int $id): FourIRShowcasing
    {
        /** @var FourIRShowcasing|Builder $fourIrShowcasingBuilder */
        $fourIrShowcasingBuilder = FourIRShowcasing::select(
            [
                'four_ir_showcasings.id',
                'four_ir_showcasings.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_showcasings.organization_name',
                'four_ir_showcasings.organization_name_en',
                'four_ir_showcasings.invite_other_organization',
                'four_ir_showcasings.invite_other_organization_en',
                'four_ir_showcasings.venue',
                'four_ir_showcasings.start_time',
                'four_ir_showcasings.end_time',
                'four_ir_showcasings.event_description',
                'four_ir_showcasings.file_path',
                'four_ir_showcasings.row_status',
                'four_ir_showcasings.created_by',
                'four_ir_showcasings.updated_by',
                'four_ir_showcasings.created_at',
                'four_ir_showcasings.updated_at'
            ]
        );
        $fourIrShowcasingBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_showcasings.four_ir_initiative_id');

        $fourIrShowcasingBuilder->where('four_ir_showcasings.id', '=', $id);

        return $fourIrShowcasingBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRShowcasing
     */
    public function store(array $data): FourIRShowcasing
    {
        /** Update form step & completion step first */
        $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

        $payload = [];

        if($initiative->form_step < FourIRInitiative::FORM_STEP_SHOWCASING){
            $payload['form_step'] = FourIRInitiative::FORM_STEP_SHOWCASING;
        }
        if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_FIFTEEN){
            $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_FIFTEEN;
        }

        $initiative->fill($payload);
        $initiative->save();

        /** Now store showcasing */
        $fourIrShowcasing = new FourIRShowcasing();
        $fourIrShowcasing->fill($data);
        $fourIrShowcasing->save();
        return $fourIrShowcasing;
    }

    /**
     * @param FourIRShowcasing $fourIrShowcasing
     * @param array $data
     * @return FourIRShowcasing
     */
    public function update(FourIRShowcasing $fourIrShowcasing, array $data): FourIRShowcasing
    {
        $fourIrShowcasing->fill($data);
        $fourIrShowcasing->save();
        return $fourIrShowcasing;
    }

    /**
     * @param FourIRShowcasing $fourIrShowcasing
     * @return bool
     */
    public function destroy(FourIRShowcasing $fourIrShowcasing): bool
    {
        return $fourIrShowcasing->delete();
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

        if(!empty($data['four_ir_initiative_id'])){
            $fourIrInitiative = FourIRInitiative::findOrFail('four_ir_initiative_id');

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
                "This form step is not allowed as the initiative was set for Not Skill Provider!"
            ]));

            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_EMPLOYMENT, ValidationException::withMessages([
                'Complete Employment step first.[24000]'
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
            'organization_name' => [
                'required',
                'string'
            ],
            'organization_name_en' => [
                'nullable',
                'string'
            ],
            'invite_other_organization' => [
                'required',
                'string'
            ],
            'invite_other_organization_en' => [
                'nullable',
                'string'
            ],
            'venue' => [
                'required',
                'string'
            ],
            'start_time' => [
                'required',
                'date-format:Y-m-d'
            ],
            'end_time' => [
                'required',
                'date-format:Y-m-d',
                'after:start_time'
            ],
            'event_description' => [
                'nullable',
                'string'
            ],
            'file_path' => [
                'nullable',
                'string'
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
            'organization_name' => 'nullable|string',
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
