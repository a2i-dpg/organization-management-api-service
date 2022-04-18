<?php


namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIrProject;
use App\Models\FourIRProjectTeamMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class FourIrProjectTeamMemberService
 * @package App\Services\FourIRServices
 */
class FourIrProjectTeamMemberService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getFourIrProjectTeamMemberList(array $request, Carbon $startTime): array
    {
        $fourIRProjectId = $request['four_ir_project_id'] ?? "";
        $email = $request['email'] ?? "";
        $phoneNumber = $request['phone_number'] ?? "";
        $role = $request['role'] ?? "";
        $designation = $request['designation'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Builder $fourIrProjectTeamMemberBuilder */
        $fourIrProjectTeamMemberBuilder = FourIRProjectTeamMember::select(
            [
                'four_ir_project_team_members.id',
                'four_ir_project_team_members.four_ir_project_id',
                'four_ir_project_team_members.email',
                'four_ir_project_team_members.phone_number',
                'four_ir_project_team_members.role',
                'four_ir_project_team_members.designation',
                'four_ir_project_team_members.team_type',
                'four_ir_project_team_members.row_status',
                'four_ir_project_team_members.created_by',
                'four_ir_project_team_members.updated_by',
                'four_ir_project_team_members.created_at',
                'four_ir_project_team_members.updated_at'
            ]
        )->acl();

        $fourIrProjectTeamMemberBuilder->orderBy('four_ir_project_team_members.id', $order);

        if (!empty($fourIRProjectId)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_project_team_members.four_ir_project_id', $fourIRProjectId);
        }
        if (!empty($email)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_project_team_members.email', 'like', '%' . $email . '%');
        }

        if (!empty($phoneNumber)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_project_team_members.phone_number', 'like', '%' . $phoneNumber . '%');
        }

        if (!empty($role)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_project_team_members.role', 'like', '%' . $role . '%');
        }

        if (!empty($designation)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_project_team_members.designation', 'like', '%' . $designation . '%');
        }

        if (is_numeric($rowStatus)) {
            $fourIrProjectTeamMemberBuilder->where('four_ir_project_team_members.row_status', $rowStatus);
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
     * @return FourIRProjectTeamMember
     */
    public function getOneFourIrProjectTeamMember(int $id): FourIRProjectTeamMember
    {
        /** @var FourIRProjectTeamMember|Builder $fourIrProjectTeamMemberBuilder */
        $fourIrProjectTeamMemberBuilder = FourIRProjectTeamMember::select(
            [
                'four_ir_project_team_members.id',
                'four_ir_project_team_members.four_ir_project_id',
                'four_ir_project_team_members.email',
                'four_ir_project_team_members.phone_number',
                'four_ir_project_team_members.role',
                'four_ir_project_team_members.designation',
                'four_ir_project_team_members.team_type',
                'four_ir_project_team_members.row_status',
                'four_ir_project_team_members.created_by',
                'four_ir_project_team_members.updated_by',
                'four_ir_project_team_members.created_at',
                'four_ir_project_team_members.updated_at'
            ]
        );
        $fourIrProjectTeamMemberBuilder->where('four_ir_project_team_members.id', $id);

        return $fourIrProjectTeamMemberBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return FourIRProjectTeamMember
     */
    public function store(array $data): FourIRProjectTeamMember
    {
        $fourIrProjectTeamMember = app()->make(FourIRProjectTeamMember::class);
        $fourIrProjectTeamMember->fill($data);
        $fourIrProjectTeamMember->save();
        return $fourIrProjectTeamMember;
    }

    /**
     * @param FourIRProjectTeamMember $fourIrProjectTeamMember
     * @param array $data
     * @return FourIRProjectTeamMember
     */
    public function update(FourIRProjectTeamMember $fourIrProjectTeamMember, array $data): FourIRProjectTeamMember
    {
        $fourIrProjectTeamMember->fill($data);
        $fourIrProjectTeamMember->save();
        return $fourIrProjectTeamMember;
    }

    /**
     * @param FourIRProjectTeamMember $fourIrProjectTeamMember
     * @return bool
     */
    public function destroy(FourIRProjectTeamMember $fourIrProjectTeamMember): bool
    {
        return $fourIrProjectTeamMember->delete();
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
            'four_ir_project_id' => [
                'required',
                'int'
            ],
            'team_type' => [
                'required',
                'int'
            ],
            'email' => [
                'required',
                'string'
            ],
            'phone_number' => [
                'required',
                'string',
                'max:15',
                'min:6'
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
            'four_ir_project_id' => 'required|int',
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
