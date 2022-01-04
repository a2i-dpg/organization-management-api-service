<?php

namespace App\Services\JobManagementServices;


use App\Models\AdditionalJobInformation;
use App\Models\AdditionalJobInformationJobLocation;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\EmploymentType;
use App\Models\GalleryImageVideo;
use App\Models\JobContactInformation;
use App\Models\PrimaryJobInformation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class PrimaryJobInformationService
{

    public function getPrimaryJobInformationDetails(string $jobId): Model|Builder
    {
        /** @var Builder $primaryJobInformationBuilder */
        $primaryJobInformationBuilder = PrimaryJobInformation::select([
            'primary_job_information.id',
            'primary_job_information.job_id',
            'primary_job_information.service_type',
            'primary_job_information.job_title',
            'primary_job_information.job_title_en',
            'primary_job_information.no_of_vacancies',
            'primary_job_information.job_category_id',
            'occupations.title as job_category_title',
            'occupations.title_en as job_category_title_en',
            'primary_job_information.application_deadline',
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
            'primary_job_information.created_at',
            'primary_job_information.updated_at',
        ]);

        $primaryJobInformationBuilder->where('primary_job_information.job_id', $jobId);
        $primaryJobInformationBuilder->join('occupations', function ($join) {
            $join->on('primary_job_information.job_category_id', '=', 'occupations.id')
                ->whereNull('occupations.deleted_at');
        });

        $primaryJobInformationBuilder->with('employmentTypes');

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


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
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
            "job_category_id" => [
                "required",
                "exists:occupations,id"
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
