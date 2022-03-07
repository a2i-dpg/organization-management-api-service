<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipType extends BaseModel
{
    public $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    public const PAYMENT_NATURE_DATE_WISE_KEY = 1;
    public const PAYMENT_NATURE_SESSION_WISE_KEY = 2;
    public const PAYMENT_NATURE = [
        self::PAYMENT_NATURE_DATE_WISE_KEY => "Date Wise Payment",
        self::PAYMENT_NATURE_SESSION_WISE_KEY => "Session Wise Payment",
    ];

    public const PAYMENT_FREQUENCY_MONTHLY_KEY = 1;
    public const PAYMENT_FREQUENCY_QUARTERLY_KEY = 2;
    public const PAYMENT_FREQUENCY_HALF_YEARLY_KEY = 3;
    public const PAYMENT_FREQUENCY_YEARLY_KEY = 4;
    public const PAYMENT_FREQUENCY_SESSIONAL_KEY = 5;

    public const PAYMENT_FREQUENCY = [
        self::PAYMENT_FREQUENCY_MONTHLY_KEY => 'Monthly Subscription',
        self::PAYMENT_FREQUENCY_QUARTERLY_KEY => 'Quarterly Subscription',
        self::PAYMENT_FREQUENCY_HALF_YEARLY_KEY => 'Half Yearly Subscription',
        self::PAYMENT_FREQUENCY_YEARLY_KEY => 'Yearly Subscription',
        self::PAYMENT_FREQUENCY_SESSIONAL_KEY => 'Session Subscription'
    ];


}
