<?php

namespace App\Services;

use App\Exceptions\HttpErrorException;
use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\IndustryAssociationConfig;
use App\Models\MembershipType;
use App\Models\NascibMember;
use App\Models\Organization;
use App\Models\PaymentTransactionHistory;
use App\Services\CommonServices\CodeGenerateService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Carbon\Carbon;
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
    public function registerNascib(Organization $organization, NascibMember $nascibMember, array $data): array
    {
        $orgData['organization_type_id'] = Organization::ORGANIZATION_TYPE_PRIVATE;
        $orgData['mobile'] = $data['entrepreneur_mobile'];
        $orgData['email'] = $data['entrepreneur_email'];
        $orgData['contact_person_name'] = $data['entrepreneur_name'];
        $orgData['contact_person_name_en'] = $data['entrepreneur_name_en'];
        $orgData['contact_person_mobile'] = $data['entrepreneur_mobile'];
        $orgData['contact_person_email'] = $data['entrepreneur_email'];
        $orgData['contact_person_designation'] = 'উদ্যোক্তা';
        $orgData['contact_person_designation_en'] = 'Entrepreneur';
        $orgData['payment_status'] = Organization::PAYMENT_PENDING;
        $orgData['membership_id'] = $data['membership_id'];
        $orgData['membership_type_id'] = $data['membership_type_id'];

        /**Model Name For Nascib Organization */
        $orgData['additional_info_model_name'] = NascibMember::class;
        $data = array_merge($data, $orgData);
        $organization->fill($data);
        $organization->save();

        $data['industry_association_organization_id'] = $this->attachToIndustryAssociation($organization, $data, true);

        $nascibMember->fill($data);
        $nascibMember->save();

        return [
            $organization,
            $nascibMember
        ];
    }

    private function attachToIndustryAssociation(Organization $organization, array $data, bool $isOpenReg = false)
    {

        $organization->industryAssociations()->attach($data['industry_association_id'], [
            'membership_id' => $data['membership_id'],
            'membership_type_id' => $data['membership_type_id'],
            'payment_status' => BaseModel::PAYMENT_PENDING,
            'additional_info_model_name' => $data['additional_info_model_name'],
            'row_status' => $isOpenReg ? BaseModel::ROW_STATUS_PENDING : BaseModel::ROW_STATUS_ACTIVE
        ]);
        $organization = $organization->fresh();
        return $organization->industryAssociations()->firstOrFail()->pivot->id;

    }

    public function updateMembershipExpireDate(int $industryAssociationId, int $organizationId, int $membershipTypeId)
    {
        $organization = Organization::findOrFail($organizationId);

        $organization->industryAssociations()->updateExistingPivot($industryAssociationId, [
            'payment_status' => BaseModel::PAYMENT_SUCCESS,
            'payment_date' => Carbon::now()->format('Y-m-d'),
            'member_ship_expire_date' => $this->getMembershipExpireDate($membershipTypeId)
        ]);
    }

    private function getMembershipExpireDate(int $membershipTypeId)
    {
        $memberShipExpirationDate = null;
        $membershipType = MembershipType::where('membership_types.id', $membershipTypeId)
            ->where('membership_types.row_status', BaseModel::ROW_STATUS_ACTIVE)
            ->join('industry_association_configs', 'industry_association_configs.industry_association_id', 'membership_types.industry_association_id')
            ->firstOrFail([
                'membership_types.payment_nature',
                'membership_types.payment_frequency',
                'membership_types.industry_association_id',
                'industry_association_configs.session_type'
            ]);

        if ($membershipType->payment_nature == MembershipType::PAYMENT_NATURE_SESSION_WISE_KEY) {
            if ($membershipType->payment_frequency == MembershipType::PAYMENT_FREQUENCY_YEARLY_KEY) {
                $memberShipExpirationDate = $this->getSessionalDate($membershipType->session_type);
            }
        } elseif ($membershipType->payment_nature == MembershipType::PAYMENT_NATURE_DATE_WISE_KEY) {
            $memberShipExpirationDate = $this->getDateWiseDate($membershipType->payment_frequency);
        }
        Log::info($memberShipExpirationDate);
        return $memberShipExpirationDate;
    }

    private function getSessionalDate(int $sessionType)
    {
        $memberShipExpirationDate = null;

        $currentDate = Carbon::now()->format('Y-m-d');
        $sessionEndDate = config('nise3.payment_config.session_type_wise_expiration_date.' . $sessionType . '.end_date');
        if ($sessionType == IndustryAssociationConfig::SESSION_TYPE_JUNE_JULY) {
            if (Carbon::parse($currentDate)->lessThanOrEqualTo($sessionEndDate)) {
                $memberShipExpirationDate = $sessionEndDate;
            } else {
                $memberShipExpirationDate = Carbon::make($currentDate)->addYear()->format('Y-m-d');
            }
        } elseif ($sessionType == IndustryAssociationConfig::SESSION_TYPE_JANUARY_DECEMBER) {
            if (Carbon::parse($currentDate)->lessThanOrEqualTo($sessionEndDate)) {
                $memberShipExpirationDate = $sessionEndDate;
            } else {
                $memberShipExpirationDate = Carbon::make($currentDate)->addYear()->format('Y-m-d');
            }
        }
        return $memberShipExpirationDate;
    }

    private function getDateWiseDate(int $paymentFrequency): ?string
    {
        $memberShipExpirationDate = null;

        if ($paymentFrequency == MembershipType::PAYMENT_FREQUENCY_MONTHLY_KEY) {
            $memberShipExpirationDate = Carbon::now()->addMonth()->format('Y-m-d');
        } elseif ($paymentFrequency == MembershipType::PAYMENT_FREQUENCY_QUARTERLY_KEY) {
            $memberShipExpirationDate = Carbon::now()->addQuarter()->format('Y-m-d');
        } elseif ($paymentFrequency == MembershipType::PAYMENT_FREQUENCY_HALF_YEARLY_KEY) {
            $memberShipExpirationDate = Carbon::now()->addMonths(6)->format('Y-m-d');
        } elseif ($paymentFrequency == MembershipType::PAYMENT_FREQUENCY_YEARLY_KEY) {
            $memberShipExpirationDate = Carbon::now()->addYear()->format('Y-m-d');
        }

        return $memberShipExpirationDate;
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
    public function createNascibUser(array $data): mixed
    {
        $nascibUserPostField = [
            'organization_id' => $data['organization_id'],
            'contact_person_name' => $data['entrepreneur_name'],
            'contact_person_name_en' => $data['entrepreneur_name_en'],
            'contact_person_email' => $data['entrepreneur_email'],
            'contact_person_mobile' => $data['entrepreneur_mobile'],
            'password' => $data['password']
        ];

        Log::channel('idp_user')->info('Nascib-User-Payload: ' . json_encode($nascibUserPostField));
        return app(OrganizationService::class)->createOpenRegisterUser($nascibUserPostField);

    }

    /**
     * @throws Throwable
     */
    public function getMemberApprovedUserMailMessageBody(array $industryAssociationOrganization): string
    {
        $industryAssociationId = $industryAssociationOrganization['industry_association_id'];
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);
        $membershipType = MembershipType::findOrFail($industryAssociationOrganization['membership_type_id'])->firstOrFail();
        $applicationFee = $membershipType->fee;
        $paymentGatewayUrl = $this->getPaymentPageUrlForNascibPayment($industryAssociationId, NascibMember::APPLICATION_TYPE_NEW);
        $mailData = [
            "industry_association_title" => $industryAssociation->title,
            "application_fee" => $applicationFee,
            "payment_gateway_url" => $paymentGatewayUrl
        ];
        return view('mail.nasib_member_user_approval_mail_template', compact('mailData'))->render();
    }

    /**
     * @throws Throwable
     */
    public function getPaymentPageUrlForNascibPayment(int $industryAssociationId, string $applicationType): string
    {
        $jwtPayload = [
            "purpose" => $applicationType,
            "purpose_related_id" => $industryAssociationId,
        ];

        $parameter = "industry_association_id=" . $industryAssociationId;

        $baseUrl = "https://" . ServiceToServiceCall::getDomain($parameter) ?? "nise.gov.bd";

        return $baseUrl . "/" . NascibMember::PAYMENT_GATEWAY_PAGE_URL_PREFIX . "/" . CodeGenerateService::jwtToken($jwtPayload);
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
            'form_fill_up_by' => [
                'required',
                'int',
                Rule::in(array_keys(NascibMember::FORM_FILL_UP_LIST))
            ],

            'application_tracking_no' => 'nullable|string|max: 191',
            'membership_type_id' => [
                "required",
                "integer",
                "exists:membership_types,id"
            ],
            'membership_id' => [
                "required",
                "string",
            ],
            'trade_license_no' => 'required|string|max:191|unique:nascib_members,trade_license_no',
            /** Same as industry */
            'title' => 'required|string|max:500',
            'title_en' => 'nullable|string|max:191',
            'address' => 'required|string|max:1200',
            'address_en' => 'nullable|string|max:600',
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

            'identification_no' => 'nullable|string|max:191',

            'entrepreneur_name' => 'required|string|max: 100',
            'entrepreneur_name_en' => 'nullable|string|max: 100',
            'entrepreneur_gender' => 'required|int|digits_between: 1,2',
            'entrepreneur_date_of_birth' => 'required|date_format:Y-m-d',
            'entrepreneur_educational_qualification' => 'required|string|max: 191',
            'entrepreneur_nid' => 'required|string',
            'entrepreneur_nid_file_path' => [
                'required',
                'string'
            ],
            'entrepreneur_mobile' => [
                "required",
                BaseModel::MOBILE_REGEX
            ],
            'entrepreneur_email' => 'required|max:191|email',
            'entrepreneur_photo_path' => [
                'required',
                'string'
            ],
            'have_factory' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            /** additional information of industry */
            'is_proprietorship' => [
                'required',
                'integer',
                Rule::in(array_keys(NascibMember::PROPRIETORSHIP_LIST))
            ],
            'date_of_establishment' => 'required|date_format:Y-m-d',
            'trade_licensing_authority' => [
                'required',
                'int',
                Rule::in(array_keys(NascibMember::TRADE_LICENSING_AUTHORITY))
            ],
            'trade_license_path' => "required|string",
            'trade_license_last_renew_year' => 'required|string|max:4',
            'have_tin' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'investment_amount' => 'required|numeric',
            'current_total_asset' => 'nullable|numeric',

            'is_registered_under_authority' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'registered_authority' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('is_registered_under_authority') == BaseModel::BOOLEAN_TRUE;
                }),
                'nullable',
                'array'
            ],
            'registered_authority.*.authority_type' => [
                'required',
                'integer',
                Rule::in(array_keys(NascibMember::REGISTERED_AUTHORITY))
            ],
            'registered_authority.*.registration_number' => [
                'required',
                'string'
            ],

            'is_authorized_under_authority' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'authorized_authority' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('is_authorized_under_authority') == BaseModel::BOOLEAN_TRUE;
                }),
                'array',
            ],
            'authorized_authority.*.authority_type' => [
                Rule::requiredIf(function () use ($request) {
                    return !array_key_exists(NascibMember::OTHER_AUTHORITY_KEY, $request->get('authorized_authority'));
                }),
                'nullable',
                "integer",
                Rule::in(array_keys(NascibMember::AUTHORIZED_AUTHORITY))
            ],
            'authorized_authority.*.registration_number' => [
                Rule::requiredIf(function () use ($request) {
                    return !array_key_exists(NascibMember::OTHER_AUTHORITY_KEY, $request->get('authorized_authority'));
                }),
                'nullable',
                "string"
            ],
            'have_specialized_area' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'specialized_area' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('have_specialized_area') == BaseModel::BOOLEAN_TRUE;
                }),
                'nullable',
                'array',
            ],
            'is_under_sme_cluster' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'under_sme_cluster_id' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('is_under_sme_cluster') == BaseModel::BOOLEAN_TRUE;
                }),
                'nullable',
                'integer',
                'exists:smef_clusters,id,deleted_at,NULL'
            ],
            'is_under_of_association_or_chamber' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'under_association_or_chamber_name' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('is_under_of_association_or_chamber') == BaseModel::BOOLEAN_TRUE;
                }),
                'nullable',
                "string"
            ],
            'under_association_or_chamber_name_en' => [
                'nullable',
                "string"
            ],
            'sector_id' => [
                'required',
                Rule::in(array_keys(NascibMember::SECTOR))
            ],
            'other_sector_name' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('sector_id') == NascibMember::OTHER_SECTOR_KEY;
                }),
                'string'
            ],
            'other_sector_name_en' => 'nullable|string|max:191',

            'business_type' => [
                'required',
                'int',
                Rule::in(array_keys(NascibMember::BUSINESS_TYPE))
            ],
            'main_product_name' => 'required|string|max:191',
            'main_product_name_en' => 'nullable|string|max:191',
            'main_material_description' => [
                'required',
                'string',
                'max:5000'
            ],
            'main_material_description_en' => [
                'nullable',
                'string',
                'max:5000'
            ],

            'is_import' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'import_type' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('is_import') == BaseModel::BOOLEAN_TRUE;
                }),
                'nullable',
                'array'
            ],
            'import_type.*' => [
                'required',
                'integer',
                Rule::in(array_keys(NascibMember::IMPORT_EXPORT_TYPE))
            ],
            'is_export' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'export_type' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('is_export') == BaseModel::BOOLEAN_TRUE;
                }),
                'nullable',
                'array'
            ],
            'export_type.*' => [
                'required',
                'integer',
                Rule::in(array_keys(NascibMember::IMPORT_EXPORT_TYPE))
            ],
            'industry_irc_no' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('is_export') == BaseModel::BOOLEAN_TRUE;
                }),
                'nullable',
                'string'
            ],
            'salaried_manpower' => [
                'nullable',
                'array'
            ],
            'salaried_manpower.' . NascibMember::PERMANENT_WORKER_KEY => [
                'required',
                'array'
            ],
            'salaried_manpower.' . NascibMember::PERMANENT_WORKER_KEY . '.' . NascibMember::MANPOWER_TYPE_MALE => [
                'nullable',
                'integer'
            ],
            'salaried_manpower.' . NascibMember::PERMANENT_WORKER_KEY . '.' . NascibMember::MANPOWER_TYPE_FEMALE => [
                'nullable',
                'integer'
            ],
            'salaried_manpower.' . NascibMember::TEMPORARY_WORKER_KEY => [
                'required',
                'array'
            ],
            'salaried_manpower.' . NascibMember::TEMPORARY_WORKER_KEY . '.' . NascibMember::MANPOWER_TYPE_MALE => [
                'nullable',
                'integer'
            ],
            'salaried_manpower.' . NascibMember::TEMPORARY_WORKER_KEY . '.' . NascibMember::MANPOWER_TYPE_FEMALE => [
                'nullable',
                'integer'
            ],
            'salaried_manpower.' . NascibMember::SEASONAL_WORKER_KEY => [
                'required',
                'array'
            ],
            'salaried_manpower.' . NascibMember::SEASONAL_WORKER_KEY . '.' . NascibMember::MANPOWER_TYPE_MALE => [
                'nullable',
                'integer'
            ],
            'salaried_manpower.' . NascibMember::SEASONAL_WORKER_KEY . '.' . NascibMember::MANPOWER_TYPE_FEMALE => [
                'nullable',
                'integer'
            ],
            'have_bank_account' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'bank_account_type' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->get('have_bank_account') == BaseModel::BOOLEAN_TRUE;
                }),
                'nullable',
                'array'
            ],

            'have_daily_accounting_system' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'use_computer' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'have_internet_connection' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'have_online_business' => [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ],
            'industry_association_id' => [
                'required',
                'int',
                'exists:industry_associations,id,deleted_at,NULL'
            ],
            'permission_sub_group_id' => [
                'nullable',
                'integer'
            ],

        ];

        /** other Authority */
        if (!empty($request->get('authorized_authority')) && is_array($request->get('authorized_authority')) && array_key_exists(NascibMember::OTHER_AUTHORITY_KEY, $request->get('authorized_authority'))) {
            $rules['authorized_authority.' . NascibMember::OTHER_AUTHORITY_KEY . '.' . 'authority_name'] = [
                "required",
                "string"
            ];
            $rules['authorized_authority.' . NascibMember::OTHER_AUTHORITY_KEY . '.' . 'register_number'] = [
                "required",
                "string"
            ];
        }

        /** Bank Account Type */
        if (!empty($request->get('bank_account_type'))) {
            $rules['bank_account_type.' . NascibMember::BANK_ACCOUNT_PERSONAL] = [
                'required',
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ];
            $rules['bank_account_type.' . NascibMember::BANK_ACCOUNT_INDUSTRY] = [
                'required',
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ];
        }

        if (!empty($request->get('form_fill_up_by')) && $request->get('form_fill_up_by') == NascibMember::FORM_FILL_UP_BY_UDC_ENTREPRENEUR) {
            $rules['udc_name'] = 'required|string|max: 100';
            $rules['udc_loc_district'] = [
                'nullable',
                'integer',
                //'exists:loc_districts,id,deleted_at,NULL'
            ];
            $rules['udc_union'] = [
                'required',
                'integer',
                //'exists:loc_unions,id,deleted_at,NULL'
            ];

            $rules['udc_code'] = 'required|string|max: 255';

            /** info_provider  information */
            $rules['info_provider_name'] = 'nullable|string|max:100';
            $rules['info_provider_mobile'] = [
                "required",
                BaseModel::MOBILE_REGEX
            ];
            $rules['info_collector_name'] = 'nullable|string|max:100';
            $rules['info_collector_mobile'] = [
                "required",
                BaseModel::MOBILE_REGEX
            ];

        }

        if (!empty($request->get('form_fill_up_by') == NascibMember::FORM_FILL_UP_BY_CHAMBER_OR_ASSOCIATION)) {
            $rules['chamber_or_association_name'] = 'required|string|max: 100';
            $rules['chamber_or_association_loc_district_id'] = [
                'nullable',
                'integer',
                'exists:loc_districts,id,deleted_at,NULL'
            ];
            $rules['chamber_or_association_union_id'] = [
                'required',
                'integer',
                'exists:loc_unions,id,deleted_at,NULL'
            ];
            $rules['chamber_or_association_code'] = 'required|string|max: 255';

            /** info_provider  information */
            $rules['info_provider_name'] = 'nullable|string|max:100';
            $rules['info_provider_mobile'] = [
                "required",
                BaseModel::MOBILE_REGEX
            ];
            $rules['info_collector_name'] = 'nullable|string|max:100';
            $rules['info_collector_mobile'] = [
                "required",
                BaseModel::MOBILE_REGEX
            ];
        }

        /** If Industry has factory then the fields are required */
        if ($request->get('have_factory') == BaseModel::BOOLEAN_TRUE) {
            $rules["factory_address"] = "required|string|max:1200";
            $rules["factory_address_en"] = "nullable|string|max:800";
            $rules['factory_loc_division_id'] = [
                'nullable',
                'integer',
                'exists:loc_divisions,id,deleted_at,NULL'
            ];
            $rules['factory_loc_district_id'] = [
                'nullable',
                'integer',
                'exists:loc_districts,id,deleted_at,NULL'
            ];
            $rules['factory_loc_upazila_id'] = [
                'nullable',
                'integer',
                'exists:loc_upazilas,id,deleted_at,NULL'
            ];
            $rules["factory_web_site"] = "nullable|string|max:255";

            $rules["have_own_land"] = [
                "required",
                Rule::in(array_keys(NascibMember::LAND_TYPE))
            ];
            $rules['have_office_or_showroom'] = [
                "required",
                Rule::in([BaseModel::BOOLEAN_TRUE, BaseModel::BOOLEAN_FALSE])
            ];
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
