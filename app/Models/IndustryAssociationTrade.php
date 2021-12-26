<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IndustryAssociationTrade
 * @package App\Models
 * @property int id
 * @property string title
 * @property string title_en
 */
class IndustryAssociationTrade extends BaseModel
{
    use SoftDeletes;

    protected $table = 'industry_association_trades';
}
