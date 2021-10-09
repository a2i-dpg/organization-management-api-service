<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;


/**
 * Class JobSector
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property-read  Collection children
 */

class JobSector extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    public function occupations():HasMany
    {
        return $this->hasMany(Occupation::class);
    }
}
