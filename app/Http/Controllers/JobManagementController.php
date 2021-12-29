<?php

namespace App\Http\Controllers;


use App\Models\CompanyInfoVisibility;
use App\Models\AdditionalJobInformation;
use App\Models\PrimaryJobInformation;
use App\Services\JobManagementServices\AreaOfBusinessService;
use App\Services\JobManagementServices\EducationInstitutionsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use function Symfony\Component\Translation\t;


class JobManagementController extends Controller
{
    /**
     * @var AreaOfBusinessService
     */
    public AreaOfBusinessService $areaOfBusinessService;
    /**
     * @var EducationInstitutionsService
     */
    public EducationInstitutionsService $educationInstitutionsService;

    private Carbon $startTime;


    public function __construct(AreaOfBusinessService $areaOfBusinessService , EducationInstitutionsService $educationInstitutionsService)
    {
        $this->areaOfBusinessService = $areaOfBusinessService;
        $this->educationInstitutionsService = $educationInstitutionsService;
        $this->startTime=Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getAreaOfBusiness(Request $request): JsonResponse
    {
        $filter = $this->areaOfBusinessService->filterAreaOfBusinessValidator($request)->validate();
        $response = $this->areaOfBusinessService->getAreaOfBusinessList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getEducationalInstitutions(Request $request): JsonResponse
    {
        $filter = $this->educationInstitutionsService->filterEducationInstitutionValidator($request)->validate();
        $response = $this->educationInstitutionsService->getEducationalInstitutionList($filter, $this->startTime);

        return Response::json($response,ResponseAlias::HTTP_OK);

    }
}
