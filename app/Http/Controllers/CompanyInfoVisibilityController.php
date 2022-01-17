<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\CompanyInfoVisibility;
use App\Models\JobManagement;
use App\Models\PrimaryJobInformation;
use App\Services\JobManagementServices\CompanyInfoVisibilityService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CompanyInfoVisibilityController extends Controller
{
    public CompanyInfoVisibilityService $companyInfoVisibilityService;
    public Carbon $startTime;

    /**
     * @param CompanyInfoVisibilityService $companyInfoVisibilityService
     */
    public function __construct(CompanyInfoVisibilityService $companyInfoVisibilityService,)
    {
        $this->companyInfoVisibilityService = $companyInfoVisibilityService;
        $this->startTime = Carbon::now();

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function storeCompanyInfoVisibility(Request $request): JsonResponse
    {

        $this->authorize('create', JobManagement::class);

        $validatedData = $this->companyInfoVisibilityService->companyInfoVisibilityValidator($request)->validate();
        $companyInfoVisibility = $this->companyInfoVisibilityService->storeOrUpdate($validatedData);
        $response = [
            "data" => $companyInfoVisibility,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Company Info Visibility successfully submitted",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param string $jobId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getCompanyInfoVisibility(string $jobId): JsonResponse
    {
//        $primaryJobInformation = PrimaryJobInformation::where('job_id', $jobId)->firstOrFail();
//
//        $companyInfoVisibility = CompanyInfoVisibility::where('job_id', $jobId)->firstOrFail();
//
//        $this->authorize('view', [JobManagement::class, $primaryJobInformation, $companyInfoVisibility]);

        $step = JobManagementController::lastAvailableStep($jobId);
        $response = [
            "data" => [
                "latest_step" => $step
            ],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        if ($step >= BaseModel::FORM_STEPS['CompanyInfoVisibility']) {
            $companyInfoVisibility = $this->companyInfoVisibilityService->getCompanyInfoVisibility($jobId);
            $companyInfoVisibility["latest_step"] = $step;
            $response["data"] = $companyInfoVisibility;
            $response['_response_status']["query_time"] = $this->startTime->diffInSeconds(Carbon::now());
        }
        return Response::json($response, ResponseAlias::HTTP_OK);

    }
}
