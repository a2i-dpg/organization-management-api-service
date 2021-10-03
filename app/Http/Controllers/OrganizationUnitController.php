<?php

namespace App\Http\Controllers;

use App\Services\OrganizationUnitService;
use App\Models\OrganizationUnit;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class OrganizationUnitController
 * @package App\Http\Controllers
 */
class OrganizationUnitController extends Controller
{
    /**
     * @var OrganizationUnitService
     */
    protected OrganizationUnitService $organizationUnitService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * OrganizationController constructor.
     * @param OrganizationUnitService $organizationUnitService
     */
    public function __construct(OrganizationUnitService $organizationUnitService)
    {
        $this->organizationUnitService = $organizationUnitService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException|AuthorizationException
     */
    public function getList(Request $request)
    {
        $this->authorize('viewAny',OrganizationUnit::class);
        $filter = $this->organizationUnitService->filterValidator($request)->validate();
        try {
            $response = $this->organizationUnitService->getAllOrganizationUnit($filter, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * * Display a listing  of  the resources
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->organizationUnitService->getOneOrganizationUnit($id, $this->startTime);
            if (!$response) {
                abort(ResponseAlias::HTTP_NOT_FOUND);
            }
            $this->authorize('view', $response);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', OrganizationUnit::class);

        $validated = $this->organizationUnitService->validator($request)->validate();
        try {
            $data = $this->organizationUnitService->store($validated);
            $response = [
                'data' => $data ? $data : null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Organization Unit added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update a specified resource to storage
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $organizationUnit = OrganizationUnit::findOrFail($id);
        $this->authorize('update', OrganizationUnit::class);

        $validated = $this->organizationUnitService->validator($request, $id)->validate();
        try {
            $data = $this->organizationUnitService->update($organizationUnit, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization Unit updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Delete the specified resource from the storage
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $organizationUnit = OrganizationUnit::findOrFail($id);
        $this->authorize('delete', $organizationUnit);

        try {
            $this->organizationUnitService->destroy($organizationUnit);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization Unit delete successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getTrashedData(Request $request)
    {
        try {
            $response = $this->organizationUnitService->getAllTrashedOrganizationUnit($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function restore(int $id)
    {
        $organizationUnit = OrganizationUnit::onlyTrashed()->findOrFail($id);
        try {
            $this->organizationUnitService->restore($organizationUnit);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization Unit restored successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function forceDelete(int $id)
    {
        $organizationUnit = OrganizationUnit::onlyTrashed()->findOrFail($id);
        try {
            $this->organizationUnitService->forceDelete($organizationUnit);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization Unit permanently deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function assignServiceToOrganizationUnit(Request $request, int $id)
    {
        $organizationUnit = OrganizationUnit::findOrFail($id);

        $validated = $this->organizationUnitService->serviceValidator($request)->validated();

        try {
            $organizationUnit = $this->organizationUnitService->assignService($organizationUnit, $validated['serviceIds']);
            $response = [
                'data' => $organizationUnit->services()->get(),
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Services added to OrganizationUnit successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function getHierarchy(int $id): JsonResponse
    {
        $organizationUnit = OrganizationUnit::find($id);

        try {
            $data = optional($organizationUnit->getHierarchy())->toArray();
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization Unit based hierarchy got successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
