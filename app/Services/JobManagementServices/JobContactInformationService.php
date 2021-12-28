<?php

namespace App\Services\JobManagementServices;


use Illuminate\Http\Request;

class JobContactInformationService
{

    public function validate(Request $request)
    {
        $rules = [
            "job_id" => [
                "required",
                "exists:primary_job_information,job_id"
            ],
        ];

    }
}
