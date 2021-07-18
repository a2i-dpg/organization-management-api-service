<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int organization_id
 * @property int organization_unit_id
 * @property int service_id
 **/

class OrganizationUnitService extends BaseModel
{
    use ScopeRowStatusTrait;
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

}
