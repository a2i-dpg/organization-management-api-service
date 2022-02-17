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
        self::FORM_FILL_UP_BY_OWN,
        self::FORM_FILL_UP_BY_UDC_ENTREPRENEUR,
        self::FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION,
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
    const OTHER_SECTOR_KEY = 1001;
    public const SECTOR = [
        1 => 'পাটজাত',
        2 => 'চামড়া',
        3 => 'হস্তশিল্প',
        4 => 'ফ্যাশন',
        5 => 'হালকা প্রকৌশল',
        6 => 'পোশাক',
        7 => 'ডিজাইন',
        8 => 'কৃষি প্রক্রিয়াজাত',
        9 => 'আইটি',
        10 => 'হারবাল',
        11 => 'প্লাস্তিক',
        12 => 'সিনথেটিকস',
        13 => 'ইলেক্ট্রিক্যাল',
        14 => 'ইলেক্ট্রনিক',
        15 => 'লাইট ইঞ্জিনিয়ারিং',
        16 => 'জুয়েলারি',
        17 => 'হস্তশিল্প (বিভিন্ন প্রকার)',
        18 => 'খাদ্য ও পানীয়',
        self::OTHER_SECTOR_KEY => 'অন্যন্য'
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

    public const AUTHORIZED_AUTHORITY = [
        1 => 'কলকারখানা ও প্রতিষ্ঠান পরিদর্শন অধিদপ্তর',
        2 => 'বাংলাদেশ পরিবেশ অধিদপ্তর',
        3 => 'বাংলাদেশ ফায়ার সার্ভিস ও সিভিল ডিফেন্স অধিদপ্তর',
        4 => 'আমদানি ও রপ্তানি প্রধান নিয়ন্ত্রকের অধিদপ্তর',
        5 => 'ঔষধ প্রশাসন অধিদপ্তর',
        6 => 'বাংলাদেশ এনার্জি রেগুলেটরি কমিশন',
        7 => 'বিস্ফোরক পরিদপ্তর',
        8 => 'অন্যন্য'
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

    public const MANPOWER_TYPE = [
        1 => 'Permanent worker (স্থায়ী কর্মী)',
        2 => 'Temporary worker (অস্থায়ী কর্মী)',
        3 => 'Seasonal worker (মৌসুমী কর্মী)'
    ];

    public const BANK_ACCOUNT_TYPE = [
        1 => 'Personal account (ব্যক্তিগত হিসাব)',
        2 => 'Organization accounts (প্রতিষ্ঠানের হিসাব)'
    ];

    public const LAND_TYPE = [
        1 => 'Own Land',
        2 => 'Rent'
    ];

    public const BUSINESS_TYPE = [
        1 => 'ম্যানুফ্যাকচারিং',
        2 => 'সার্ভিস',
        3 => 'ট্রেডিং(দেশ পণ্য/বিদেশী বিদেশী)',
    ];

    public const YES_NO = [
        0 => 'না',
        1 => 'হ্যাঁ',
    ];


    protected $casts = [
        'registered_authority' => 'array',
        'authorized_authority' => 'array',
        'specialized_area' => 'array',
        'salaried_manpower' => 'array',
        'export_type' => 'array',
        'import_type' => 'array',
        'bank_account_type' => 'array',
    ];

}
