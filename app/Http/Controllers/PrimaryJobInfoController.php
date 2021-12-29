<?php

namespace App\Http\Controllers;

use App\Models\PrimaryJobInformation;
use App\Services\JobManagementServices\PrimaryJobInformationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class PrimaryJobInfoController extends Controller
{
    public PrimaryJobInformationService $primaryJobInformationService;
    public Carbon $startTime;

    /**
     * @param PrimaryJobInformationService $primaryJobInformationService
     */
    public function __construct(PrimaryJobInformationService $primaryJobInformationService)
    {
        $this->primaryJobInformationService = $primaryJobInformationService;
        $this->startTime = Carbon::now();

    }

    public function getJobId(): string
    {
        return PrimaryJobInformation::jobId();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function storePrimaryJobInformation(Request $request): JsonResponse
    {
        $validatedData = $this->primaryJobInformationService->validator($request)->validate();
        $employmentTypes = $validatedData['employment_type'];
        DB::beginTransaction();
        try {
            $primaryJobInformation = $this->primaryJobInformationService->store($validatedData);
            $this->primaryJobInformationService->syncWithEmploymentStatus($primaryJobInformation, $employmentTypes);
            $response = [
                "data" => $primaryJobInformation,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "PrimaryJobInformation successfully submitted",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param string $jobId
     * @return JsonResponse
     */
    public function getPrimaryJobInformation(string $jobId): JsonResponse
    {
        $primaryJobInformation = $this->primaryJobInformationService->getPrimaryJobInformationDetails($jobId);
        $response = [
            "data" => $primaryJobInformation,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }
}
