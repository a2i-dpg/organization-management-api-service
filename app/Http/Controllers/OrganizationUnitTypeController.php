<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
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
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function getList(Request $request)
    {
        $filter = $this->organizationUnitTypeService->filterValidator($request)->validate();
        try {
            $response = $this->organizationUnitTypeService->getAllOrganizationUnitType($filter, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * * Display a specified resource
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->organizationUnitTypeService->getOneOrganizationUnitType($id, $this->startTime);
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
    function store(Request $request): JsonResponse
    {
        $validated = $this->organizationUnitTypeService->validator($request)->validate();
        try {
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
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::findOrFail($id);

        $validated = $this->organizationUnitTypeService->validator($request, $id)->validate();
        try {
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
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * remove the specified resource from storage
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::findOrFail($id);

        try {
            $this->organizationUnitTypeService->destroy($organizationUnitType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization Unit Type deleted successfully",
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
    public function getHierarchy(int $id)
    {
        $organizationUnitType = OrganizationUnitType::find($id);
        try {
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
            $response = $this->organizationUnitTypeService->getAllTrashedOrganizationUnitType($request, $this->startTime);
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
        $organizationUnitType = OrganizationUnitType::onlyTrashed()->findOrFail($id);
        try {
            $this->organizationUnitTypeService->restore( $organizationUnitType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "OrganizationUnitType restored successfully",
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
        $organizationUnitType = OrganizationUnitType::onlyTrashed()->findOrFail($id);
        try {
            $this->organizationUnitTypeService->forceDelete($organizationUnitType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "OrganizationUnitType permanently deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
