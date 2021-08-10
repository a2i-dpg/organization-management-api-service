<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Occupation
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int job_sector_id
 * @property int row_status
 * @property-read jobSector jobSector
 */
class Occupation extends BaseModel
{
    use SoftDeletes, HasFactory;

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
