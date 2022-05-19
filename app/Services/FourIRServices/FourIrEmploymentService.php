<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIREmployment;
use App\Models\FourIRInitiative;
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
 * Class FourIrEmploymentService
 * @package App\Services
 */
class FourIrEmploymentService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrEmploymentList(array $request, Carbon $startTime): array
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";
        $name = $request['name'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrEmploymentBuilder */
        $fourIrEmploymentBuilder = FourIREmployment::select(
            [
                'four_ir_employments.id',
                'four_ir_employments.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_employments.name',
                'four_ir_employments.name_en',
                'four_ir_employments.email',
                'four_ir_employments.industry_name',
                'four_ir_employments.industry_name_en',
                'four_ir_employments.job_starting_date',
                'four_ir_employments.contact_number',
                'four_ir_employments.designation',
                'four_ir_employments.starting_salary',
                'four_ir_employments.medium_of_job',
                'four_ir_employments.accessor_id',
                'four_ir_employments.row_status',
                'four_ir_employments.created_by',
                'four_ir_employments.updated_by',
                'four_ir_employments.created_at',
                'four_ir_employments.updated_at'
            ]
        )->acl();

        $fourIrEmploymentBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_employments.four_ir_initiative_id');

        if(!empty($fourIrInitiativeId)){
            $fourIrEmploymentBuilder->where('four_ir_initiative_id', $fourIrInitiativeId);
        }

        if (!empty($name)) {
            $fourIrEmploymentBuilder->where(function ($builder) use ($name){
                $builder->where('four_ir_employments.name', 'like', '%' . $name . '%');
                $builder->orWhere('four_ir_employments.name_en', 'like', '%' . $name . '%');
            });
        }

        $fourIrEmploymentBuilder->orderBy('four_ir_employments.id', $order);

        if (is_numeric($rowStatus)) {
            $fourIrEmploymentBuilder->where('four_ir_employments.row_status', $rowStatus);
        }

        /** @var Collection $fourIrEmployments */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrEmployments = $fourIrEmploymentBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrEmployments->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrEmployments = $fourIrEmploymentBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrEmployments->toArray()['data'] ?? $fourIrEmployments->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    public function getEmploymentByYouthIds(array $youthIds, int $fourIrInitiativeId, Carbon $startTime): array
    {

        /** @var Builder $fourIrEmploymentBuilder */
        $fourIrEmploymentBuilder = FourIREmployment::select(
            [
                'four_ir_employments.id',
                'four_ir_employments.four_ir_initiative_id',
                'four_ir_employments.name',
                'four_ir_employments.user_id',
                'four_ir_employments.employment_status',
                'four_ir_employments.name_en',
                'four_ir_employments.email',
                'four_ir_employments.industry_name',
                'four_ir_employments.industry_name_en',
                'four_ir_employments.job_starting_date',
                'four_ir_employments.contact_number',
                'four_ir_employments.designation',
                'four_ir_employments.starting_salary',
                'four_ir_employments.medium_of_job',
                'four_ir_employments.accessor_id',
                'four_ir_employments.row_status',
                'four_ir_employments.created_by',
                'four_ir_employments.updated_by',
                'four_ir_employments.created_at',
                'four_ir_employments.updated_at'
            ]
        )->acl();

        if(!empty($fourIrInitiativeId)){
            $fourIrEmploymentBuilder->where('four_ir_employments.four_ir_initiative_id', $fourIrInitiativeId);
        }

        if(!empty($youthIds)){
            $fourIrEmploymentBuilder->whereIn('four_ir_employments.user_id', $youthIds);
        }

        return  $fourIrEmploymentBuilder->get()->toArray()['data'] ?? $fourIrEmploymentBuilder->get()->toArray();
    }

    /**
     * @param int $id
     * @return FourIREmployment
     */
    public function getOneFourIrEmployment(int $id): FourIREmployment
    {
        /** @var FourIREmployment|Builder $fourIrEmploymentBuilder */
        $fourIrEmploymentBuilder = FourIREmployment::select(
            [
                'four_ir_employments.id',
                'four_ir_employments.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',

                'four_ir_employments.name',
                'four_ir_employments.name_en',
                'four_ir_employments.email',
                'four_ir_employments.industry_name',
                'four_ir_employments.industry_name_en',
                'four_ir_employments.job_starting_date',
                'four_ir_employments.contact_number',
                'four_ir_employments.designation',
                'four_ir_employments.starting_salary',
                'four_ir_employments.employment_status',
                'four_ir_employments.medium_of_job',
                'four_ir_employments.accessor_id',
                'four_ir_employments.row_status',
                'four_ir_employments.created_by',
                'four_ir_employments.updated_by',
                'four_ir_employments.created_at',
                'four_ir_employments.updated_at'
            ]
        );
        $fourIrEmploymentBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_employments.four_ir_initiative_id');

        $fourIrEmploymentBuilder->where('four_ir_employments.id', '=', $id);

        return $fourIrEmploymentBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIREmployment
     */
    public function store(array $data): FourIREmployment
    {
        /** Update form step & completion step first */
        $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);
        $payload = [];

        if($initiative->form_step < FourIRInitiative::FORM_STEP_EMPLOYMENT){
            $payload['form_step'] = FourIRInitiative::FORM_STEP_EMPLOYMENT;
        }
        if($initiative->completion_step < FourIRInitiative::COMPLETION_STEP_FOURTEEN){
            $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_FOURTEEN;
        }
        $initiative->fill($payload);
        $initiative->save();

        $fourIrEmployment= FourIREmployment::updateOrCreate(
            [
                'user_id' =>  $data['user_id']
            ],
           $data
        );

        return $fourIrEmployment;
    }

    /**
     * @param FourIREmployment $fourIrEmployment
     * @param array $data
     * @return FourIREmployment
     */
    public function update(FourIREmployment $fourIrEmployment, array $data): FourIREmployment
    {
        $fourIrEmployment->fill($data);
        $fourIrEmployment->save();
        return $fourIrEmployment;
    }

    /**
     * @param FourIREmployment $fourIrEmployment
     * @return bool
     */
    public function destroy(FourIREmployment $fourIrEmployment): bool
    {
        return $fourIrEmployment->delete();
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

//        if(!empty($data['four_ir_initiative_id'])){
//            $fourIrInitiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);
//
//            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_FALSE, ValidationException::withMessages([
//                "This form step is not allowed as the initiative was set for Not Skill Provider!"
//            ]));
//
//            throw_if(!empty($fourIrInitiative) && $fourIrInitiative->form_step < FourIRInitiative::FORM_STEP_CREATE_APPROVE_COURSE, ValidationException::withMessages([
//                'Complete Create & Approve course step first.[24000]'
//            ]));
//        }

        $rules = [
            'four_ir_initiative_id' => [
                'required',
                'int',
                'exists:four_ir_initiatives,id,deleted_at,NULL'
            ],
            'accessor_type' => [
                'required',
                'string'
            ],
            'accessor_id' => [
                'required',
                'int'
            ],
            "employment_status"=>[
                'required',
                'int'
            ],
            "user_id"=>[
        'required',
        'int'
        ],
            'name' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['employment_status'];
                }),
                'nullable',
                'string',
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'email' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['employment_status'];
                }),
                'nullable',
                'email'
            ],
            'industry_name' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['employment_status'];
                }),
                'nullable',
                'string',
            ],
            'industry_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'job_starting_date' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['employment_status'];
                }),
                'nullable',
                'date-format:Y-m-d'
            ],
            'contact_number' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['employment_status'];
                }),
                'nullable',
                BaseModel::MOBILE_REGEX
            ],
            'designation' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['employment_status'];
                }),
                'nullable',
                'string',
                'max:300'
            ],
            'starting_salary' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['employment_status'];
                }),
                'nullable',
                'int'
            ],
            'medium_of_job' => [
                Rule::requiredIf(function () use ($data) {
                    return (bool)$data['employment_status'];
                }),
                'nullable',
                'string',
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
            'name' => 'nullable|max:600|min:2',
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
