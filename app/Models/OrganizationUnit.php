<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByOrganization;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Organization Unit
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string title
 * @property integer organization_id
 * @property integer organization_unit_type_id
 * @property string address
 * @property string mobile
 * @property string email
 * @property string fax_no
 * @property string contact_person_name
 * @property string contact_person_email
 * @property string contact_person_mobile
 * @property string contact_person_designation
 * @property integer employee_size
 * @property integer row_status
 * @property-read Organization organization
 * @property-read OrganizationUnitType organizationUnitType
 */
class OrganizationUnit extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait, ScopeFilterByOrganization;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;


    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;
    /**
     * @var mixed
     */

    /**
     * @return null
     */
    public function getHierarchy()
    {
        $topRoot = $this->humanResources->where('parent_id', null)->first();
        if (!$topRoot) {
            return null;
        }
        $topRoot->load('children');
        return $this->makeHierarchy($topRoot);
    }

    /**
     * @param $root
     * @return mixed
     */
    public function makeHierarchy($root)
    {
        $root['name'] = $root->title_en;
        $root['parent'] = $root->parent_id;
        $root['organization_title'] = $root->organization->title_en;
        $root['organization_unit_title'] = $root->organizationUnit->title_en;

        $children = $root->children;

        if (empty($children)) {
            return $root;
        }

        foreach ($children as $key => $child) {
            $root['children'][$key] = $child;
            $this->makeHierarchy($child);
        }
        return $root;
    }

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function organizationUnitType(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnitType::class);
    }

    /**
     * @return HasMany
     */
    public function humanResources(): HasMany
    {
        return $this->hasMany(HumanResource::class);
    }

    /**
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'organization_unit_services');
    }


}
