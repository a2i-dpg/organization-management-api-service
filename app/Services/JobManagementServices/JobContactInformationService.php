<?php

namespace App\Services\JobManagementServices;


use App\Models\JobContactInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobContactInformationService
{


    /**
     * @param string $jobId
     * @return JobContactInformation
     */
    public function getContactInformation(string $jobId): JobContactInformation
    {
        return JobContactInformation::where('job_id', $jobId)->firstOrFail();
    }

    /**
     * @param array $data
     * @return JobContactInformation
     */
    public function store(array $data): JobContactInformation
    {
        return JobContactInformation::updateOrCreate(
            ['job_id' => $data['job_id']],
            $data
        );

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validate(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "job_id" => [
                "required",
//                "exists:primary_job_information,job_id"
            ],
            "contact_person_id" => [
                "required"
            ]
        ];

        return Validator::make($request->all(), $rules);

    }
}
