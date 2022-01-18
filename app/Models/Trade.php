<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Trade
 * @package App\Models
 * @property int id
 * @property string title
 * @property string title_en
 */
class Trade extends BaseModel
{
    use SoftDeletes;

    protected $table = 'trades';
}
