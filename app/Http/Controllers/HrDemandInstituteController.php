<?php

namespace App\Http\Controllers;

use App\Models\HrDemand;
use App\Models\HrDemandInstitute;
use App\Services\HrDemandInstituteService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class HrDemandInstituteController extends Controller
{
    public HrDemandInstituteService $hrDemandInstituteService;
    private Carbon $startTime;

    /**
     * HrDemandInstituteController constructor.
     * @param HrDemandInstituteService $hrDemandInstituteService
     */
    public function __construct(HrDemandInstituteService $hrDemandInstituteService)
    {
        $this->hrDemandInstituteService = $hrDemandInstituteService;
        $this->startTime = Carbon::now();
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
