<?php

namespace App\Http\Controllers;

use App\Models\JobSector;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\JobSectorService;

/**
 * Class JobSectorController
 * @package App\Http\Controllers
 */
class JobSectorController extends Controller
{
    /**
     * @var JobSectorService
     */
    public JobSectorService $jobSectorService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * JobSectorController constructor.
     * @param JobSectorService $jobSectorService
     */
    public function __construct(JobSectorService $jobSectorService)
    {
        $this->jobSectorService = $jobSectorService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request)
    {
        try {
            $response = $this->jobSectorService->getJobSectorList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Display the specified resource
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id):JsonResponse
    {
        try {
            $response = $this->jobSectorService->getOneJobSector($id,$this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    function store(Request $request):JsonResponse
    {
        $validated = $this->jobSectorService->validator($request)->validate();
        try {
            $data = $this->jobSectorService->store($validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Job Sector added successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id):JsonResponse
    {
        $jobSector = JobSector::findOrFail($id);
        $validated = $this->jobSectorService->validator($request,$id)->validate();

        try {
            $data = $this->jobSectorService->update($jobSector, $validated);
            $response = [
                'data' => $data ? $data : null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Job Sector updated successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id):JsonResponse
    {
        $JobSector = JobSector::findOrFail($id);
        try {
            $this->jobSectorService->destroy($JobSector);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Job Sector deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
