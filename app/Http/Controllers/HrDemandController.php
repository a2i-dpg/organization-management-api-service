<?php

namespace App\Http\Controllers;

use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use App\Services\HrDemandService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class HrDemandController extends Controller
{
    public HrDemandService $hrDemandService;
    private Carbon $startTime;

    /**
     * HrDemandController constructor.
     *
     * @param HrDemandService $hrDemandService
     */
    public function __construct(HrDemandService $hrDemandService)
    {
        $this->hrDemandService = $hrDemandService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the Hr Demand Institutes to Industry Association User.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', HrDemand::class);

        $filter = $this->hrDemandService->filterValidator($request)->validate();
        $response = $this->hrDemandService->getHrDemandList($filter, $this->startTime);

        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Display a listing of the Hr Demand Institutes (Approved by Institute) to Industry Association User.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function getListApprovedByInstitute(Request $request): JsonResponse
    {
        $this->authorize('viewAny', HrDemand::class);

        $filter = $this->hrDemandService->filterValidator($request)->validate();
        $filter[HrDemand::SHOW_ONLY_HR_DEMAND_INSTITUTES_APPROVED_BY_TSP_KEY] = true;
        $response = $this->hrDemandService->getHrDemandList($filter, $this->startTime);

        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource to Industry Association User.
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $humanResource = $this->hrDemandService->getOneHrDemand($id);
        $this->authorize('view', HrDemand::class);
        $response = [
            "data" => $humanResource,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', HrDemand::class);
        $validatedData = $this->hrDemandService->validator($request)->validate();
        $data = $this->hrDemandService->store($validatedData);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Hr demand added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id) : JsonResponse
    {
        $hrDemand = HrDemand::findOrFail($id);
        $this->authorize('delete', $hrDemand);

        $this->hrDemandService->destroy($hrDemand);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "HR Demand Deleted Successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
