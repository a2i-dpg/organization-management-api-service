<?php

namespace App\Http\Controllers;

use App\Services\FourIRServices\FourIRSkillDevelopmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRSkillDevelopmentController extends Controller
{
    public FourIRSkillDevelopmentService $fourIRCourseDevelopmentService;
    private Carbon $startTime;


    public function __construct(FourIRSkillDevelopmentService $fourIRCourseDevelopmentService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRCourseDevelopmentService = $fourIRCourseDevelopmentService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable|ValidationException
     */
    public function getList(Request $request): JsonResponse
    {

        $filter = $this->fourIRCourseDevelopmentService->filterValidator($request)->validate();
        $response = $this->fourIRCourseDevelopmentService->getFourIRCourseDevelopmentList($filter);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
