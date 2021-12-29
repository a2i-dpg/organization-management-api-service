<?php

namespace App\Http\Controllers;

use App\Models\JobSector;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
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
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', JobSector::class);

        $filter = $this->jobSectorService->filterValidator($request)->validate();
        $response = $this->jobSectorService->getJobSectorList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $jobSector = $this->jobSectorService->getOneJobSector($id);
        $this->authorize('view', $jobSector);
        $response = [
            "data" => $jobSector,
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
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('create', JobSector::class);

        $validated = $this->jobSectorService->validator($request)->validate();
        $data = $this->jobSectorService->store($validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "JobSector added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $jobSector = JobSector::findOrFail($id);

        $this->authorize('update', $jobSector);

        $validated = $this->jobSectorService->validator($request, $id)->validate();

        $data = $this->jobSectorService->update($jobSector, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "JobSector updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $JobSector = JobSector::findOrFail($id);
        $this->authorize('delete', $JobSector);

        $this->jobSectorService->destroy($JobSector);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "JobSector deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->jobSectorService->getTrashedJobSectorList($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $jobSector = JobSector::onlyTrashed()->findOrFail($id);
        $this->jobSectorService->restore($jobSector);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "JobSector restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function forceDelete(int $id): JsonResponse
    {
        $JobSector = JobSector::onlyTrashed()->findOrFail($id);
        $this->jobSectorService->forceDelete($JobSector);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "JobSector permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws ValidationException
     */
    public function getAreaOfBusiness(Request $request): JsonResponse
    {
        $filter = $this->jobSectorService->filterAreaOfBusinessValidator($request)->validate();
        $response = $this->jobSectorService->getAreaOfBusinessList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }
    public function getEducationalInstitutions(Request $request): JsonResponse
    {
        $filter = $this->jobSectorService->filterEducationInstitutionValidator($request)->validate();
        $response = $this->jobSectorService->getEducationalInstitutionList($filter, $this->startTime);

        return Response::json($response,ResponseAlias::HTTP_OK);

    }
}
