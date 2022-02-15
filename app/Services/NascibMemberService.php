<?php

namespace App\Services;

use App\Exceptions\HttpErrorException;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use App\Models\IndustryAssociation;
use App\Models\Organization;
use App\Models\NascibMember;
use App\Models\PrimaryJobInformation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

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
    public function getIndustryAssociationList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $tradeId = $request['trade_id'] ?? "";

        /** @var Builder organizationBuilder */
        $industryAssociationBuilder = IndustryAssociation::select([
            'industry_associations.id',
            'industry_associations.trade_id',
            'trades.title as trade_title',
            'trades.title_en as trade_title_en',
            'industry_associations.title_en',
            'industry_associations.title',
            'industry_associations.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'industry_associations.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'industry_associations.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'loc_upazilas.title as location_latitude',
            'loc_upazilas.title as location_longitude',
            'loc_upazilas.title as google_map_src',
            'industry_associations.name_of_the_office_head',
            'industry_associations.name_of_the_office_head_en',
            'industry_associations.name_of_the_office_head_designation',
            'industry_associations.name_of_the_office_head_designation_en',
            'industry_associations.address',
            'industry_associations.address_en',
            'industry_associations.country',
            'industry_associations.phone_code',
            'industry_associations.mobile',
            'industry_associations.email',
            'industry_associations.fax_no',
            'industry_associations.trade_number',
            'industry_associations.contact_person_name',
            'industry_associations.contact_person_name_en',
            'industry_associations.contact_person_mobile',
            'industry_associations.contact_person_email',
            'industry_associations.contact_person_designation',
            'industry_associations.contact_person_designation_en',
            'industry_associations.logo',
            'industry_associations.domain',
            'industry_associations.row_status',
            'industry_associations.created_by',
            'industry_associations.updated_by',
            'industry_associations.created_at',
            'industry_associations.updated_at'
        ]);
        $industryAssociationBuilder->join('trades', function ($join) {
            $join->on('trades.id', '=', 'industry_associations.trade_id')
                ->whereNull('trades.deleted_at');
        });

        $industryAssociationBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('industry_associations.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $industryAssociationBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('industry_associations.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $industryAssociationBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('industry_associations.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $industryAssociationBuilder->orderBy('industry_associations.id', $order);


        if (is_numeric($rowStatus)) {
            $industryAssociationBuilder->where('industry_associations.row_status', $rowStatus);
        }

        if (is_numeric($tradeId)) {
            $industryAssociationBuilder->where('industry_associations.trade_id', $tradeId);
        }

        if (!empty($titleEn)) {
            $industryAssociationBuilder->where('industry_associations.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $industryAssociationBuilder->where('industry_associations.title', 'like', '%' . $title . '%');
        }

        $industryAssociationBuilder->with('skills');

        /** @var Collection $organizations */

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $industryAssociations = $industryAssociationBuilder->paginate($pageSize);
            $paginateData = (object)$industryAssociations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $industryAssociations = $industryAssociationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $industryAssociations->toArray()['data'] ?? $industryAssociations->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());

        return $response;
    }

    /**
     * @param int $id
     * @return Model|Builder
     */
    public function getOneIndustryAssociation(int $id): Model|Builder
    {
        /** @var Builder $industryAssociationBuilder */
        $industryAssociationBuilder = IndustryAssociation::select([
            'industry_associations.id',
            'industry_associations.trade_id',
            'trades.title as trade_title',
            'trades.title_en as trade_title_en',
            'industry_associations.title_en',
            'industry_associations.title',
            'industry_associations.loc_division_id',
            'loc_divisions.title_en as loc_division_title_en',
            'loc_divisions.title as loc_division_title',
            'industry_associations.loc_district_id',
            'loc_districts.title_en as loc_district_title_en',
            'loc_districts.title as loc_district_title',
            'industry_associations.loc_upazila_id',
            'loc_upazilas.title_en as loc_upazila_title_en',
            'loc_upazilas.title as loc_upazila_title',
            'industry_associations.location_latitude',
            'industry_associations.location_longitude',
            'loc_upazilas.title as google_map_src',
            'industry_associations.name_of_the_office_head',
            'industry_associations.name_of_the_office_head_en',
            'industry_associations.name_of_the_office_head_designation',
            'industry_associations.name_of_the_office_head_designation_en',
            'industry_associations.address',
            'industry_associations.address_en',
            'industry_associations.country',
            'industry_associations.phone_code',
            'industry_associations.mobile',
            'industry_associations.email',
            'industry_associations.fax_no',
            'industry_associations.trade_number',
            'industry_associations.contact_person_name',
            'industry_associations.contact_person_name_en',
            'industry_associations.contact_person_mobile',
            'industry_associations.contact_person_email',
            'industry_associations.contact_person_designation',
            'industry_associations.contact_person_designation_en',
            'industry_associations.logo',
            'industry_associations.domain',
            'industry_associations.row_status',
            'industry_associations.created_by',
            'industry_associations.updated_by',
            'industry_associations.created_at',
            'industry_associations.updated_at'
        ]);

        $industryAssociationBuilder->join('trades', function ($join) {
            $join->on('trades.id', '=', 'industry_associations.trade_id')
                ->whereNull('trades.deleted_at');
        });

        $industryAssociationBuilder->leftjoin('loc_divisions', function ($join) {
            $join->on('industry_associations.loc_division_id', '=', 'loc_divisions.id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $industryAssociationBuilder->leftjoin('loc_districts', function ($join) {
            $join->on('industry_associations.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });
        $industryAssociationBuilder->leftjoin('loc_upazilas', function ($join) {
            $join->on('industry_associations.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $industryAssociationBuilder->where('industry_associations.id', '=', $id);

        $industryAssociationBuilder->with('skills');

        return $industryAssociationBuilder->firstOrFail();
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @param array $data
     * @return IndustryAssociation
     */
    public function store(NascibMember $nascibMember, array $data): NascibMember
    {
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

    private function syncSkill(IndustryAssociation $industryAssociation, array $skills)
    {
        $industryAssociation->skills()->sync($skills);
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
     * @param IndustryAssociation $industryAssociation
     * @return IndustryAssociation
     */
    public function industryAssociationStatusChangeAfterApproval(IndustryAssociation $industryAssociation): IndustryAssociation
    {
        $industryAssociation->row_status = BaseModel::ROW_STATUS_ACTIVE;
        $industryAssociation->save();
        return $industryAssociation;
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return IndustryAssociation
     */
    public function industryAssociationStatusChangeAfterRejection(IndustryAssociation $industryAssociation): IndustryAssociation
    {
        $industryAssociation->row_status = BaseModel::ROW_STATUS_REJECTED;
        $industryAssociation->save();
        return $industryAssociation;
    }

    /**
     * @param Request $request
     * @param IndustryAssociation $industryAssociation
     * @return array|null
     * @throws RequestException
     */
    public function industryAssociationUserApproval(Request $request, IndustryAssociation $industryAssociation): array|null
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'user-approval';
        $userPostField = [
            'permission_sub_group_id' => $request->input('permission_sub_group_id') ?? "",
            'user_type' => BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE,
            'industry_association_id' => $industryAssociation->id,
            'name_en' => $industryAssociation->contact_person_name ?? "",
            'name' => $industryAssociation->contact_person_name ?? "",
            'row_status' => $industryAssociation->row_status
        ];

        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->put($url, $userPostField)
            ->throw(function ($response, $e) {
                return $e;
            })
            ->json();
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return mixed
     * @throws RequestException
     */

    public function industryAssociationUserRejection(IndustryAssociation $industryAssociation): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'user-rejection';
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
            ->put($url, $userPostField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json();
    }

    /**
     * @param array $data
     * @param Organization $organization
     * @return int
     */
    public function industryAssociationMembershipApproval(array $data, Organization $organization): int
    {
        return $organization->industryAssociations()->updateExistingPivot($data['industry_association_id'], [
            'row_status' => BaseModel::ROW_STATUS_ACTIVE
        ]);
    }

    /**
     * @param array $data
     * @param Organization $organization
     * @return int
     */
    public function industryAssociationMembershipRejection(array $data, Organization $organization): int
    {
        return $organization->industryAssociations()->updateExistingPivot($data['industry_association_id'], [
            'row_status' => BaseModel::ROW_STATUS_REJECTED
        ]);

    }


    /**
     * Validator for industry registration/industryAssociation membership  approval/rejection
     * @param Request $request
     * @param int $organizationId
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function industryAssociationMembershipValidator(Request $request, int $organizationId): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'industry_association_id' => [
                'required',
                'integer',
                Rule::exists('industry_association_organization', 'industry_association_id')
                    ->where(function ($query) use ($organizationId) {
                        $query->where('organization_id', $organizationId);
                    })
            ]
        ];
        return Validator::make($request->all(), $rules);
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
            'username' => $data['contact_person_mobile'] ?? "",
            'name_en' => $data['contact_person_name'] ?? "",
            'name' => $data['contact_person_name'] ?? "",
            'email' => $data['contact_person_email'] ?? "",
            'mobile' => $data['contact_person_mobile'] ?? ""
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
     * @param array $data
     * @return array|null
     * @throws RequestException
     */
    public function createOpenRegisterUser(array $data): array|null
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'user-open-registration';

        $userPostField = [
            'user_type' => BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE,
            'username' => $data['contact_person_mobile'] ?? "",
            'industry_association_id' => $data['industry_association_id'] ?? "",
            'name_en' => $data['contact_person_name_en'] ?? "",
            'name' => $data['contact_person_name'] ?? "",
            'email' => $data['contact_person_email'] ?? "",
            'mobile' => $data['contact_person_mobile'] ?? "",
            'password' => $data['password'] ?? ""
        ];

        Log::channel('org_reg')->info("organization registration data provided to core", $userPostField);

        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->post($url, $userPostField)
            ->throw(static function (Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json();
    }


    /**
     * @param $id
     * @return mixed
     */
    public function getIndustryAssociationCode($id): mixed
    {
        return IndustryAssociation::findOrFail($id)->code;

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
     * @param IndustryAssociation $industryAssociation
     * @return array
     */
    public function getIndustryAssociationDashboardStatistics(IndustryAssociation $industryAssociation): array
    {
        $organizations = $this->getIndustryCountByIndustryAssociation($industryAssociation);
        $employed = $this->employmentCountByIndustryAssociation($industryAssociation);
        $unemployed = 0;
        $vacancies = $this->getVacancyCountByIndustryAssociation($industryAssociation);
        $trendingSkills = $this->getTrendingJobSkillsCountByIndustryAssociation($industryAssociation);

        return [
            "organizations" => $organizations,
            "employed" => $employed,
            "unemployed" => $unemployed,
            "vacancies" => $vacancies,
            "trending_skills" => $trendingSkills
        ];
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return int
     */
    public function employmentCountByIndustryAssociation(IndustryAssociation $industryAssociation): int
    {
        return AppliedJob::query()
            ->join('primary_job_information', 'primary_job_information.job_id', '=', 'applied_jobs.job_id')
            ->where('primary_job_information.industry_association_id', $industryAssociation->id)
            ->where('applied_jobs.apply_status', AppliedJob::APPLY_STATUS["Hired"])
            ->count('applied_jobs.id');
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return int
     */
    public function getIndustryCountByIndustryAssociation(IndustryAssociation $industryAssociation): int
    {
        return $industryAssociation->organizations()->count('organization_id');
    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return int
     */
    public function getTrendingJobSkillsCountByIndustryAssociation(IndustryAssociation $industryAssociation): int
    {
        $candidateRequirementBuilder = CandidateRequirement::query()
            ->join('primary_job_information', 'primary_job_information.job_id', '=', 'candidate_requirements.job_id')
            ->where('primary_job_information.industry_association_id', $industryAssociation->id);

        $candidateRequirements = $candidateRequirementBuilder->get();

        $trendingSkills = 0;
        foreach ($candidateRequirements as $candidateRequirement) {
            $trendingSkills += $candidateRequirement->skills()->distinct()->count('skill_id');
        }
        return $trendingSkills;

    }

    /**
     * @param IndustryAssociation $industryAssociation
     * @return int
     */
    public function getVacancyCountByIndustryAssociation(IndustryAssociation $industryAssociation): int
    {
        return PrimaryJobInformation::where('industry_association_id', $industryAssociation->id)
            ->where('application_deadline', '>', Carbon::today())
            ->where('published_at', '<=', Carbon::now())
            ->sum('no_of_vacancies');
    }

    /**
     * industryAssociation open registration validation
     * @param Request $request
     * @return mixed
     */
    public function industryAssociationRegistrationValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'password.regex' => BaseModel::PASSWORD_VALIDATION_MESSAGE
        ];

        $rules = [
            'trade_id' => [
                'required',
                'int',
                'exists:trades,id,deleted_at,NULL',
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:600',
                'min:2',
            ],
            'title' => [
                'required',
                'string',
                'max:1200',
                'min:2'
            ],
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
            "name_of_the_office_head" => [
                "required",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_en" => [
                "nullable",
                "string",
                'max:600'
            ],
            "name_of_the_office_head_designation" => [
                "required",
                "string"
            ],
            "name_of_the_office_head_designation_en" => [
                "nullable",
                "string"
            ],
            'address' => [
                'nullable',
                'max: 1200',
                'min:2'
            ],
            'address_en' => [
                'nullable',
                'max: 600',
                'min:2'
            ],
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'email' => [
                'required',
                'email',
                'max:191'
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_name_en' => [
                'nullable',
                'max: 250',
                'min:2'
            ],
            'contact_person_mobile' => [
                'required',
                Rule::unique('industry_associations', 'contact_person_mobile')
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    }),
                BaseModel::MOBILE_REGEX,
            ],
            'contact_person_email' => [
                'required',
                'email',
                'max:191'
            ],
            'contact_person_designation' => [
                'required',
                'max: 600',
                "min:2"
            ],
            'contact_person_designation_en' => [
                'nullable',
                'max: 300',
                "min:2"
            ],
            /**Commented it for custom validation message*/
//            "password" => [
//                "required",
//                "confirmed",
//                Password::min(BaseModel::PASSWORD_MIN_LENGTH)
//                    ->letters()
//                    ->mixedCase()
//                    ->numbers()
//            ],
            "password" => [
                'required',
                'min:' . BaseModel::PASSWORD_MIN_LENGTH,
                BaseModel::PASSWORD_REGEX
            ],
            "password_confirmation" => 'required_with:password',
            'row_status' => [
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_PENDING])
            ]
        ];
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
