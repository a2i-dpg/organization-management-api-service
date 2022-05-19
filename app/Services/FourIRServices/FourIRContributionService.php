<?php

namespace App\Services\FourIRServices;

use App\Models\BaseModel;
use App\Models\FourIRContribution;
use App\Models\FourIRInitiativeTeamMember;
use App\Models\FourIRInitiativeTnaFormat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class FourIRContributionService
{

    public function getContributionList(array $request): array
    {
        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";

        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $userId = $request['user_id'] ?? Auth::id();
        $response = [];

        Log::info("Filter Payload" . json_encode($request));

        $fourIrContributionBuilder = FourIRInitiativeTeamMember::select([
            "four_ir_initiative_team_members.id",
            "four_ir_initiative_team_members.role_responsibility",
            "four_ir_initiative_team_members.name as team_member_name",
            "four_ir_initiative_team_members.name_en as team_member_name_en",
            "four_ir_initiative_team_members.email as team_member_email",
            "four_ir_initiative_team_members.phone_number as team_member_phone_number",
            "four_ir_initiative_team_members.designation as team_member_designation",
            "four_ir_initiative_team_members.file_path as team_member_file_path",
            "four_ir_contributions.id as four_ir_contribution_id",
            "four_ir_initiative_team_members.four_ir_initiative_id as four_ir_initiative_id",
            "four_ir_contributions.four_ir_initiative_id as four_ir_contribution_initiative_id",
            "four_ir_taglines.id as four_ir_tagline_id",
            "four_ir_taglines.name as four_ir_tagline_name",
            "four_ir_taglines.name_en as four_ir_tagline_name_en",
            "four_ir_initiatives.name as four_ir_initiative_name",
            "four_ir_initiatives.name_en as four_ir_initiative_name_en",
            "four_ir_initiative_team_members.user_id",
            "four_ir_initiative_team_members.file_path",
            "four_ir_initiative_team_members.organization",
            "four_ir_initiative_team_members.team_type",
            "four_ir_contributions.contribution as contribution",
            "four_ir_contributions.contribution_en as contribution_en",
            'four_ir_contributions.row_status',
            'four_ir_contributions.created_by',
            'four_ir_contributions.updated_by',
            'four_ir_contributions.created_at',
            'four_ir_contributions.updated_at'
        ])->acl();

        $fourIrContributionBuilder->join("four_ir_initiatives", "four_ir_initiatives.id", "four_ir_initiative_team_members.four_ir_initiative_id");
        $fourIrContributionBuilder->join("four_ir_taglines", "four_ir_taglines.id", "four_ir_initiatives.four_ir_tagline_id");
        $fourIrContributionBuilder->leftJoin("four_ir_contributions", "four_ir_contributions.four_ir_initiative_id", "four_ir_initiative_team_members.four_ir_initiative_id");

        $fourIrContributionBuilder->where("four_ir_initiative_team_members.user_id", $userId);

        $fourIrContributionBuilder->orderBy('four_ir_initiative_team_members.id', $order);

        if (!empty($fourIrInitiativeId)) {
            $fourIrContributionBuilder->where('four_ir_initiative_team_members.four_ir_initiative_id', $fourIrInitiativeId);
        }

        if (is_numeric($rowStatus)) {
            $fourIrContributionBuilder->where('four_ir_contributions.row_status', $rowStatus);
        }
        Log::info("SQL-:   " . json_encode($fourIrContributionBuilder->toSql()));
        /** @var Collection $fourIrProjectTeamMembers */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $fourIrContributions = $fourIrContributionBuilder->paginate($pageSize);
            $paginateData = (object)$fourIrProjectTeamMembers->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $fourIrContributions = $fourIrContributionBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $fourIrContributions->toArray()['data'] ?? $fourIrContributions->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => 0
        ];

        return $response;
    }


    public function getOne(int $id): array
    {
        $fourIrContributionBuilder = FourIRInitiativeTeamMember::select([
            "four_ir_initiative_team_members.id",
            "four_ir_initiative_team_members.role_responsibility",
            "four_ir_initiative_team_members.name as team_member_name",
            "four_ir_initiative_team_members.name_en as team_member_name_en",
            "four_ir_initiative_team_members.email as team_member_email",
            "four_ir_initiative_team_members.phone_number as team_member_phone_number",
            "four_ir_initiative_team_members.designation as team_member_designation",
            "four_ir_contributions.id as four_ir_contribution_id",
            "four_ir_contributions.four_ir_initiative_id as four_ir_initiative_id",
            "four_ir_taglines.id as four_ir_tagline_id",
            "four_ir_taglines.name as four_ir_tagline_name",
            "four_ir_taglines.name_en as four_ir_tagline_name_en",
            "four_ir_initiatives.name as four_ir_tagline_name",
            "four_ir_initiatives.name_en as four_ir_tagline_name_en",
            "four_ir_contributions.contribution as contribution",
            "four_ir_contributions.contribution_en as contribution_en",
            "four_ir_initiative_team_members.user_id",
            "four_ir_initiative_team_members.file_path",
            "four_ir_initiative_team_members.organization",
            "four_ir_initiative_team_members.team_type",
            'four_ir_contributions.row_status',
            'four_ir_contributions.created_by',
            'four_ir_contributions.updated_by',
            'four_ir_contributions.created_at',
            'four_ir_contributions.updated_at'
        ]);

        $fourIrContributionBuilder->join("four_ir_initiatives", "four_ir_initiatives.id", "four_ir_initiative_team_members.four_ir_initiative_id");
        $fourIrContributionBuilder->join("four_ir_taglines", "four_ir_taglines.id", "four_ir_initiatives.four_ir_tagline_id");
        $fourIrContributionBuilder->leftJoin("four_ir_contributions", "four_ir_contributions.four_ir_initiative_id", "four_ir_initiative_team_members.four_ir_initiative_id");
        $fourIrContributionBuilder->where("four_ir_contributions.id", $id);
        return $fourIrContributionBuilder->firstOrFail()->toArray();
    }

    public function createOrUpdate(array $request)
    {
        return FourIRContribution::updateOrCreate(
            [
                "four_ir_initiative_id" => $request['four_ir_initiative_id'],
                "user_id" => $request['user_id'],
            ],
            $request);
    }

    public function valiation(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "four_ir_initiative_id" => [
                "required",
                "integer",
                'exists:four_ir_initiatives,id,deleted_at,NULL',
            ],
            "contribution" => [
                "required",
                "string"
            ]
        ];
        return Validator::make($request->all(), $rules);
    }

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
            'four_ir_initiative_id' => 'nullable|int',
            'user_id' => 'nullable|int',
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
