<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $accessor_type
 * @property int $accessor_id
 */
class FourIRInitiativeCsCurriculumCblm extends BaseModel
{
    use SoftDeletes, ScopeAcl, ScopeRowStatusTrait, CreatedUpdatedBy;

    protected $table = 'four_ir_initiative_cs_curriculum_cblm';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    public const TYPE_CS = 1;
    public const TYPE_CURRICULUM = 2;
    public const TYPE_CBLM = 3;
    public const TYPES = [
        self::TYPE_CS,
        self::TYPE_CURRICULUM,
        self::TYPE_CBLM
    ];

    public const LEVELS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    public const APPROVED_BY_NSDA = 1;
    public const APPROVED_BY_BTEB = 2;
    public const APPROVED_BYS = [
        self::APPROVED_BY_NSDA,
        self::APPROVED_BY_BTEB
    ];

    /**
     * @return HasMany
     */
    public function experts(): HasMany
    {
        return $this->hasMany(FourIrCsCurriculumCblmExpert::class, 'four_ir_initiative_cs_curriculum_cblm_id', 'id');
    }
}
