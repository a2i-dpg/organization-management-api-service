<?php

namespace App\Http\Controllers;

use App\Services\ExamDegreeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExamDegreeController extends Controller
{
    public ExamDegreeService $examDegreeService;
    private Carbon $startTime;


    public function __construct(ExamDegreeService $examDegreeService)
    {
        $this->examDegreeService = $examDegreeService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->examDegreeService->filterValidator($request)->validate();
        $response = $this->examDegreeService->getExamDegreeList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
