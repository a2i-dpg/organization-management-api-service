<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByOrganization;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrganizationUnitType
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property-read int organization_id
 * @property-read HumanResourceTemplate humanResourceTemplate
 * @property int row_status
 * @property-read Organization $organization
 * */
class OrganizationUnitType extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait, ScopeFilterByOrganization;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return HasMany
     */
    public function humanResourceTemplates(): HasMany
    {
        return $this->hasMany(HumanResourceTemplate::class, 'organization_unit_type_id');
    }

    /**
     * @return HasMany
     */
    public function organizationUnits(): HasMany
    {
        return $this->hasMany(OrganizationUnit::class, 'organization_unit_type_id');
    }


    /**
     * @return null
     */
    public function getHierarchy()
    {
        $topRoot = $this->humanResourceTemplate->where('parent_id', null)->first();

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
        $root['parent'] = $root->parent_id;
        $root['organization_title'] = $root->organization->title_en;
        $root['organization_unit_type_title'] = $root->organizationUnitType->title_en;

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

}
