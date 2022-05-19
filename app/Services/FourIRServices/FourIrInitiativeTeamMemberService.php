<?php


namespace App\Services\FourIRServices;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeTeamMember;
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
 * Class FourIrInitiativeTeamMemberService
 * @package App\Services\FourIRServices
 */
class FourIrInitiativeTeamMemberService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrInitiativeTeamMemberList(array $request, Carbon $startTime): array
    {
        $fourIRProjectId = $request['four_ir_initiative_id'] ?? "";
        $email = $request['email'] ?? "";
        $teamType = $request['team_type'] ?? "";
        $phoneNumber = $request['phone_number'] ?? "";
        $role = $request['role'] ?? "";
        $designation = $request['designation'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrInitiativeTeamMemberBuilder */
        $fourIrInitiativeTeamMemberBuilder = FourIRInitiativeTeamMember::select(
            [
                'four_ir_initiative_team_members.id',
                'four_ir_initiative_team_members.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.implementing_team_launching_date',
                'four_ir_initiatives.expert_team_launching_date',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',


                'four_ir_initiative_team_members.name',
                'four_ir_initiative_team_members.name_en',
                'four_ir_initiative_team_members.email',
                'four_ir_initiative_team_members.user_id',
                'four_ir_initiative_team_members.phone_number',
                'four_ir_initiative_team_members.designation',
                'four_ir_initiative_team_members.role_responsibility',
                'four_ir_initiative_team_members.organization',
                'four_ir_initiative_team_members.application_role_id',
                'four_ir_initiative_team_members.file_path',
                'four_ir_initiative_team_members.team_type',
                'four_ir_initiative_team_members.row_status',
                'four_ir_initiative_team_members.created_by',
                'four_ir_initiative_team_members.updated_by',
                'four_ir_initiative_team_members.created_at',
                'four_ir_initiative_team_members.updated_at'
            ]
        )->acl();

        $fourIrInitiativeTeamMemberBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_team_members.four_ir_initiative_id');

        $fourIrInitiativeTeamMemberBuilder->orderBy('four_ir_initiative_team_members.id', $order);

        if (!empty($fourIRProjectId)) {
            $fourIrInitiativeTeamMemberBuilder->where('four_ir_initiative_team_members.four_ir_initiative_id', $fourIRProjectId);
        }

        if (!empty($teamType)) {
            $fourIrInitiativeTeamMemberBuilder->where('four_ir_initiative_team_members.team_type', $teamType);
        }

        if (!empty($email)) {
            $fourIrInitiativeTeamMemberBuilder->where('four_ir_initiative_team_members.email', 'like', '%' . $email . '%');
        }

        if (!empty($phoneNumber)) {
            $fourIrInitiativeTeamMemberBuilder->where('four_ir_initiative_team_members.phone_number', 'like', '%' . $phoneNumber . '%');
        }


        if (!empty($designation)) {
            $fourIrInitiativeTeamMemberBuilder->where('four_ir_initiative_team_members.designation', 'like', '%' . $designation . '%');
        }

        if (is_numeric($rowStatus)) {
            $fourIrInitiativeTeamMemberBuilder->where('four_ir_initiative_team_members.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjectTeamMembers */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjectTeamMembers = $fourIrInitiativeTeamMemberBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjectTeamMembers->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjectTeamMembers = $fourIrInitiativeTeamMemberBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrProjectTeamMembers->toArray()['data'] ?? $fourIrProjectTeamMembers->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return FourIRInitiativeTeamMember
     */
    public function getOneFourIrInitiativeTeamMember(int $id): FourIRInitiativeTeamMember
    {
        /** @var FourIRInitiativeTeamMember|Builder $fourIrInitiativeTeamMemberBuilder */
        $fourIrInitiativeTeamMemberBuilder = FourIRInitiativeTeamMember::select(
            [
                'four_ir_initiative_team_members.id',
                'four_ir_initiative_team_members.four_ir_initiative_id',

                'four_ir_initiatives.name as initiative_name',
                'four_ir_initiatives.name_en as initiative_name_en',
                'four_ir_initiatives.is_skill_provide',
                'four_ir_initiatives.implementing_team_launching_date',
                'four_ir_initiatives.expert_team_launching_date',
                'four_ir_initiatives.completion_step',
                'four_ir_initiatives.form_step',


                'four_ir_initiative_team_members.name',
                'four_ir_initiative_team_members.name_en',
                'four_ir_initiative_team_members.email',
                'four_ir_initiative_team_members.user_id',
                'four_ir_initiative_team_members.phone_number',
                'four_ir_initiative_team_members.designation',
                'four_ir_initiative_team_members.role_responsibility',
                'four_ir_initiative_team_members.organization',
                'four_ir_initiative_team_members.application_role_id',
                'four_ir_initiative_team_members.file_path',
                'four_ir_initiative_team_members.team_type',
                'four_ir_initiative_team_members.row_status',
                'four_ir_initiative_team_members.created_by',
                'four_ir_initiative_team_members.updated_by',
                'four_ir_initiative_team_members.created_at',
                'four_ir_initiative_team_members.updated_at'
            ]
        );
        $fourIrInitiativeTeamMemberBuilder->join('four_ir_initiatives', 'four_ir_initiatives.id', '=', 'four_ir_initiative_team_members.four_ir_initiative_id');

        $fourIrInitiativeTeamMemberBuilder->where('four_ir_initiative_team_members.id', $id);

        return $fourIrInitiativeTeamMemberBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRInitiativeTeamMember
     */
    public function store(array $data): FourIRInitiativeTeamMember
    {
        $userData = $this->createPayloadToStoreUser($data);
        $data['user_id'] = $userData['id'];
        $fourIrProjectTeamMember = app()->make(FourIRInitiativeTeamMember::class);
        $fourIrProjectTeamMember->fill($data);
        $fourIrProjectTeamMember->save();

        $this->updateInitiativeStepper($data);

        return $fourIrProjectTeamMember;
    }

    /**
     * @param array $data
     * @return void
     */
    private function updateInitiativeStepper(array $data)
    {
        $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);

        $initiative->form_step = $data['team_type'] == FourIRInitiativeTeamMember:: IMPLEMENTING_TEAM_TYPE ? FourIRInitiative::FORM_STEP_IMPLEMENTING_TEAM : FourIRInitiative::FORM_STEP_EXPERT_TEAM;
        // Tod do (If cell requirement available then remove)
        if ($data['team_type'] == FourIRInitiativeTeamMember::EXPERT_TEAM_TYPE && $initiative->completion_step < FourIRInitiative::COMPLETION_STEP_TWO) {
            $initiative->completion_step = FourIRInitiative::COMPLETION_STEP_TWO;
        }
        $initiative->save();
    }

    /**
     * @param array $data
     * @return array
     */
    private function createPayloadToStoreUser(array $data): array
    {
        $payload = [
            "user_type" => BaseModel::FOUR_IR_USER_TYPE,
            "username" => $data['phone_number'],
            "role_id" => $data['application_role_id'],
            "name" => $data['name'],
            "name_en" => $data['name_en'] ?? "",
            "email" => $data['email'],
            "mobile" => $data['phone_number'],
            "password" => BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD,
            "password_confirmation" => BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD,
            "row_status" => BaseModel::ROW_STATUS_ACTIVE
        ];

        return ServiceToServiceCall::createFourIrUser($payload);
    }

    /**
     * @param FourIRInitiativeTeamMember $fourIrProjectTeamMember
     * @param array $data
     * @return FourIRInitiativeTeamMember
     */
    public function update(FourIRInitiativeTeamMember $fourIrProjectTeamMember, array $data): FourIRInitiativeTeamMember
    {
        /** Username can't be updated */
        if (!empty($data['phone_number'])) {
            unset($data['phone_number']);
        }

        $fourIrProjectTeamMember->fill($data);
        $fourIrProjectTeamMember->save();

        $this->payloadToUpdateCoreUser($fourIrProjectTeamMember, $data);

        return $fourIrProjectTeamMember;
    }

    /**
     * @param FourIRInitiativeTeamMember $fourIrProjectTeamMember
     * @param array $data
     * @return void
     */
    private function payloadToUpdateCoreUser(FourIRInitiativeTeamMember $fourIrProjectTeamMember, array $data): void
    {
        $payload = [
            "user_id" => $fourIrProjectTeamMember->user_id,
            "name" => $data['name'],
            "name_en" => $data['name_en'] ?? "",
            "email" => $data['email'],
            "row_status" => BaseModel::ROW_STATUS_INACTIVE
        ];

        ServiceToServiceCall::updateFourIrUser($payload);
    }

    /**
     * @param FourIRInitiativeTeamMember $fourIrProjectTeamMember
     * @return bool
     */
    public function destroy(FourIRInitiativeTeamMember $fourIrProjectTeamMember): bool
    {
        /** Core service user delete */
        ServiceToServiceCall::deleteFourIrUser($fourIrProjectTeamMember->toArray());

        return $fourIrProjectTeamMember->delete();
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
        if (!empty($data['four_ir_initiative_id'] && !empty($data['team_type']))) {
            if ($data['team_type'] == FourIRInitiativeTeamMember::EXPERT_TEAM_TYPE) {
                $initiative = FourIRInitiative::findOrFail($data['four_ir_initiative_id']);
                throw_if(!empty($initiative) && $initiative->form_step < FourIRInitiative::FORM_STEP_IMPLEMENTING_TEAM, ValidationException::withMessages([
                    'Complete Implementing team step first.[24000]'
                ]));
            }
        }

        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
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
            'application_role_id' => [
                'required',
                'int'
            ],
            'team_type' => [
                'required',
                'int',
                Rule::in(FourIRInitiativeTeamMember::TEAM_TYPES)
            ],
            'name' => [
                'required',
                'string'
            ],
            'file_path' => [
                'nullable',
                'string'
            ],
            'name_en' => [
                'nullable',
                'string'
            ],
            'organization' => [
                'nullable',
                'string'
            ],
            'email' => [
                'required',
                'email'
            ],
            'phone_number' => [
                'required',
                'string',
                'max:15',
                'min:6',
                Rule::unique('four_ir_initiative_team_members')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) use ($request) {
                        return $query->where('team_type', $request->input('team_type'))
                            ->whereNull('deleted_at');
                    }),
            ],
            'designation' => [
                'required',
                'string',
                'max:100',
                'min:2'
            ],
            'role_responsibility' => [
                'required',
                'string',
                'max:200',
                'min:2'
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
            "team_type" => 'nullable|int',
            'email' => 'nullable',
            'phone_number' => 'nullable',
            'role' => 'nullable',
            'designation' => 'nullable',
            'page' => 'nullable|int|gt:0',
            'page_size' => 'nullable|int|gt:0',
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
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function teamLaunchingDateValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $rules = [
            'four_ir_initiative_id' => [
                'required',
                'int',
                'exists:four_ir_initiatives,id,deleted_at,NULL'
            ],
            'team_type' => [
                'required',
                'int',
                Rule::in(FourIRInitiativeTeamMember::TEAM_TYPES),
                function ($attr, $value, $failed) use ($data) {
                    $teamMember = FourIRInitiativeTeamMember::where('four_ir_initiative_id', $data['four_ir_initiative_id'])
                        ->where('team_type', $value)
                        ->first();
                    if (empty($teamMember)) {
                        if ($value == FourIRInitiativeTeamMember::IMPLEMENTING_TEAM_TYPE) {
                            $failed("At least one implementing team member should be registered for this Initiative!");
                        } else {
                            $failed("At least one expert team member should be registered for this Initiative!");
                        }
                    }
                }
            ],
            'launching_date' => [
                'required',
                'date_format:Y-m-d'
            ]
        ];
        return Validator::make($data, $rules);
    }

    /**
     * @param array $data
     * @return FourIRInitiative
     */
    public function addTeamLaunchingDate(array $data): FourIRInitiative
    {
        $initiative = FourIRInitiative::find($data['four_ir_initiative_id']);

        $payload = [];

        if ($data['team_type'] == FourIRInitiativeTeamMember::IMPLEMENTING_TEAM_TYPE) {
            $payload['implementing_team_launching_date'] = $data['launching_date'];
            if ($initiative->form_step < FourIRInitiative::FORM_STEP_IMPLEMENTING_TEAM) {
                $payload['form_step'] = FourIRInitiative::FORM_STEP_IMPLEMENTING_TEAM;
            }
        } else {
            $payload['expert_team_launching_date'] = $data['launching_date'];
            if ($initiative->form_step < FourIRInitiative::FORM_STEP_EXPERT_TEAM) {
                $payload['form_step'] = FourIRInitiative::FORM_STEP_EXPERT_TEAM;
            }
            if ($initiative->is_skill_provide == FourIRInitiative::SKILL_PROVIDE_TRUE && $initiative->completion_step < FourIRInitiative::COMPLETION_STEP_TWO) {
                $payload['completion_step'] = FourIRInitiative::COMPLETION_STEP_TWO;
            }
        }

        $initiative->fill($payload);
        $initiative->save();
        return $initiative;
    }
}
