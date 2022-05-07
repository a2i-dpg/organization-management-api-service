<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $user_id
 */
class FourIRInitiativeTeamMember extends BaseModel
{

    use softDeletes, ScopeAcl, ScopeRowStatusTrait, CreatedUpdatedBy;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    protected $table = 'four_ir_initiative_team_members';

    public const IMPLEMENTING_TEAM_TYPE  = 1;
    public const EXPERT_TEAM_TYPE  = 2;

    public const TEAM_TYPES = [
        self::IMPLEMENTING_TEAM_TYPE,
        self::EXPERT_TEAM_TYPE,
    ];
}
