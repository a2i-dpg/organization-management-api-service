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
        $orgData['organization_type_id'] = 1;//TODO
        $orgData['membership_id'] = 'MS-ID-123';//TODO
        $orgData['permission_sub_group_id'] = $data['permission_sub_group_id'];;
        $orgData['industry_association_id'] = $data['industry_association_id'];
        $orgData['title'] = $data['organization_name'];
        $orgData['loc_division_id'] = 1;//TODO $data['organization_loc_district_id'];
        $orgData['loc_district_id'] = $data['organization_loc_district_id'];
        $orgData['loc_upazila_id'] = $data['organization_loc_upazila_id'];
        $orgData['address'] = $data['organization_address'];
        $orgData['mobile'] = $data['mobile'];
        $orgData['email'] = $data['email'];
        $orgData['contact_person_name'] = $data['name'];
        $orgData['contact_person_mobile'] = $data['mobile'];
        $orgData['contact_person_email'] = $data['email'];
        $orgData['contact_person_designation'] = 'Software Engineer'; //TODO
        $orgData['additional_info_model_name'] = 'NascibMember'; //TODO

        $organization = $organization->create($orgData);
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
            'form_fill_up_by' => [
                'required',
                'int',
                'between:1,4'
            ],
            'udc_name' => 'nullable|string|max: 100',
            'udc_loc_district' => 'nullable|string|max: 191',
            'udc_union' => 'nullable|string|max: 191',
            'udc_code' => 'nullable|string|max: 255',

            'chamber_or_association_name' => 'nullable|string|max: 100',
            'chamber_or_association_loc_district_id' => 'nullable|int|exists:loc_districts,id',
            'chamber_or_association_union' => 'nullable|string|max: 191',
            'chamber_or_association_code' => 'nullable|string|max: 255',

            'name' => 'required|string|max: 100',
            'name_bn' => 'nullable|string|max: 100',
            'gender' => 'required|int|digits_between: 1,2',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'educational_qualification' => 'required|string|max: 191',
            'nid' => 'required|string|max: 30',
            'nid_file' => "required|mimes:pdf|max:2048",
            'mobile' => 'required|string|max: 20',
            'email' => 'nullable|string|max:191|email',
            'entrepreneur_photo' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:500',
                'dimensions:width=300,height=300'
            ],
            'organization_trade_license_no' => 'required|string|max:191|unique:organization_ina000002,organization_trade_license_no',
            'organization_identification_no' => 'nullable|string|max:191',
            'organization_name' => 'required|string|max:191',
            'organization_address' => 'required|string|max:191',
            'organization_loc_district_id' => 'nullable|int|exists:loc_districts,id',
            'organization_loc_upazila_id' => 'nullable|int|exists:loc_upazilas,id',
            'organization_domain' => 'nullable|string|max:255',
            'factory' => 'boolean',
            "factory_address" => "nullable|string|max:255",
            "factory_loc_district_id" => "required|int|exists:loc_districts,id",
            "factory_loc_upazila_id" => "required|int|exists:loc_upazilas,id",
            "factory_web_site" => "nullable|string|max:255",
            "office_or_showroom" => "boolean",
            "factory_land_own_or_rent" => "boolean",

            'proprietorship' => 'required|int|in: 1,2,3',
            'industry_establishment_year' => 'required|date_format:Y',
            'trade_licensing_authority' => [
                'required',
                'int',
                'between:1,3'
            ],
            'trade_license' => "nullable|mimes:pdf|max:2048",
            'industry_last_renew_year' => 'required|string|max:4',
            'tin' => 'boolean',
            'investment_amount' => 'required|string|max:255',
            'current_total_asset' => 'nullable|string|max:255',

            'registered_under_authority' => 'boolean',
            'registered_authority' => [
                'nullable',
                'array',
            ],
            'authorized_under_authority' => 'boolean',
            'authorized_authority' => [
                'nullable',
                'array',
            ],
            'specialized_area' => 'boolean',
            'specialized_area_name' => [
                'nullable',
                'array',
            ],
            'under_sme_cluster' => 'boolean',
            'under_sme_cluster_name' => 'nullable|string|max:100',

            'member_of_association_or_chamber' => 'boolean',
            'member_of_association_or_chamber_name' => 'nullable|string|max:191',
            'sector' => 'required|string|max:191',
            'sector_other_name' => 'nullable|string|max:191',
            'business_type' => [
                'required',
                'int',
                'between:1,3'
            ],
            'main_product_name' => 'required|string|max:191',
            'main_material_description' => [
                'required',
                'string',
                'max:5000'
            ],

            'import' => 'boolean',
            'import_by' => [
                'nullable',
                'array'
            ],
            'export_abroad' => 'boolean',
            'export_abroad_by' => [
                'nullable',
                'array'
            ],
            'industry_irc_no' => 'nullable|string|max:191',

            'salaried_manpower' => [
                'nullable',
                'array'
            ],
            'have_bank_account' => 'boolean',
            'bank_account_type' => [
                'nullable',
                'array'
            ],
            'accounting_system' => 'boolean',
            'use_computer' => 'boolean',
            'internet_connection' => 'boolean',
            'online_business' => 'boolean',
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

        if (!empty($request->form_fill_up_by == NascibMember::FORM_FILL_UP_BY_UDC_ENTREPRENEUR)) {
            $rules['udc_name'] = 'required|string|max: 100';
            //$rules['udc_loc_district'] = 'required|string|max: 191';
            $rules['udc_union'] = 'required|string|max: 191';
            $rules['udc_code'] = 'required|string|max: 255';
        }

        if (!empty($request->form_fill_up_by == NascibMember::FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION)) {
            $rules['chamber_or_association_name'] = 'required|string|max: 100';
            $rules['chamber_or_association_loc_district_id'] = 'required|int|exists:loc_districts,id';
            $rules['chamber_or_association_union'] = 'required|string|max: 191';
            $rules['chamber_or_association_code'] = 'required|string|max: 255';
        }

        if (!empty($request->factory)) {
            $rules['factory_loc_district_id'] = 'required|int|exists:loc_districts,id';
            $rules['factory_loc_upazila_id'] = 'required|int|exists:loc_upazilas,id';
            $rules['office_or_showroom'] = 'required|boolean';
        } else {
            $rules['factory_loc_district_id'] = 'nullable|int|exists:loc_districts,id';
            $rules['factory_loc_upazila_id'] = 'nullable|int|exists:loc_upazilas,id';
            $rules['office_or_showroom'] = 'boolean';
        }

        if (!empty($request->under_sme_cluster)) {
            $rules['under_sme_cluster_name'] = 'required|string|max:100';
        }
        if (!empty($request->member_of_association_or_chamber)) {
            $rules['member_of_association_or_chamber_name'] = 'required|string|max:191';
        }

        if (!empty($request->export_abroad)) {
            $rules['industry_irc_no'] = 'required|string|max:191';
        }


        if (!empty($request->sector == 'others')) {
            $rules['sector_other_name'] = 'required|string|max:191';
        }

        if (!empty($request->export_abroad)) {
            $rules['export_abroad_by'] = 'required|array|min:1';
        }

        if (!empty($request->import)) {
            $rules['import_by'] = 'required|array|min:1';
        }
        if (!empty($request->have_bank_account)) {
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
