<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FourIRProjectCell extends BaseModel
{
    protected  $table ="four_ir_project_cells";
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;
}
