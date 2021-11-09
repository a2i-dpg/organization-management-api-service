<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Models\OrganizationUnitType;
use App\Services\OrganizationUnitTypeService;

/**
 * Class OrganizationUnitTypeController
 * @package App\Http\Controllers
 */
class OrganizationUnitTypeController extends Controller
{
    /**
     * @var OrganizationUnitTypeService
     */
    public OrganizationUnitTypeService $organizationUnitTypeService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * OrganizationUnitTypeController constructor.
     * @param OrganizationUnitTypeService $organizationUnitTypeService
     */
    public function __construct(OrganizationUnitTypeService $organizationUnitTypeService)
    {
        $this->organizationUnitTypeService = $organizationUnitTypeService;
        $this->startTime = Carbon::now();
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', OrganizationUnitType::class);

        $filter = $this->organizationUnitTypeService->filterValidator($request)->validate();
        $response = $this->organizationUnitTypeService->getAllOrganizationUnitType($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * * Display a specified resource
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $organizationUnitType = $this->organizationUnitTypeService->getOneOrganizationUnitType($id);
        $this->authorize('view', $organizationUnitType);
        $response = [
            "data" => $organizationUnitType ?: [],
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
     * @throws Throwable
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('create', OrganizationUnitType::class);

        $validated = $this->organizationUnitTypeService->validator($request)->validate();
        $data = $this->organizationUnitTypeService->store($validated);

        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Organization Unit Type added successfully",
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
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::findOrFail($id);
        $this->authorize('update', $organizationUnitType);

        $validated = $this->organizationUnitTypeService->validator($request, $id)->validate();
        $data = $this->organizationUnitTypeService->update($organizationUnitType, $validated);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization Unit Type updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::findOrFail($id);
        $this->authorize('delete', $organizationUnitType);

        $this->organizationUnitTypeService->destroy($organizationUnitType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization Unit Type deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getHierarchy(int $id): JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::find($id);
        $data = optional($organizationUnitType->getHierarchy())->toArray();
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "OrganizationUnitType  based hierarchy got successfully",
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
        $response = $this->organizationUnitTypeService->getAllTrashedOrganizationUnitType($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::onlyTrashed()->findOrFail($id);
        $this->organizationUnitTypeService->restore($organizationUnitType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "OrganizationUnitType restored successfully",
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
        $organizationUnitType = OrganizationUnitType::onlyTrashed()->findOrFail($id);
        $this->organizationUnitTypeService->forceDelete($organizationUnitType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "OrganizationUnitType permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
