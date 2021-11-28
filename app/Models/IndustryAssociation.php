<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IndustryAssociation
 * @package App\Models
 * @property int id
 * @property int industry_association_type_id
 * @property string title
 * @property string title_en
 * @property int|null loc_district_id
 * @property int|null loc_division_id
 * @property int|null loc_upazila_id
 * @property string|null location_latitude
 * @property string|null location_longitude
 * @property string|null google_map_src
 * @property string address
 * @property string address_en
 * @property string country
 * @property string phone_code
 * @property string|null mobile
 * @property string|null email
 * @property string|null name_of_the_office_head
 * @property string|null name_of_the_office_head_en
 * @property string|null name_of_the_office_head_designation
 * @property string|null name_of_the_office_head_designation_en
 * @property string contact_person_name
 * @property string contact_person_name_en
 * @property string contact_person_mobile
 * @property string contact_person_email
 * @property string contact_person_designation
 * @property string contact_person_designation_en
 * @property string logo
 * @property string domain
 * @property int row_status
 * @property int created_by
 * @property int updated_by
 * @property int created_at
 * @property int updated_at
 */
class IndustryAssociation extends BaseModel
{
    use ScopeRowStatusTrait, SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;


    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    public const ROW_STATUS_PENDING = 2;
    public const ROW_STATUS_REJECTED = 3;


    public const INDUSTRY_ASSOCIATION_TYPE_GOVT = 1;
    public const INDUSTRY_ASSOCIATION_TYPE_NON_GOVT = 2;
    public const INDUSTRY_ASSOCIATION_TYPE_OTHERS = 3;


    public const INDUSTRY_ASSOCIATION_TYPES = [
        self::INDUSTRY_ASSOCIATION_TYPE_GOVT,
        self::INDUSTRY_ASSOCIATION_TYPE_NON_GOVT,
        self::INDUSTRY_ASSOCIATION_TYPE_OTHERS

    ];


    public const ROW_STATUSES = [
        self::ROW_STATUS_INACTIVE,
        self::ROW_STATUS_ACTIVE, /** Approved Status */
        self::ROW_STATUS_PENDING,
        self::ROW_STATUS_REJECTED
    ];


    /**
     * @return BelongsToMany
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'industry_association_organization');
    }


}
