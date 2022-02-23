<?php

namespace App\Models;


class AdditionalJobInformationJobLevel extends BaseModel
{

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $appends = [
        "job_level_title"
    ];

    public function getJobLevelTitleAttribute(): string
    {
        return AdditionalJobInformation::JOB_PLACE_TYPE[$this->job_level_id] ?? '';
    }
}
