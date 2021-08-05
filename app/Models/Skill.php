<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Scopes\ScopeRowStatusTrait;
/**
 * Class Skill
 * @package App\Models
 * @property int|null organization_id
 * @property string title_en
 * @property string title_bn
 * @property int | null description
 * @property int row_status
 * @property-read Organization organization
 */
class Skill extends BaseModel
{
    use ScopeRowStatusTrait;
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
