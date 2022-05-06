<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 */
class FourIRInitiativeTnaFormat extends BaseModel
{
    use SoftDeletes, ScopeAcl, ScopeRowStatusTrait, CreatedUpdatedBy;

    protected $table = 'four_ir_initiative_tna_formats';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    public const WORKSHOP_TNA_METHOD = 1;
    public const FGD_WORKSHOP_TNA_METHOD = 2;
    public const INDUSTRY_VISIT_TNA_METHOD = 3;
    public const DESKTOP_RESEARCH_TNA_METHOD = 4;
    public const EXISTING_REPORT_VIEW_TNA_METHOD = 5;
    public const OTHERS_TNA_METHOD = 6;
    public const TNA_METHODS_WORKSHOP_NUMBER_KEYS = [
        self::WORKSHOP_TNA_METHOD => "workshop_method_workshop_numbers",
        self::FGD_WORKSHOP_TNA_METHOD => "fgd_workshop_numbers",
        self::INDUSTRY_VISIT_TNA_METHOD => "industry_visit_workshop_numbers",
        self::DESKTOP_RESEARCH_TNA_METHOD => "desktop_research_workshop_numbers",
        self::EXISTING_REPORT_VIEW_TNA_METHOD => "existing_report_review_workshop_numbers",
        self::OTHERS_TNA_METHOD => "others_workshop_numbers",
    ];
}
