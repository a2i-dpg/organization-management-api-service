<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;

/**
 * Class Course
 * @package App\Models
 * @property string|null title_en
 * @property string|null title_bn
 * @property string code
 * @property int institute_id
 * @property double course_fee
 * @property string course_duration
 * @property string target_group
 * @property string course_objects
 * @property string course_contents
 * @property string training_methodology
 * @property string evaluation_system
 * @property string description
 * @property string prerequisite
 * @property string eligibility
 * @property File cover_image
 * @method static \Illuminate\Database\Eloquent\Builder|Institute acl()
 * @method static Builder|Institute active()
 * @method static Builder|Institute newModelQuery()
 * @method static Builder|Institute newQuery()
 * @method static Builder|Institute query()
 */

class Course extends BaseModel
{
    use \App\Traits\Scopes\ScopeRowStatusTrait;

    protected $table = 'courses';
    protected $guarded = ['id'];
    const DEFAULT_COVER_IMAGE = 'course/course.jpeg';

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function courseSessions(): HasMany
    {
        return $this->hasMany(CourseSession::class,'course_id','id');
    }

    public function publishCourses(): HasMany
    {
        return $this->hasMany(PublishCourse::class);
    }
}
