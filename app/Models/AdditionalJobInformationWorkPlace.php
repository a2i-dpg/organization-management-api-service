<?php

namespace App\Models;

class AdditionalJobInformationWorkPlace extends BaseModel
{

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $appends = [
        "work_place_title"
    ];

    public function getWorkPlaceTitleAttribute(): string
    {
        return AdditionalJobInformation::WORK_PLACE[$this->work_place_id] ?? '';
    }

}
