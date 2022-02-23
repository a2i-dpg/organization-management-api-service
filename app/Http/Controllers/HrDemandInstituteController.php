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
use Throwable;

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
     * Display a listing of the Hr Demand Institutes to Industry Association User & Institute Admin.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', HrDemandInstitute::class);

        $filter = $this->hrDemandInstituteService->filterValidator($request)->validate();
        $response = $this->hrDemandInstituteService->getHrDemandInstituteList($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Display a listing of the Hr Demand Institutes to Industry Association User & Institute Admin.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
    {
        $this->authorize('view', HrDemandInstitute::class);

        $response = $this->hrDemandInstituteService->getOneHrDemandInstitute($id);

        $responsePayload = [
            "data" => $response,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($responsePayload, ResponseAlias::HTTP_OK);
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException|Throwable
     */
    public function hrDemandApprovedByInstitute(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);

        $this->authorize('updateByInstitute', $hrDemandInstitute);

        $validated = $this->hrDemandInstituteService->hrDemandApprovedByInstituteValidator($request, $hrDemandInstitute)->validate();
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
     * @throws AuthorizationException|ValidationException|Throwable
     */
    public function hrDemandRejectedByInstitute(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);

        throw_if(empty($hrDemandInstitute->institute_id), ValidationException::withMessages([
            "All institute row can't be updated by this operation!"
        ]));

        throw_if($hrDemandInstitute->vacancy_approved_by_industry_association != 0, ValidationException::withMessages([
            "Already approved by Industry Association User!"
        ]));

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
     * @throws Throwable
     */
    public function hrDemandApprovedByIndustryAssociation(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);

        throw_if(empty($hrDemandInstitute->institute_id), ValidationException::withMessages([
            "All institute row can't be updated by this operation!"
        ]));

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
     * @throws Throwable
     */
    public function hrDemandRejectedByIndustryAssociation(Request $request, int $id): JsonResponse
    {
        $hrDemandInstitute = HrDemandInstitute::findOrFail($id);

        throw_if(empty($hrDemandInstitute->institute_id), ValidationException::withMessages([
            "All institute row can't be updated by this operation!"
        ]));

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
