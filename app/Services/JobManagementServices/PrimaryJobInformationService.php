<?php

namespace App\Services\JobManagementServices;


use App\Models\AdditionalJobInformation;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\EmploymentType;
use App\Models\JobContactInformation;
use App\Models\PrimaryJobInformation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class PrimaryJobInformationService
{

    public function getJobList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $occupationId = $request['occupation_id'] ?? "";
        $jobSectorId = $request['job_sector_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $type = $request['type'] ?? "";
        $isRequestFromClientSide = !empty($request[BaseModel::IS_CLIENT_SITE_RESPONSE_KEY]);

        /** @var Builder $jobInformationBuilder */
        $jobInformationBuilder = PrimaryJobInformation::select([
            'primary_job_information.id',
            'primary_job_information.job_id',
            'primary_job_information.service_type',
            'primary_job_information.job_title',
            'primary_job_information.job_title_en',
            'primary_job_information.occupation_id',
            'primary_job_information.job_sector_id',
            'primary_job_information.industry_association_id',
            'primary_job_information.organization_id',
            'primary_job_information.institute_id',
            'primary_job_information.application_deadline',
            'primary_job_information.published_at',
            'primary_job_information.archived_at',
            'primary_job_information.application_deadline',
            'primary_job_information.id',
        ]);


        if (empty($isRequestFromClientSide)) {
            $jobInformationBuilder->acl();
        }

        $jobInformationBuilder->orderBy('primary_job_information.id', $order);

        if (is_numeric($rowStatus)) {
            $jobInformationBuilder->where('primary_job_information.row_status', $rowStatus);
        }
        if (is_numeric($occupationId)) {
            $jobInformationBuilder->where('primary_job_information.occupation_id', $occupationId);
        }
        if (is_numeric($jobSectorId)) {
            $jobInformationBuilder->where('primary_job_information.job_sector_id', $jobSectorId);
        }
        if (!empty($titleEn)) {
            $jobInformationBuilder->where('primary_job_information.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $jobInformationBuilder->where('primary_job_information.title', 'like', '%' . $title . '%');
        }

        if (!empty($type) && $type == PrimaryJobInformation::JOB_FILTER_TYPE_RECENT) {
            $jobInformationBuilder->whereDate('primary_job_information.published_at', '>', $startTime->subDays(30)->endOfDay());
            $jobInformationBuilder->whereDate('primary_job_information.application_deadline', '>', $startTime);
            $jobInformationBuilder->where(function ($builder) use ($startTime) {
                $builder->whereNull('primary_job_information.archived_at');
                $builder->orWhereDate('primary_job_information.archived_at', '>=', $startTime);
            });
            $jobInformationBuilder->active();

        }

//        if (!empty($type) && $type == PrimaryJobInformation::JOB_FILTER_TYPE_SKILL_MATCHING) {
//
//            DB::table('candidate_requirement_skill')->pluck('job_id')->whereIn()
//
//
//        }

        /** TODO:Change popular job search logic when job application process starts */

        if (!empty($type) && $type == PrimaryJobInformation::JOB_FILTER_TYPE_POPULAR) {
            $jobInformationBuilder->whereDate('primary_job_information.published_at', '>', $startTime->subDays(30)->endOfDay());
            $jobInformationBuilder->whereDate('primary_job_information.application_deadline', '>', $startTime);
            $jobInformationBuilder->where(function ($builder) use ($startTime) {
                $builder->whereNull('primary_job_information.archived_at');
                $builder->orWhereDate('primary_job_information.archived_at', '>=', $startTime);
            });
            $jobInformationBuilder->active();

        }

        /** If request from client side */

        if ($isRequestFromClientSide) {
            $jobInformationBuilder->whereDate('primary_job_information.published_at', '<=', $startTime);
            $jobInformationBuilder->whereDate('primary_job_information.application_deadline', '>=', $startTime);
            $jobInformationBuilder->where(function ($builder) use ($startTime) {
                $builder->whereNull('primary_job_information.archived_at');
                $builder->orWhereDate('primary_job_information.archived_at', '>=', $startTime);
            });

            $jobInformationBuilder->active();
        }

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $jobInformation = $jobInformationBuilder->paginate($pageSize);
            $paginateData = (object)$jobInformation->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $jobInformation = $jobInformationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $jobInformation->toArray()['data'] ?? $jobInformation->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());
        return $response;
    }

    public function getPrimaryJobInformationDetails(string $jobId): PrimaryJobInformation
    {
        /** @var PrimaryJobInformation|Builder $primaryJobInformationBuilder */
        $primaryJobInformationBuilder = PrimaryJobInformation::select([
            'primary_job_information.id',
            'primary_job_information.job_id',
            'primary_job_information.job_id',
            'primary_job_information.industry_association_id',
            'primary_job_information.organization_id',
            'primary_job_information.institute_id',
            'primary_job_information.service_type',
            'primary_job_information.job_title',
            'primary_job_information.job_title_en',
            'primary_job_information.no_of_vacancies',
            'primary_job_information.occupation_id',
            'occupations.title as occupation_title',
            'occupations.title_en as occupation_title_en',
            'primary_job_information.job_sector_id',
            'job_sectors.title as job_sector_title',
            'job_sectors.title_en as job_sector_title_en',
            'primary_job_information.application_deadline',
            'primary_job_information.is_apply_online',
            'primary_job_information.resume_receiving_option',
            'primary_job_information.email',
            'primary_job_information.is_use_nise3_mail_system',
            'primary_job_information.special_instruction_for_job_seekers',
            'primary_job_information.special_instruction_for_job_seekers_en',
            'primary_job_information.instruction_for_hard_copy',
            'primary_job_information.instruction_for_hard_copy_en',
            'primary_job_information.instruction_for_walk_in_interview',
            'primary_job_information.instruction_for_walk_in_interview_en',
            'primary_job_information.is_photograph_enclose_with_resume',
            'primary_job_information.is_prefer_video_resume',
            'primary_job_information.published_at',
            'primary_job_information.archived_at',
            'primary_job_information.created_at',
            'primary_job_information.updated_at',
        ]);

        $primaryJobInformationBuilder->where('primary_job_information.job_id', $jobId);
        $primaryJobInformationBuilder->join('occupations', function ($join) {
            $join->on('primary_job_information.occupation_id', '=', 'occupations.id')
                ->whereNull('occupations.deleted_at');
        });
        $primaryJobInformationBuilder->join('job_sectors', function ($join) {
            $join->on('primary_job_information.job_sector_id', '=', 'job_sectors.id')
                ->whereNull('job_sectors.deleted_at');
        });

        $primaryJobInformationBuilder->with(['employmentTypes' => function ($query) {
            $query->select('id', 'title');
        }]);

        return $primaryJobInformationBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return PrimaryJobInformation
     */
    public function store(array $data): PrimaryJobInformation
    {
        return PrimaryJobInformation::updateOrCreate(
            ['job_id' => $data['job_id']],
            $data
        );
    }

    /**
     * @param PrimaryJobInformation $primaryJobInformation
     * @param array $employmentTypes
     */
    public function syncWithEmploymentStatus(PrimaryJobInformation $primaryJobInformation, array $employmentTypes)
    {
        $employmentTypeId = EmploymentType::whereIn('id', $employmentTypes)->pluck('id')->toArray();
        if (!empty($employmentTypeId)) {
            $primaryJobInformation->employmentTypes()->sync($employmentTypes);
        }
    }

    public function JobListFilterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }


        return Validator::make($request->all(), [
            'job_title_en' => 'nullable|max:300|min:2',
            'job_title' => 'nullable|max:500|min:2',
            'occupation_id' => 'nullable|integer',
            'job_sector_id' => 'nullable|integer',
            'page' => 'nullable|integer|gt:0',
            'type' => [
                'nullable',
                'integer',
                Rule::in(PrimaryJobInformation::JOB_FILTER_TYPES)
            ],
            'page_size' => 'nullable|integer|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        $requestData = $request->all();
        $requestData["employment_type"] = is_array($requestData['employment_type']) ? $requestData['employment_type'] : explode(',', $requestData['employment_type']);
        $rules = [
            "job_id" => [
                "required",
                "string"
            ],
            "service_type" => [
                "required",
                Rule::in(array_keys(PrimaryJobInformation::JOB_SERVICE_TYPE))
            ],
            "job_title" => [
                "required",
                "string"
            ],
            "job_title_en" => [
                "nullable",
                "string"
            ],
            "is_number_of_vacancy_na" => [
                "required",
                Rule::in(PrimaryJobInformation::BOOLEAN_FLAG)
            ],
            "no_of_vacancies" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->offsetGet('is_number_of_vacancy_na') == PrimaryJobInformation::BOOLEAN_FLAG_FALSE;
                }),
                "nullable",
                "integer"
            ],
            "occupation_id" => [
                "required",
                "exists:occupations,id,deleted_at,NULL"
            ],
            "job_sector_id" => [
                "required",
                "exists:job_sectors,id,deleted_at,NULL"
            ],
            "organization_id" => [
                Rule::requiredIf(function () use ($authUser) {
                    return $authUser->isOrganizationUser();
                }),
                "nullable",
                "exists:organizations,id,deleted_at,NULL"
            ],
            "institute_id" => [
                Rule::requiredIf(function () use ($authUser) {
                    return $authUser->isInstituteUser();
                }),
                "nullable",
            ],
            "industry_association_id" => [
                Rule::requiredIf(function () use ($authUser) {
                    return $authUser->isIndustryAssociationUser();
                }),
                "nullable",
                "exists:industry_associations,id,deleted_at,NULL"
            ],
            "employment_type" => [
                "required",
                "array"
            ],
            "employment_type.*" => [
                "required",
                "exists:employment_types,id"
            ],
            "application_deadline" => [
                "required",
                "date"
            ],
            "is_apply_online" => [
                "nullable",
                Rule::in(PrimaryJobInformation::BOOLEAN_FLAG)
            ],
            "resume_receiving_option" => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->has('is_apply_online');
                }),
                "nullable",
                Rule::in(array_keys(PrimaryJobInformation::RESUME_RECEIVING_OPTION))
            ],
            "special_instruction_for_job_seekers" => [
                "nullable"
            ],
            "special_instruction_for_job_seekers_en" => [
                "nullable"
            ],
            "is_photograph_enclose_with_resume" => [
                "required",
                Rule::in(PrimaryJobInformation::BOOLEAN_FLAG)
            ],
            "is_prefer_video_resume" => [        // TODO :will work this field in future
//                Rule::requiredIf(function () use ($request) {
//                    return $request->resume_receiving_option == PrimaryJobInformation::RESUME_RECEIVING_OPTION[1];
//                }),
                "nullable",
                Rule::in(PrimaryJobInformation::BOOLEAN_FLAG)
            ]
        ];

        if (!empty($requestData['resume_receiving_option']) && $requestData['resume_receiving_option'] == PrimaryJobInformation::RESUME_RECEIVING_OPTION[1]) {
            {
                $rules["email"] = [
                    Rule::requiredIf(function () use ($request) {
                        return $request->offsetGet('resume_receiving_option') == PrimaryJobInformation::RESUME_RECEIVING_OPTION[1];
                    }),
                    "nullable",
                    "email"
                ];
                $rules["is_use_nise3_mail_system"] = [
                    "nullable"
                ];
            }
        }
        if (!empty($requestData['resume_receiving_option']) && $requestData['resume_receiving_option'] == PrimaryJobInformation::RESUME_RECEIVING_OPTION[2]) {
            $rules["instruction_for_hard_copy"] = [
                Rule::requiredIf(function () use ($request) {
                    return $request->offsetGet('resume_receiving_option') == PrimaryJobInformation::RESUME_RECEIVING_OPTION[2];
                }),
                "nullable"
            ];
            $rules["instruction_for_hard_copy_en"] = [
                "nullable"
            ];
        }
        if (!empty($requestData['resume_receiving_option']) && $requestData['resume_receiving_option'] == PrimaryJobInformation::RESUME_RECEIVING_OPTION[3]) {
            {
                $rules["instruction_for_walk_in_interview"] = [
                    Rule::requiredIf(function () use ($request) {
                        return $request->offsetGet('resume_receiving_option') == PrimaryJobInformation::RESUME_RECEIVING_OPTION[3];
                    }),
                    "nullable"
                ];
                $rules["instruction_for_walk_in_interview_en"] = [
                    "nullable"
                ];

            }
        }

        return Validator::make($requestData, $rules);
    }


    /**
     * @param array $data
     * @param PrimaryJobInformation $primaryJobInformation
     * @return bool
     * @throws Throwable
     */
    public function publishOrArchiveOrDraftJob(array $data, PrimaryJobInformation $primaryJobInformation): bool
    {
        if ($data['status'] == PrimaryJobInformation::STATUS_PUBLISH) {
            $primaryJobInformation->published_at = Carbon::now()->format('Y-m-d H:i:s');
            $primaryJobInformation->archived_at = null;
        }
        if ($data['status'] == PrimaryJobInformation::STATUS_ARCHIVE) {
            $primaryJobInformation->archived_at = Carbon::now()->format('Y-m-d H:i:s');
        }
        if ($data['status'] == PrimaryJobInformation::STATUS_DRAFT) {
            $primaryJobInformation->archived_at = null;
            $primaryJobInformation->published_at = null;
        }
        return $primaryJobInformation->saveOrFail();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function publishOrArchiveValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'status' => [
                'integer',
                Rule::in(PrimaryJobInformation::PUBLISH_OR_ARCHIVE_STATUSES)
            ]

        ];
        return Validator::make($request->all(), $rules);
    }

    public function isJobPublishOrArchiveApplicable(string $jobId): bool
    {
        $isPrimaryJobInformationComplete = (bool)PrimaryJobInformation::where('job_id', $jobId)->count('id');
        $isAdditionalJobInformationComplete = (bool)AdditionalJobInformation::where('job_id', $jobId)->count('id');
        $isCandidateRequirementComplete = (bool)CandidateRequirement::where('job_id', $jobId)->count('id');
        $isCompanyInfoVisibilityComplete = (bool)CompanyInfoVisibility::where('job_id', $jobId)->count('id');
        $isJobContactInformationComplete = (bool)JobContactInformation::where('job_id', $jobId)->count('id');
        return $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete && $isCandidateRequirementComplete && $isCompanyInfoVisibilityComplete && $isJobContactInformationComplete;
    }


}
