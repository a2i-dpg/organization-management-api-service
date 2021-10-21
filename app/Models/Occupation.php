<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Occupation
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int job_sector_id
 * @property int row_status
 * @property-read jobSector jobSector
 */
class Occupation extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     * @return BelongsTo
     */
    public function jobSector(): BelongsTo
    {
        return $this->belongsTo(JobSector::class);
    }
}
