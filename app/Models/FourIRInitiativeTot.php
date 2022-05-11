<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 */
class FourIRInitiativeTot extends Model
{
    use SoftDeletes, ScopeAcl, ScopeRowStatusTrait,CreatedUpdatedBy;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    protected $table = 'four_ir_initiative_tots';

    public const TYPE_ORGANIZER = 1;
    public const TYPE_CO_ORGANIZER = 2;
    public const TYPE_PARTICIPANT = 3;

    /**
     * @return HasMany
     */
    public function organizerParticipants(): HasMany
    {
        return $this->hasMany(FourIRInitiativeTotMastersTrainersParticipant::class, 'four_ir_initiative_tot_id', 'id');
    }
}
