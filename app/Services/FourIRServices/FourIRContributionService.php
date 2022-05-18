<?php

namespace App\Services\FourIRServices;


use App\Models\FourIRContribution;
use App\Models\FourIRInitiativeTeamMember;
use App\Models\FourIRInitiativeTnaFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FourIRContributionService
{

    public function getList(array $filter)
    {

        $fourIrInitiativeId = $request['four_ir_initiative_id'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $userId = Auth::id();

        $fourIrContributionBuilder = FourIRInitiativeTeamMember::select([
            "four_ir_initiative_team_members.id as four_ir_initiative_team_member_id",
            "four_ir_contributions.id as four_ir_contribution_id",
            "four_ir_contributions.four_ir_initiative_id as four_ir_initiative_id",
            "four_ir_taglines.id as four_ir_tagline_id",
            "four_ir_taglines.name as four_ir_tagline_name",
            "four_ir_taglines.name_en as four_ir_tagline_name_en",
            "four_ir_initiatives.name as four_ir_tagline_name",
            "four_ir_initiatives.name_en as four_ir_tagline_name_en",
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
        $fourIrContributionBuilder->join("four_ir_contributions", "four_ir_contributions.four_ir_initiative_id", "four_ir_initiative_team_members.four_ir_initiative_id");
        $fourIrContributionBuilder->where("four_ir_initiative_team_members.user_id", $userId);
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
}
