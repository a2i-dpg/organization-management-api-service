<?php

namespace App\Http\Controllers;

use App\Models\FourIRTagline;
use App\Services\FourIRServices\FourIrTaglineService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRTaglineController extends Controller
{
    public FourIrTaglineService $fourIrTaglineService;
    private Carbon $startTime;

    /**
     * FourIRTaglineController constructor.
     *
     * @param FourIrTaglineService $fourIrTaglineService
     */
    public function __construct(FourIrTaglineService $fourIrTaglineService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrTaglineService = $fourIrTaglineService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', FourIRTagline::class);

        $filter = $this->fourIrTaglineService->filterValidator($request)->validate();
        $response = $this->fourIrTaglineService->getFourIRTaglineList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $fourIrTagline = $this->fourIrTaglineService->getOneFourIRTagline($id);
        $this->authorize('view', $fourIrTagline);
        $response = [
            "data" => $fourIrTagline,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response,ResponseAlias::HTTP_OK);
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
         $this->authorize('create', FourIRTagline::class);
        $validated = $this->fourIrTaglineService->validator($request)->validate();
        $data = $this->fourIrTaglineService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir Tagline added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrTagline = FourIRTagline::findOrFail($id);
         $this->authorize('update', $fourIrTagline);
        $validated = $this->fourIrTaglineService->validator($request, $id)->validate();
        $data = $this->fourIrTaglineService->update($fourIrTagline, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Tagline updated successfully",
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
        $fourIrTagline = FourIRTagline::findOrFail($id);
       $this->authorize('delete', $fourIrTagline);
        $this->fourIrTaglineService->destroy($fourIrTagline);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Tagline deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
