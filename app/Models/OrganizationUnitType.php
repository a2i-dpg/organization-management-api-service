<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrganizationUnitType
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property-read int organization_id
 * @property-read HumanResourceTemplate humanResourceTemplate
 * @property int row_status
 * @property-read Organization $organization
 * */
class OrganizationUnitType extends BaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

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
    public function humanResourceTemplate(): HasMany
    {
        return $this->hasMany(HumanResourceTemplate::class);
    }


    public function getHierarchy()
    {
        $topRoot = $this->humanResourceTemplate->where('parent_id', null)->first();
        if (!$topRoot) {
            return null;
        }
        $topRoot->load('children');

        return $this->makeHierarchy($topRoot);
    }

    public function makeHierarchy($root)
    {
        $root['name'] = $root->title_en;
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
