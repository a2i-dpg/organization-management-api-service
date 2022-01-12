<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Services\JobManagementServices\CandidateRequirementsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CandidateRequirementController extends Controller
{

    public CandidateRequirementsService $candidateRequirementsService;
    public Carbon $startTime;

    /**
     * @param CandidateRequirementsService $candidateRequirementsService
     */
    public function __construct(CandidateRequirementsService $candidateRequirementsService)
    {
        $this->candidateRequirementsService = $candidateRequirementsService;
        $this->startTime = Carbon::now();

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function storeCandidateRequirements(Request $request): JsonResponse
    {
        $validatedData = $this->candidateRequirementsService->validator($request)->validate();

        $degrees = $validatedData['degrees'] ?? [];
        $preferredEducationalInstitution = $validatedData['preferred_educational_institution'] ?? [];

        $training = $validatedData['training'] ?? [];
        $professionalCertification = $validatedData['professional_certification'] ?? [];
        $areaOfExperience = $validatedData['area_of_experience'] ?? [];
        $areaOfBusiness = $validatedData['area_of_business'] ?? [];
        $skills = $validatedData['skills'] ?? [];
        $gender = $validatedData['gender'] ?? [];

        DB::beginTransaction();
        try {
            $candidateRequirements = $this->candidateRequirementsService->store($validatedData);
            Log::info("------>",$candidateRequirements->toArray());
            $this->candidateRequirementsService->syncWithDegrees($candidateRequirements, $degrees);
            $this->candidateRequirementsService->syncWithPreferredEducationalInstitution($candidateRequirements, $preferredEducationalInstitution);
            $this->candidateRequirementsService->syncWithTraining($candidateRequirements, $training);
            $this->candidateRequirementsService->syncWithProfessionalCertification($candidateRequirements, $professionalCertification);
            $this->candidateRequirementsService->syncWithAreaOfExperience($candidateRequirements, $areaOfExperience);
            $this->candidateRequirementsService->syncWithAreaOfBusiness($candidateRequirements, $areaOfBusiness);
            $this->candidateRequirementsService->syncWithSkills($candidateRequirements, $skills);
            $this->candidateRequirementsService->syncWithGender($candidateRequirements, $gender);

            $response = [
                "data" => $candidateRequirements,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "CandidateRequirements successfully submitted",
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
    public function getCandidateRequirements(string $jobId): JsonResponse
    {
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
        if ($step >= BaseModel::FORM_STEPS['CandidateRequirement']) {
            $candidateRequirements = $this->candidateRequirementsService->getCandidateRequirements($jobId);
            $candidateRequirements["latest_step"] = $step;
            $response["data"] = $candidateRequirements;
            $response['_response_status']["query_time"] = $this->startTime->diffInSeconds(Carbon::now());
        }
        return Response::json($response, ResponseAlias::HTTP_OK);

    }
}
