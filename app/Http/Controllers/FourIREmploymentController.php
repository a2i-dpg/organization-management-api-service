<?php

namespace App\Http\Controllers;

use App\Models\FourIREmployment;
use App\Models\FourIRInitiative;
use App\Services\FourIRServices\FourIrEmploymentService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

//TODO: FourIR Employment need to check
class FourIREmploymentController extends Controller
{
    public FourIrEmploymentService $fourIrEmploymentService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeController constructor.
     *
     * @param FourIrEmploymentService $fourIrEmploymentService
     */
    public function __construct(FourIrEmploymentService $fourIrEmploymentService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrEmploymentService = $fourIrEmploymentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAnyInitiativeStep', FourIRInitiative::class);


        $filter = $this->fourIrEmploymentService->filterValidator($request)->validate();
        $response = $this->fourIrEmploymentService->getFourIrEmploymentList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrEmployment = $this->fourIrEmploymentService->getOneFourIrEmployment($id);
        $this->authorize('viewSingleInitiativeStep', $fourIrEmployment);
        $response = [
            "data" => $fourIrEmployment,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('creatInitiativeStep', FourIRInitiative::class);
        $validated = $this->fourIrEmploymentService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIrEmploymentService->store($validated);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Employment added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrEmployment = FourIREmployment::findOrFail($id);
        $this->authorize('updateInitiativeStep', $fourIrEmployment);
        $validated = $this->fourIrEmploymentService->validator($request, $id)->validate();
        $data = $this->fourIrEmploymentService->update($fourIrEmployment, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Employment updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $fourIrEmployment = FourIREmployment::findOrFail($id);
        $this->authorize('deleteInitiativeStep', $fourIrEmployment);
        $this->fourIrEmploymentService->destroy($fourIrEmployment);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Employment deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
