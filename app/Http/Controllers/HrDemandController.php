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
     * @param HrDemandService $hrDemandService
     */
    public function __construct(HrDemandService $hrDemandService)
    {
        $this->hrDemandService = $hrDemandService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getList()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function read()
    {
        //
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
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $hrDemand = HrDemand::findOrFail($id);
        $this->authorize('update', $hrDemand);

        $validated = $this->hrDemandService->validator($request, $id)->validate();
        $data = $this->hrDemandService->update($hrDemand, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Hr Demand updated successfully",
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

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function hrDemandApprovedByInstitute(Request $request, int $id) : JsonResponse{

            $hrDemandInstitute = HrDemandInstitute::findOrFail($id);
            //$this->authorize('update', $hrDemand);

            $validated = $this->hrDemandService->hrDemandApproveByInstituteValidator($request,$hrDemandInstitute->hr_demand_id)->validate();
            $data = $this->hrDemandService->hrDemandApprovedByInstitute($hrDemandInstitute, $validated);

            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Hr demand approved successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];

            return Response::json($response, ResponseAlias::HTTP_CREATED);

    }


}
