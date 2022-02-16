<?php

namespace App\Services;

use App\Exceptions\HttpErrorException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\Organization;
use App\Models\NascibMember;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 *
 */
class NascibMemberService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getNascibMemberList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $tradeId = $request['trade_id'] ?? "";

        /** @var Builder organizationBuilder */
        $nascibMemberBuilder = NascibMember::select([
            'organization_ina000002.id',
            'organization_ina000002.organization_id',
            'organizations.title as organization_title',
            'organizations.title_en as organization_title_en',
            'organization_ina000002.application_tracking_no',
            'organization_ina000002.form_fill_up_by',
            'organization_ina000002.udc_name',
            'organization_ina000002.udc_loc_district',
            'organization_ina000002.udc_union',
            'organization_ina000002.udc_code',
            'organization_ina000002.chamber_or_association_name',
            'organization_ina000002.chamber_or_association_loc_district_id',
            'organization_ina000002.chamber_or_association_union',
            'organization_ina000002.chamber_or_association_code',
            'organization_ina000002.name',
            'organization_ina000002.name_bn',
            'organization_ina000002.gender',
            'organization_ina000002.date_of_birth',
            'organization_ina000002.educational_qualification',
            'organization_ina000002.nid',
            'organization_ina000002.nid_file',
            'organization_ina000002.mobile',
            'organization_ina000002.email',
            'organization_ina000002.entrepreneur_photo',
            'organization_ina000002.organization_trade_license_no',
            'organization_ina000002.organization_identification_no',
            'organization_ina000002.organization_name',
            'organization_ina000002.organization_address',
            'organization_ina000002.organization_loc_district_id',
            'organization_ina000002.organization_loc_upazila_id',
            'organization_ina000002.organization_domain',
            'organization_ina000002.factory',
            'organization_ina000002.factory_address',
            'organization_ina000002.factory_loc_district_id',
            'organization_ina000002.factory_loc_upazila_id ',
            'organization_ina000002.factory_web_site',
            'organization_ina000002.office_or_showroom',
            'organization_ina000002.factory_land_own_or_rent',
            'organization_ina000002.proprietorship',
            'organization_ina000002.industry_establishment_year',
            'organization_ina000002.trade_licensing_authority',
            'organization_ina000002.trade_license',
            'organization_ina000002.industry_last_renew_year',
            'organization_ina000002.tin',
            'organization_ina000002.investment_amount',
            'organization_ina000002.current_total_asset',
            'organization_ina000002.registered_under_authority',
            'organization_ina000002.registered_authority',
            'organization_ina000002.authorized_under_authority',
            'organization_ina000002.authorized_authority',
            'organization_ina000002.specialized_area',
            'organization_ina000002.specialized_area_name',
            'organization_ina000002.under_sme_cluster',
            'organization_ina000002.under_sme_cluster_name',
            'organization_ina000002.member_of_association_or_chamber',
            'organization_ina000002.member_of_association_or_chamber_name',
            'organization_ina000002.sector',
            'organization_ina000002.sector_other_name',
            'organization_ina000002.business_type',
            'organization_ina000002.main_product_name',
            'organization_ina000002.main_material_description',
            'organization_ina000002.import',
            'organization_ina000002.import_by',
            'organization_ina000002.export_abroad',
            'organization_ina000002.export_abroad_by',
            'organization_ina000002.industry_irc_no',
            'organization_ina000002.salaried_manpower',
            'organization_ina000002.have_bank_account',
            'organization_ina000002.bank_account_type',
            'organization_ina000002.accounting_system',
            'organization_ina000002.use_computer',
            'organization_ina000002.internet_connection',
            'organization_ina000002.online_business',
            'organization_ina000002.info_provider_name',
            'organization_ina000002.info_provider_mobile',
            'organization_ina000002.info_collector_name',
            'organization_ina000002.info_collector_mobile',
            'organization_ina000002.status',
            'organization_ina000002.row_status',
            'organization_ina000002.created_by',
            'organization_ina000002.updated_by',
            'organization_ina000002.created_at',
            'organization_ina000002.updated_at'
        ]);
        $nascibMemberBuilder->join('organizations', function ($join) {
            $join->on('organizations.id', '=', 'organization_ina000002.organization_id')
                ->whereNull('organizations.deleted_at');
        });

        $nascibMemberBuilder->orderBy('organization_ina000002.id', $order);


        if (is_numeric($rowStatus)) {
            $nascibMemberBuilder->where('organization_ina000002.row_status', $rowStatus);
        }


        /** @var Collection $organizations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $industryAssociations = $nascibMemberBuilder->paginate($pageSize);
            $paginateData = (object)$industryAssociations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $industryAssociations = $nascibMemberBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $industryAssociations->toArray()['data'] ?? $industryAssociations->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());

        return $response;
    }

    public function store(Organization $organization, NascibMember $nascibMember, array $data): NascibMember
    {
        $authUser = Auth::user();

        $orgData['organization_type_id'] = $data['organization_type_id'];
        $orgData['membership_id'] = $data['membership_id'];
        $orgData['permission_sub_group_id'] = $data['permission_sub_group_id'];;
        $orgData['industry_association_id'] = $data['industry_association_id'];

        $orgData['title'] = $data['organization_name'];
        $orgData['title_en'] = $data['organization_name_en'];

        $orgData['loc_division_id'] = $data['organization_loc_division_id'];
        $orgData['loc_district_id'] = $data['organization_loc_district_id'];
        $orgData['loc_upazila_id'] = $data['organization_loc_upazila_id'];
        $orgData['address'] = $data['organization_address'];
        $orgData['address_en'] = $data['organization_address_en'];

        $orgData['mobile'] = $data['mobile'];
        $orgData['email'] = $data['email'];
        $orgData['contact_person_name'] = $data['name'];
        $orgData['contact_person_name_en'] = $data['name_en'];
        $orgData['contact_person_mobile'] = $data['mobile'];
        $orgData['contact_person_email'] = $data['contact_person_email'];
        $orgData['contact_person_designation'] = $data['contact_person_designation'];
        $orgData['contact_person_designation_en'] = $data['contact_person_designation_en'];

        /**Model Name For Nascib Organization */
        $orgData['additional_info_model_name'] = NascibMember::class;

        $organization->fill($orgData);
        $organization->save();

        $organizationService = App(OrganizationService::class);
        $organizationService->addOrganizationToIndustryAssociation($organization, $orgData);

        $data['organization_id'] = $organization->id;

        $nascibMember->fill($data);
        $nascibMember->save();

        return $nascibMember;
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @param array $data
     * @return IndustryAssociation
     */
    public function update(IndustryAssociation $industryAssociation, array $data): IndustryAssociation
    {
        $industryAssociation->fill($data);
        $industryAssociation->save();

        if (!empty($data['skills'])) {
            $this->syncSkill($industryAssociation, $data['skills']);
        } else {
            $this->syncSkill($industryAssociation, []);
        }

        return $industryAssociation;
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return bool
     */
    public function destroy(IndustryAssociation $industryAssociation): bool
    {
        return $industryAssociation->delete();
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return mixed
     * @throws RequestException
     */
    public function userDestroy(IndustryAssociation $industryAssociation): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'user-delete';
        $userPostField = [
            'user_type' => BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE,
            'industry_association_id' => $industryAssociation->id,
        ];

        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug')
            ])
            ->timeout(5)
            ->delete($url, $userPostField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json();
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return bool
     */
    public function restore(IndustryAssociation $industryAssociation): bool
    {
        return $industryAssociation->restore();
    }

    /**
     * industryAssociation comapnyInfoVisibilityvalidator
     * @param array $data
     * @return mixed
     * @throws RequestException
     */
    public function createUser(array $data): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'admin-user-create';
        $userPostField = [
            'permission_sub_group_id' => $data['permission_sub_group_id'] ?? "",
            'user_type' => BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE,
            'industry_association_id' => $data['industry_association_id'] ?? "",
            'username' => $data['mobile'] ?? "",
            'name_en' => $data['name'] ?? "",
            'name' => $data['name'] ?? "",
            'email' => $data['email'] ?? "",
            'mobile' => $data['mobile'] ?? ""
        ];

        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->timeout(5)
            ->post($url, $userPostField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json();

    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        $rules = [
            'application_tracking_no' => 'nullable|string|max: 191',
            /** Same as industry */
            'title' => 'required|string|max:500',
            'title_en' => 'nullable|string|max:191',
            'address' => 'required|string|max:1200',
            'address_en' => 'required|string|max:600',
            'loc_division_id' => [
                'required',
                'integer',
                'exists:loc_divisions,id,deleted_at,NULL'
            ],
            'loc_district_id' => [
                'required',
                'integer',
                'exists:loc_districts,id,deleted_at,NULL'
            ],
            'loc_upazila_id' => [
                'nullable',
                'integer',
                'exists:loc_upazilas,id,deleted_at,NULL'
            ],
            'domain' => 'nullable|string|max:255',
            /** end */

            'trade_license_no' => 'required|string|max:191|unique:nascib_members,trade_license_no',
            'identification_no' => 'nullable|string|max:191',

            'entrepreneur_name' => 'required|string|max: 100',
            'entrepreneur_name_bn' => 'nullable|string|max: 100',
            'entrepreneur_gender' => 'required|int|digits_between: 1,2',
            'entrepreneur_date_of_birth' => 'required|date_format:Y-m-d',
            'entrepreneur_educational_qualification' => 'required|string|max: 191',
            'entrepreneur_nid' => 'required|string',
            'entrepreneur_nid_file_path' => "required|mimes:pdf|max:2048",
            'entrepreneur_mobile' => [
                "required",
                BaseModel::MOBILE_REGEX
            ],
            'entrepreneur_email' => 'required|max:191|email',
            'entrepreneur_photo_path' => [
                'required',
                'string'
            ],

            'form_fill_up_by' => [
                'required',
                'int',
                Rule::in(array_keys(NascibMember::FORM_FILL_UP_LIST))
            ],
            'udc_name' => 'nullable|string|max: 100',
            'udc_loc_district' => 'nullable|string|max: 191',
            'udc_union' => 'nullable|string|max: 191',
            'udc_code' => 'nullable|string|max: 255',
            'chamber_or_association_name' => 'nullable|string|max: 100',
            'chamber_or_association_loc_district_id' => 'nullable|int|exists:loc_districts,id',
            'chamber_or_association_union' => 'nullable|string|max: 191',
            'chamber_or_association_code' => 'nullable|string|max: 255',

            'is_factory' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            "factory_address" => "nullable|string|max:255",
            "factory_web_site" => "nullable|string|max:255",
            "factory_land_own_or_rent" => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'is_proprietorship' => [
                'required',
                'integer',
                Rule::in(array_keys(NascibMember::PROPRIETORSHIP_LIST))
            ],
            'industry_establishment_year' => 'required|date_format:Y',
            'trade_licensing_authority' => [
                'required',
                'int',
                Rule::in(array_keys(NascibMember::TRADE_LICENSING_AUTHORITY))
            ],
            'trade_license' => "nullable|mimes:pdf|max:2048",
            'industry_last_renew_year' => 'required|string|max:4',
            'is_tin' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'investment_amount' => 'required|string|max:255',
            'current_total_asset' => 'nullable|string|max:255',

            'is_registered_under_authority' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'registered_authority' => [
                'nullable',
                'array',
            ],
            'authorized_under_authority' => 'boolean',
            'authorized_authority' => [
                'nullable',
                'array',
            ],
            'have_specialized_area' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'specialized_area_name' => [
                'nullable',
                'array',
            ],
            'under_sme_cluster' => 'boolean',
            'under_sme_cluster_name' => 'nullable|string|max:100',

            'have_member_of_association_or_chamber' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'member_of_association_or_chamber_name' => 'nullable|string|max:191',
            'sector' => 'required|string|max:191',
            'sector_other_name' => 'nullable|string|max:191',
            'business_type' => [
                'required',
                'int',
                Rule::in(array_keys(NascibMember::BUSINESS_TYPE))
            ],
            'main_product_name' => 'required|string|max:191',
            'main_material_description' => [
                'required',
                'string',
                'max:5000'
            ],

            'is_import' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'import_by' => [
                'nullable',
                'array'
            ],
            'is_export_abroad' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'export_abroad_by' => [
                'nullable',
                'array'
            ],
            'industry_irc_no' => 'nullable|string|max:191',
            'salaried_manpower' => [
                'nullable',
                'array'
            ],
            'have_bank_account' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'bank_account_type' => [
                'nullable',
                'array'
            ],

            'have_accounting_system' => 'boolean',
            'use_computer' => 'boolean',
            'have_internet_connection' => 'boolean',
            'have_online_business' => 'boolean',

            'info_provider_name' => 'nullable|string|max:100',
            'info_provider_mobile' => 'nullable|string|max:100',
            'info_collector_name' => 'nullable|string|max:100',
            'info_collector_mobile' => 'nullable|string|max:100',

            'industry_association_id' => [
                'required',
                'int',
                'exists:industry_associations,id,deleted_at,NULL'
            ],
            'permission_sub_group_id' => [
                Rule::requiredIf(function () use ($id) {
                    return is_null($id);
                }),
                'nullable',
                'integer'
            ],

        ];

        if (!empty($request->get('form_fill_up_by') == NascibMember::FORM_FILL_UP_BY_UDC_ENTREPRENEUR)) {
            $rules['udc_name'] = 'required|string|max: 100';
            //$rules['udc_loc_district'] = 'required|string|max: 191';
            $rules['udc_union'] = 'required|string|max: 191';
            $rules['udc_code'] = 'required|string|max: 255';
        }

        if (!empty($request->get('form_fill_up_by') == NascibMember::FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION)) {
            $rules['chamber_or_association_name'] = 'required|string|max: 100';
            $rules['chamber_or_association_loc_district_id'] = 'required|int|exists:loc_districts,id';
            $rules['chamber_or_association_union'] = 'required|string|max: 191';
            $rules['chamber_or_association_code'] = 'required|string|max: 255';
        }

        if (!empty($request->get('factory'))) {
            $rules['factory_loc_district_id'] = 'required|int|exists:loc_districts,id';
            $rules['factory_loc_upazila_id'] = 'required|int|exists:loc_upazilas,id';
            $rules['office_or_showroom'] = 'required|boolean';
        } else {
            $rules['factory_loc_district_id'] = 'nullable|int|exists:loc_districts,id';
            $rules['factory_loc_upazila_id'] = 'nullable|int|exists:loc_upazilas,id';
            $rules['office_or_showroom'] = 'boolean';
        }

        if (!empty($request->get('under_sme_cluster'))) {
            $rules['under_sme_cluster_name'] = 'required|string|max:100';
        }
        if (!empty($request->get('member_of_association_or_chamber'))) {
            $rules['member_of_association_or_chamber_name'] = 'required|string|max:191';
        }

        if (!empty($request->get('export_abroad'))) {
            $rules['industry_irc_no'] = 'required|string|max:191';
        }


        if (!empty($request->get('sector') == 'others')) {
            $rules['sector_other_name'] = 'required|string|max:191';
        }

        if (!empty($request->get('export_abroad'))) {
            $rules['export_abroad_by'] = 'required|array|min:1';
        }

        if (!empty($request->get('import'))) {
            $rules['import_by'] = 'required|array|min:1';
        }
        if (!empty($request->get('have_bank_account'))) {
            $rules['bank_account_type'] = 'required|array|min:1';
        }

        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:600|min:2',
            'title' => 'nullable|max:1200|min:2',
            'page' => 'integer|gt:0',
            'page_size' => 'integer|gt:0',
            'trade_id' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "integer",
                Rule::in(IndustryAssociation::ROW_STATUSES),
            ],
        ], $customMessage);
    }


}
