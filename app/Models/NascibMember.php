<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IndustryAssociation
 * @package App\Models
 * @property int id
 * @property string code
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
class NascibMember extends BaseModel
{

    protected $table = 'nascib_members';
    use ScopeRowStatusTrait;

    protected $guarded = ['id'];
    public $timestamps = false;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    public const ROW_STATUS_PENDING = 2;
    public const ROW_STATUS_REJECTED = 3;

    public const FORM_FILL_UP_BY_OWN = 1;
    public const FORM_FILL_UP_BY_UDC_ENTREPRENEUR = 2;
    public const FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION = 3;

    public const FORM_FILL_UP_LIST = [
        self::FORM_FILL_UP_BY_OWN => "Form fill up by own",
        self::FORM_FILL_UP_BY_UDC_ENTREPRENEUR => "Form fill up by UDC Entrepreneur",
        self::FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION => "Form fill up by Chamber or Association"
    ];

    public const STATUS_INITIAL_STATE = 0;
    public const STATUS_ACCEPT = 1;
    public const STATUS_REJECTED = 2;


    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;
    public const GENDER_OTHERS = 3;


    public const PROPRIETORSHIP_SOLE_PROPRIETORSHIP = 1;
    public const PROPRIETORSHIP_PARTNERSHIP_PROPRIETORSHIP = 2;
    public const PROPRIETORSHIP_JOIN_PROPRIETORSHIP = 3;

    public const PROPRIETORSHIP_LIST = [
        self::PROPRIETORSHIP_SOLE_PROPRIETORSHIP => 'Sole Proprietorship (একক মালিকানা)',
        self::PROPRIETORSHIP_PARTNERSHIP_PROPRIETORSHIP => 'Partnership Proprietorship (অংশীদারি মালিকানা)',
        self::PROPRIETORSHIP_JOIN_PROPRIETORSHIP => 'Join Proprietorship (যৌথ মালিকানা)',
    ];

    public const TRADE_LICENSING_AUTHORITY_CITY_CORPORATION_KEY = 1;
    public const TRADE_LICENSING_AUTHORITY_MUNICIPALITY = 2;
    public const TRADE_LICENSING_AUTHORITY_UNION_COUNCIL = 3;

    public const TRADE_LICENSING_AUTHORITY = [
        self::TRADE_LICENSING_AUTHORITY_CITY_CORPORATION_KEY => 'City Corporation (সিটি কর্পোরেশন)',
        self::TRADE_LICENSING_AUTHORITY_MUNICIPALITY => 'Municipality (পৌরসভা)',
        self::TRADE_LICENSING_AUTHORITY_UNION_COUNCIL => 'Union Council (ইউনিয়ন পরিষদ)',
    ];
    const OTHER_SECTOR_KEY = "other_sector";
    public const SECTOR = [
        1 => "পাট ও পাটজাত (পণ্য উৎপাদন ও প্রতিোজাতকরণ)",
        2 => "চামড়া ও চামড়াজাত (পণ্য উৎপাদন ও প্রতিোজাতকরণ)",
        3 => "প্লাতিক, রাোর ও তসনঝথটিক (পণ্য উৎপাদন ও প্রতিোজাতকরণ)",
        4 => "কাগজ ও কাগজজাত (পণ্য উৎপাদন ও প্রতিোজাতকরণ)",
        5 => "রাসাশিনক ও রাসাশিনকজাত (পণ্য উৎপাদন ও প্রতিোজাতকরণ)",
        6 => "মিৌশলক ফাি মাশসউটিকযাল প্রস্তুত এবাং ওষুধ (পণ্য উৎপাদন ও প্রতিোজাতকরণ)",
        7 => "ইঝেতিকযাে ও ইঝেিতনক (পণ্য উৎপাদন ও প্রতিোজাতকরণ)",
        8 => "যন্ত্রপাশত, যন্ত্রাাংি ও সরঞ্জাদির মিরািত ও স্থাপন(কশিউটার এবাং ব্যশক্তগত ও গৃহস্থাশল দ্রব্যাশে)",
        9 => "কতিউটার",
        10 => "দৃশি/চিিা উৎপােন কায াশে",
        11 => "হােকা প্রঝকৌশে (োইট ইতিতনোতরিং)",
        12 => "আসোেপত্র উৎপাদন",
        13 => "হস্ত ও কুটির তশল্প",
        14 => "হস্ততশল্প (অন্যান্য)",
        15 => "ফ্যাশন ও তিজাইন",
        16 => "ততরী নপাশাক তশল্প (ক্ষুদ্র ও মাোরী)",
        17 => "কৃতি ও কৃতিজাত পন্য উৎপাদন এেিং প্রতিোজাতকরণ",
        18 => "তথ্য ও ন াগাঝ াগ",
        19 => "নপশাগত, তেজ্ঞাতনক এেিং কাতরগতর কা িম (কনসােটযাতন্স)",
        20 => "জনস্বাস্থ্য এেিং সামাতজক কা িম",
        21 => "খাদ্য ও পানীে",
        22 => "আোসন ও খাদ্য নসো কা িম",
        23 => "কোতেদ্যা, আপ্যােন এেিং তেঝনাদন",
        24 => "পণ্য আমদাতন, রপ্তাতন ও সরেরাহকারী (শুধু)",
        25 => "পাইকাতর ও খুচরা ব্যেসা (নহােঝসে এেিং তরঝটইে)",
        self::OTHER_SECTOR_KEY => "অন্যান্য"
    ];

    public const REGISTERED_AUTHORITY = [
        1 => 'জয়েন্ট-স্টক কোম্পানি',
        2 => 'সমবায় অধিদপ্তর',
        3 => 'এনজিও বিষয়ক ব্যুরো',
        4 => 'সমাজসেবা অধিদপ্তর',
        5 => 'মহিলা বিষয়ক অধিদপ্তর',
        6 => 'যুব উন্নয়ন অধিদপ্তর',
        7 => 'বিটিআরসি',
        8 => 'স্বাস্থ্য অধিদপ্তর',
        9 => 'বাংলাদেশ ক্ষুদ্র ও কুটির শিল্প কর্পোরেশন(বিসিক)',
    ];
    const OTHER_AUTHORITY_KEY = 'other_authority';
    public const AUTHORIZED_AUTHORITY = [
        1 => 'কলকারখানা ও প্রতিষ্ঠান পরিদর্শন অধিদপ্তর',
        2 => 'বাংলাদেশ পরিবেশ অধিদপ্তর',
        3 => 'বাংলাদেশ ফায়ার সার্ভিস ও সিভিল ডিফেন্স অধিদপ্তর',
        4 => 'আমদানি ও রপ্তানি প্রধান নিয়ন্ত্রকের অধিদপ্তর',
        5 => 'ঔষধ প্রশাসন অধিদপ্তর',
        6 => 'বাংলাদেশ এনার্জি রেগুলেটরি কমিশন',
        7 => 'বিস্ফোরক পরিদপ্তর',
        self::OTHER_AUTHORITY_KEY => 'অন্যন্য'
    ];

    public const FACTORY_CATEGORIES = [
        1 => "কুটির শিল্প (র্সবােচ্চ জনবল ১৫)",
        2 => "মাইক্রো শিল্প-(ম্যানুফেকচারিং- ১৬-৩০ জন বা তার চেয়েকম সংখ্যক শ্রমিক)",
        3 => "মাইক্রো শিল্প-(সেবা- সর্বোচ্চ১৫ জন)",
        4 => "ক্ষুদ্রশিল্প-(ম্যানুফেকচারিং- ৩১-১২০জন)",
        5 => "ক্ষুদ্র শিল্প-(সবো-  ১৬-৫০জন )",
        6 => "মাঝারি শিল্প- (ম্যানুফেকচারিং- ১২১-৩০০ জন (তৈরী পোষাক র্সবােচ্চ ১০০০ জন))",
        7 => "মাঝারি শিল্প- (সেবা- ৫১-১২০ জন)"
    ];

    public const SPECIALIZED_AREA = [
        1 => 'বাংলাদেশ রপ্তানি প্রক্রিয়াকরণ অঞ্চল (EPZ)',
        2 => 'বাংলাদেশ অর্থনৈতিক অঞ্চল (BEZA)',
        3 => 'বিসিক শিল্প নগরী',
        4 => 'বেসরকারি ইকোনমিক জোন',
    ];

    public const IMPORT_EXPORT_TYPE = [
        1 => 'Direct',
        2 => 'InDirect'
    ];

    public const PERMANENT_WORKER_KEY = 'permanent_worker';
    public const TEMPORARY_WORKER_KEY = 'temporary_worker';
    public const SEASONAL_WORKER_KEY = 'seasonal_worker';
    public const WORKER_TYPE = [
        self::PERMANENT_WORKER_KEY => 'Permanent worker (স্থায়ী কর্মী)',
        self::TEMPORARY_WORKER_KEY => 'Temporary worker (অস্থায়ী কর্মী)',
        self::SEASONAL_WORKER_KEY => 'Seasonal worker (মৌসুমী কর্মী)'
    ];
    public const MANPOWER_TYPE_MALE = 'male';
    public const MANPOWER_TYPE_FEMALE = 'female';
    public const MANPOWER_TYPE = [
        self::MANPOWER_TYPE_MALE,
        self::MANPOWER_TYPE_FEMALE
    ];

    public const BANK_ACCOUNT_PERSONAL = 'personal';
    public const BANK_ACCOUNT_INDUSTRY = 'industry';
    public const BANK_ACCOUNT_TYPE = [
        self::BANK_ACCOUNT_PERSONAL => 'Personal account (ব্যক্তিগত হিসাব)',
        self::BANK_ACCOUNT_INDUSTRY => 'industry accounts (প্রতিষ্ঠানের হিসাব)'
    ];

    public const LAND_TYPE = [
        1 => 'Own Land',
        2 => 'Rent'
    ];
    public const BUSINESS_TYPE_MANUFACTURING = 1;
    public const BUSINESS_TYPE_SERVICE = 2;
    public const BUSINESS_TYPE_TRADING = 3;
    public const BUSINESS_TYPE = [
        self::BUSINESS_TYPE_MANUFACTURING => 'ম্যানুফ্যাকচারিং',
        self::BUSINESS_TYPE_SERVICE => 'সার্ভিস',
        self::BUSINESS_TYPE_TRADING => 'ট্রেডিং(দেশ পণ্য/বিদেশী বিদেশী)',
    ];

    public const YES_NO = [
        0 => 'না',
        1 => 'হ্যাঁ',
    ];

    public const APPLICATION_TYPE_NEW = "NEW_APPLICATION";
    public const APPLICATION_TYPE_RENEW = "RENEW_APPLICATION";
    public const APPLICATION_TYPE = [
        self::APPLICATION_TYPE_NEW => "নতুন",
        self::APPLICATION_TYPE_RENEW => "নবায়ন"
    ];

    public const PAYMENT_GATEWAY_PAGE_URL_PREFIX = "member-registration-payment-method";

    protected $casts = [
        'registered_authority' => 'array',
        'authorized_authority' => 'array',
        'specialized_area' => 'array',
        'salaried_manpower' => 'array',
        'export_type' => 'array',
        'import_type' => 'array',
        'bank_account_type' => 'array',
        'business_type_services' => 'array'
    ];

}
