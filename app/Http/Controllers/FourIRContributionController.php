<?php

namespace App\Http\Controllers;

use App\Models\FourIRContribution;
use App\Services\FourIRServices\FourIRContributionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FourIRContributionController extends Controller
{
    public FourIRContributionService $fourIRContributionService;
    public Carbon $startTime;

    /**
     * @param FourIRContributionService $fourIRContributionService
     */
    public function __construct(FourIRContributionService $fourIRContributionService)
    {
        $this->fourIRContributionService = $fourIRContributionService;
        $this->startTime = Carbon::now();
    }

    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', FourIRContribution::class);
        $filter = $this->fourIRContributionService->filterValidator($request)->validate();
        $response = $this->fourIRContributionService->getContributionList($filter);
        return Response::json($response, $response['_response_status']['code']);
    }

    public function read(int $id): JsonResponse
    {
        $contribution = $this->fourIRContributionService->getOne($id);
        $this->authorize('view', $contribution);
        $response = [
            "data" => $contribution,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('create', FourIRContribution::class);
        $validateData = $this->fourIRContributionService->valiation($request)->validate();
        $validateData['user_id'] = Auth::id();

        $responseData = $this->fourIRContributionService->createOrUpdate($validateData);
        $response = [
            "data" => $responseData,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }


    public function destroy(int $id)
    {

    }


}
