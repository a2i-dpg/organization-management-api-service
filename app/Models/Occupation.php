<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Occupation
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int job_sector_id
 * @property int row_status
 * @property-read jobSector jobSector
 */
class Occupation extends Model
{
    public const ROW_STATUS_ACTIVE = '1';
    public const ROW_STATUS_INACTIVE = '0';
    public const ROW_STATUS_DELETED = '99';

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function jobSector(): BelongsTo
    {
        return $this->belongsTo(JobSector::class);
    }
}
