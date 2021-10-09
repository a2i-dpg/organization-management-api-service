<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Organization
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string title
 * @property string address
 * @property string mobile
 * @property string email
 * @property string|null fax_no
 * @property int|null loc_district_id
 * @property int|null loc_division_id
 * @property int|null loc_upazila_id
 * @property string contact_person_name
 * @property string contact_person_mobile
 * @property string contact_person_email
 * @property string contact_person_designation
 * @property string|null description
 * @property string logo
 * @property string domain
 * @property int organization_type_id
 * @property-read OrganizationType organizationType
 */
class Organization extends BaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }

    public function organizationUnitTypes(): HasMany
    {
        return $this->hasMany(OrganizationUnitType::class, 'organization_id');
    }

    public function organizationUnits(): HasMany
    {
        return $this->hasMany(OrganizationUnit::class, 'organization_id');
    }

    public function rankTypes(): HasMany
    {
        return $this->hasMany(RankType::class, 'organization_id');
    }
}
