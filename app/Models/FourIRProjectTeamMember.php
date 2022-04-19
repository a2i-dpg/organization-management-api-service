<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRProjectTeamMember extends BaseModel
{

    use softDeletes, CreatedUpdatedBy;

    protected $guarded = ['id'];

    protected $table = 'four_ir_project_team_members';

    public const IMPLEMENTING_TEAM_TYPE  = 1;
    public const MENTORING_TEAM_TYPE  = 2;

    public const TEAM_TYPES = [
        self::IMPLEMENTING_TEAM_TYPE,
        self::MENTORING_TEAM_TYPE,
    ];
}
