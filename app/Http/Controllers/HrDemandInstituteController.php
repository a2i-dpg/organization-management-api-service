<?php

namespace App\Http\Controllers;

use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use App\Services\HrDemandInstituteService;
use App\Services\HrDemandService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class HrDemandInstituteController extends Controller
{
    public HrDemandService $hrDemandService;
    public HrDemandInstituteService $hrDemandInstituteService;
    private Carbon $startTime;

    /**
     * HrDemandInstituteController constructor.
     * @param HrDemandService $hrDemandService
     * @param HrDemandInstituteService $hrDemandInstituteService
     */
    public function __construct(HrDemandService $hrDemandService, HrDemandInstituteService $hrDemandInstituteService)
    {
        $this->hrDemandService = $hrDemandService;
        $this->hrDemandInstituteService = $hrDemandInstituteService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the Hr Demand Institutes to Institute Admin.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAnyByInstitute', HrDemand::class);

        $filter = $this->hrDemandService->filterValidator($request)->validate();
        $response = $this->hrDemandService->getHrDemandList($filter, $this->startTime);

        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Show the resource to Institute Admin user.
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $humanResource = $this->hrDemandService->getOneHrDemand($id);
        $this->authorize('viewByInstitute', $humanResource);
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
     * Update the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);
        $this->authorize('update', HrDemand::class);

        $validated = $this->hrDemandInstituteService->updateValidator($request, $hrDemandInstitute, $id)->validate();
        $data = $this->hrDemandInstituteService->update($hrDemandInstitute, $validated);

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
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function hrDemandApprovedByInstitute(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);

        $this->authorize('updateByInstitute', $hrDemandInstitute);

        $validated = $this->hrDemandInstituteService->hrDemandApprovedByInstituteValidator($request, $hrDemandInstitute->hr_demand_id)->validate();
        $data = $this->hrDemandInstituteService->hrDemandApprovedByInstitute($hrDemandInstitute, $validated);

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

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function hrDemandRejectedByInstitute(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);

        $this->authorize('updateByInstitute', $hrDemandInstitute);

        $data = $this->hrDemandInstituteService->hrDemandRejectedByInstitute($hrDemandInstitute);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Hr demand rejected successfully",
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
     * @throws ValidationException|AuthorizationException
     */
    public function hrDemandApprovedByIndustryAssociation(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);

        $this->authorize('update', HrDemand::class);

        $validated = $this->hrDemandInstituteService->hrDemandApprovedByIndustryAssociationValidator($request, $hrDemandInstitute)->validate();
        $data = $this->hrDemandInstituteService->hrDemandApprovedByIndustryAssociation($hrDemandInstitute, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Hr demand approved successfully by Industry Association User",
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
     */
    public function hrDemandRejectedByIndustryAssociation(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);

        $this->authorize('update', HrDemand::class);

        $data = $this->hrDemandInstituteService->hrDemandRejectedByIndustryAssociation($hrDemandInstitute);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Hr demand rejected successfully by Industry Association User",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
