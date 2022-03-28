<?php

namespace App\Services;

use App\Exceptions\HttpErrorException;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use App\Models\IndustryAssociation;
use App\Models\NascibMember;
use App\Models\Organization;
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
class IndustryAssociationService
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
    public function store(IndustryAssociation $industryAssociation, array $data): IndustryAssociation
    {
        $industryAssociation->fill($data);
        $industryAssociation->save();
        return $industryAssociation;
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
     * @return array
     */
    public function industryAssociationMembershipApproval(array $data, Organization $organization): array
    {
        $status = 0;
        $message = 'Unprocessable Request';

        if ($organization->row_status == BaseModel::ROW_STATUS_PENDING) {
            $message = 'You are not approved as organization member, so unable to process this request. Please complete the improvement process.';
        }

        $industryAssociationOrganization = $organization->industryAssociations();
        $paymentStatusCheckData = $industryAssociationOrganization->firstOrFail()->pivot;
        if ($paymentStatusCheckData->additional_info_model_name && $paymentStatusCheckData->payment_status == BaseModel::PAYMENT_PENDING) {
            $message = 'Payment is till now pending, so unable to process this request. Please complete payment.';
        } else {
            $status = $organization->industryAssociations()->updateExistingPivot($data['industry_association_id'], [
                'row_status' => BaseModel::ROW_STATUS_ACTIVE
            ]);
            if ($status) {
                $message = 'Successfully add as a member';
            }
        }
        return [
            $status,
            $message
        ];

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

        if (!empty($request->offsetGet('skills'))) {
            $skillIds = is_array($request->offsetGet('skills')) ? $request->offsetGet('skills') : explode(',', $request->offsetGet('skills'));
            $request->offsetSet('skills', $skillIds);
        }

        if (!empty($request->offsetGet('phone_numbers'))) {
            $phoneNumber = is_array($request->offsetGet('phone_numbers')) ? $request->offsetGet('phone_numbers') : explode(',', $request->offsetGet('phone_numbers'));
            $request->offsetSet('phone_numbers', $phoneNumber);
        }
        if (!empty($request->offsetGet('mobile_numbers'))) {
            $mobileNumber = is_array($request->offsetGet('mobile_numbers')) ? $request->offsetGet('mobile_numbers') : explode(',', $request->offsetGet('mobile_numbers'));
            $request->offsetSet('mobile_numbers', $mobileNumber);
        }



        $rules = [
            'trade_id' => [
                'required',
                'int',
                'exists:trades,id,deleted_at,NULL'
            ],
            'permission_sub_group_id' => [
                Rule::requiredIf(function () use ($id) {
                    return is_null($id);
                }),
                'nullable',
                'integer'
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
            "location_latitude" => [
                'nullable',
                'string',
            ],
            "location_longitude" => [
                'nullable',
                'string',
            ],
            "google_map_src" => [
                'nullable',
                'string',
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
            "country" => [
                "nullable",
                "string",
                "min:2"
            ],
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
            ],
            'mobile_numbers' => [
                'nullable',
                'array'
            ],
            'mobile_numbers.*' => [
                'nullable',
                'string',
                BaseModel::MOBILE_REGEX
            ],



            'phone_numbers' => [
                'nullable',
                'array'
            ],
            'phone_numbers.*' => [
                'nullable',
                'string',
            ],
            'email' => [
                'required',
                'email',
                'max:191'
            ],
            'fax_no' => [
                'nullable',
                'string',
                'max: 30',
            ],
            'trade_number' => [
                'required',
                'string',
                'max:100'
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
                    ->ignore($id)
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
            'domain' => [
                'nullable',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'string',
                'max:191',
                Rule::unique('industry_associations', 'domain')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            "skills" => [
                "nullable",
                "array",
                "min:1"
            ],
            "skills.*" => [
                "required",
                "int",
                "exists:skills,id,deleted_at,NULL"
            ],
            'logo' => [
                'required',
                'string',
            ],
            'row_status' => [
                'nullable',
                Rule::in(BaseModel::ROW_STATUSES),
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    public function industryAssociationProfileUpdateValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if (!empty($request->offsetGet('skills'))) {
            $skillIds = is_array($request->offsetGet('skills')) ? $request->offsetGet('skills') : explode(',', $request->offsetGet('skills'));
            $request->offsetSet('skills', $skillIds);
        }

        $rules = [
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
            "location_latitude" => [
                'nullable',
                'string',
            ],
            "location_longitude" => [
                'nullable',
                'string',
            ],
            "google_map_src" => [
                'nullable',
                'string',
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
            "skills" => [
                "nullable",
                "array",
                "min:1"
            ],
            "skills.*" => [
                "required",
                "int",
                "exists:skills,id,deleted_at,NULL"
            ],
            'logo' => [
                'required',
                'string',
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
            ]
        ];
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
