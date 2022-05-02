<?php


namespace App\Services\FourIRServices;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\FourIRInitiativeTeamMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
    public function getFourIrProjectTeamMemberList(array $request, Carbon $startTime): array
    {
        $fourIRProjectId = $request['four_ir_initiative_id'] ?? "";
        $email = $request['email'] ?? "";
        $phoneNumber = $request['phone_number'] ?? "";
        $role = $request['role'] ?? "";
        $designation = $request['designation'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectTeamMemberBuilder */
        $fourIrProjectTeamMemberBuilder = FourIRInitiativeTeamMember::select(
            [
                'four_ir_initiative_team_members.id',
                'four_ir_initiative_team_members.four_ir_initiative_id',
                'four_ir_initiative_team_members.user_id',
                'four_ir_initiative_team_members.name',
                'four_ir_initiative_team_members.name_en',
                'four_ir_initiative_team_members.email',
                'four_ir_initiative_team_members.phone_number',
                'four_ir_initiative_team_members.role',
                'four_ir_initiative_team_members.designation',
                'four_ir_initiative_team_members.team_type',
                'four_ir_initiative_team_members.row_status',
                'four_ir_initiative_team_members.created_by',
                'four_ir_initiative_team_members.updated_by',
                'four_ir_initiative_team_members.created_at',
                'four_ir_initiative_team_members.updated_at'
            ]
        )->acl();

        $fourIrProjectTeamMemberBuilder->orderBy('four_ir_initiative_team_members.id', $order);

        if (!empty($fourIRProjectId)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_initiative_team_members.four_ir_initiative_id', $fourIRProjectId);
        }
        if (!empty($email)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_initiative_team_members.email', 'like', '%' . $email . '%');
        }

        if (!empty($phoneNumber)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_initiative_team_members.phone_number', 'like', '%' . $phoneNumber . '%');
        }

        if (!empty($role)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_initiative_team_members.role', 'like', '%' . $role . '%');
        }

        if (!empty($designation)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_initiative_team_members.designation', 'like', '%' . $designation . '%');
        }

        if (is_numeric($rowStatus)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_initiative_team_members.row_status', $rowStatus);
        }

        /** @var Collection $fourIrProjectTeamMembers */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrProjectTeamMembers = $fourIrProjectTeamMemberBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjectTeamMembers->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrProjectTeamMembers = $fourIrProjectTeamMemberBuilder->get();
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
    public function getOneFourIrProjectTeamMember(int $id): FourIRInitiativeTeamMember
    {
        /** @var FourIRInitiativeTeamMember|Builder $fourIrProjectTeamMemberBuilder */
        $fourIrProjectTeamMemberBuilder = FourIRInitiativeTeamMember::select(
            [
                'four_ir_initiative_team_members.id',
                'four_ir_initiative_team_members.four_ir_initiative_id',
                'four_ir_initiative_team_members.user_id',
                'four_ir_initiative_team_members.name',
                'four_ir_initiative_team_members.name_en',
                'four_ir_initiative_team_members.email',
                'four_ir_initiative_team_members.phone_number',
                'four_ir_initiative_team_members.role',
                'four_ir_initiative_team_members.designation',
                'four_ir_initiative_team_members.team_type',
                'four_ir_initiative_team_members.row_status',
                'four_ir_initiative_team_members.created_by',
                'four_ir_initiative_team_members.updated_by',
                'four_ir_initiative_team_members.created_at',
                'four_ir_initiative_team_members.updated_at'
            ]
        );
        $fourIrProjectTeamMemberBuilder->where('four_ir_initiative_team_members.id', $id);

        return $fourIrProjectTeamMemberBuilder->firstOrFail();
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

        return $fourIrProjectTeamMember;

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
            "name" => $data['name'],
            "name_en" => $data['name_en'] ?? "",
            "email" => $data['email'],
            "mobile" => $data['phone_number'],
            "password" => BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD,
            "password_confirmation" => BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD,
            "row_status" => BaseModel::ROW_STATUS_INACTIVE
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
        $fourIrProjectTeamMember->fill($data);
        $fourIrProjectTeamMember->save();
        return $fourIrProjectTeamMember;
    }

    /**
     * @param FourIRInitiativeTeamMember $fourIrProjectTeamMember
     * @return bool
     */
    public function destroy(FourIRInitiativeTeamMember $fourIrProjectTeamMember): bool
    {
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
            'team_type' => [
                'required',
                'int',
                Rule::in(FourIRInitiativeTeamMember::TEAM_TYPES),
/*                function ($attr, $value, $failed) use ($request) {
                    if($value == FourIRInitiativeTeamMember::IMPLEMENTING_TEAM_TYPE){
                        $guideline = FourIRGuideline::where('four_ir_initiative_id', $request->input('four_ir_initiative_id'))->first();
                        if(empty($guideline)){
                            $failed('Complete Guideline step first.[24000]');
                        }
                    } else if($value == FourIRInitiativeTeamMember::EXPERT_TEAM_TYPE) {
                        $implementingTeam = FourIRInitiativeTeamMember::where('four_ir_initiative_id', $request->input('four_ir_initiative_id'))
                            ->where('team_type', FourIRInitiativeTeamMember::IMPLEMENTING_TEAM_TYPE)
                            ->first();
                        if(empty($implementingTeam)){
                            $failed('Complete Implementing step first.[24000]');
                        }
                    }
                }*/
            ],
            'name' => [
                'required',
                'string'
            ],
            'name_en' => [
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
                    ->where(function (\Illuminate\Database\Query\Builder $query) use($request) {
                        return $query->where('team_type',$request->input('team_type'))
                              ->whereNull('deleted_at');
                }),
            ],
            'role' => [
                'required',
                'string',
                'max:200',
                'min:2'
            ],
            'designation' => [
                'required',
                'string',
                'max:100',
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
}
