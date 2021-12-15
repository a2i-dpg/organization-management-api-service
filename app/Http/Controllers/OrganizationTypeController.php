<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\OrganizationType;
use App\Services\OrganizationTypeService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class OrganizationTypeController
 * @package App\Http\Controllers
 */
class OrganizationTypeController extends Controller
{
    /**
     * @var OrganizationTypeService
     */
    public OrganizationTypeService $organizationTypeService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * OrganizationTypeController constructor.
     * @param OrganizationTypeService $organizationTypeService
     */
    public function __construct(OrganizationTypeService $organizationTypeService)
    {
        $this->organizationTypeService = $organizationTypeService;
        $this->startTime = Carbon::now();
    }

    /**
     *Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        //$this->authorize('viewAny', OrganizationType::class);

        $filter = $this->organizationTypeService->filterValidator($request)->validate();
        $response = $this->organizationTypeService->getAllOrganizationType($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Display a specified resource
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $organizationType = $this->organizationTypeService->getOneOrganizationType($id);
        $this->authorize('view', $organizationType);
        $response = [
            "data" => $organizationType,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', OrganizationType::class);

        $validated = $this->organizationTypeService->validator($request)->validate();
        $data = $this->organizationTypeService->store($validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Organization Type added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $organizationType = OrganizationType::findOrFail($id);
        $this->authorize('update', $organizationType);

        $validated = $this->organizationTypeService->validator($request, $id)->validate();
        $data = $this->organizationTypeService->update($organizationType, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization Type updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $organizationType = OrganizationType::findOrFail($id);
        $this->authorize('delete', $organizationType);

        $this->organizationTypeService->destroy($organizationType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization Type deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->organizationTypeService->getAllTrashedOrganizationUnit($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $organizationType = OrganizationType::onlyTrashed()->findOrFail($id);
        $this->organizationTypeService->restore($organizationType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "OrganizationType restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $organizationType = OrganizationType::onlyTrashed()->findOrFail($id);
        $this->organizationTypeService->forceDelete($organizationType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "OrganizationType permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
