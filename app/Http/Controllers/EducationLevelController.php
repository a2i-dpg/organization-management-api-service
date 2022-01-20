<?php

namespace App\Http\Controllers;

use App\Services\EducationLevelService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EducationLevelController extends Controller
{
    public EducationLevelService $educationLevelService;
    private Carbon $startTime;


    public function __construct(EducationLevelService $educationLevelService)
    {
        $this->educationLevelService = $educationLevelService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->educationLevelService->filterValidator($request)->validate();
        $response = $this->educationLevelService->getEducationLevelList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
