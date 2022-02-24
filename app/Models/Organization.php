<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Collection\Collection;
use function React\Promise\all;

/**
 * Class Organization
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string title
 * @property int industry_association_id
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
 * @property-read Collection industrySubTrades
 * @property Collection industryAssociations
 */
class Organization extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;


    public const ROW_STATUSES = [
        self::ROW_STATUS_INACTIVE,
        self::ROW_STATUS_ACTIVE, /** Approved Status */
        self::ROW_STATUS_PENDING,
        self::ROW_STATUS_REJECTED
    ];

    public const IS_REG_APPROVAL_TRUE = 1;
    public const IS_REG_APPROVAL_FALSE = 0;

    public const INDUSTRY_CODE_PREFIX = "IND";
    public const INDUSTRY_CODE_SIZE = 11;

    public const ORGANIZATION_TYPE_GOVT = 1;
    public const ORGANIZATION_TYPE_PRIVATE = 2;
    public const ORGANIZATION_TYPE_NGO = 3;
    public const ORGANIZATION_TYPE_INTERNATIONAL = 4;

    public const ORGANIZATION_TYPE = [
        self::ORGANIZATION_TYPE_GOVT,
        self::ORGANIZATION_TYPE_PRIVATE,
        self:: ORGANIZATION_TYPE_NGO,
        self::ORGANIZATION_TYPE_INTERNATIONAL
    ];

    public const PAYMENT_SUCCESS = 1;
    public const PAYMENT_PENDING = 2;
    public const PAYMENT_CANCEL = 3;
    public const PAYMENT_FAIL = 4;

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

    /**
     * @return BelongsToMany
     */
    public function industryAssociations(): BelongsToMany
    {
        return $this->belongsToMany(IndustryAssociation::class, 'industry_association_organization', 'organization_id', 'industry_association_id')->withPivot('id', 'membership_id', 'row_status', 'payment_status', 'payment_date', 'renewal_date')->withTimestamps();
    }

    public function subTrades(): BelongsToMany
    {
        return $this->belongsToMany(SubTrade::class, 'organization_sub_trade', 'organization_id', 'sub_trade_id');
    }

    /**public function toArray(): \Illuminate\Database\Eloquent\Collection|array
     * {
     * $originalData = parent::toArray();
     *
     * if (!empty($originalData['additional_info_model_name']) && class_exists($originalData['additional_info_model_name'])) {
     * $model = app($originalData['additional_info_model_name']);
     * $modelData = $model->where('organization_id', $originalData['id'])->firstOrFail()->makeHidden(['id', 'organization_id'])->toArray();
     * if ($modelData) {
     * $originalData = array_merge($originalData, $modelData);
     * }
     * }
     * return $originalData;
     * }*/
}
