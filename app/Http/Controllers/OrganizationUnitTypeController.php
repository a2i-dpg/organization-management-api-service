<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
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
     */
    public function getList(Request $request)
    {
        try {
            $response = $this->organizationUnitTypeService->getAllOrganizationUnitType($request, $this->startTime);
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
    public function read(int $id):JsonResponse
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
    function store(Request $request):JsonResponse
    {
        $validated = $this->organizationUnitTypeService->validator($request)->validate();
        try {
            $data = $this->organizationUnitTypeService->store($validated);

            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Organization Unit Type added successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id):JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::findOrFail($id);

        $validated = $this->organizationUnitTypeService->validator($request, $id)->validate();
        try {
            $data = $this->organizationUnitTypeService->update($organizationUnitType, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Organization Unit Type updated successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * remove the specified resource from storage
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id):JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::findOrFail($id);

        try {
            $this->organizationUnitTypeService->destroy($organizationUnitType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Organization Unit Type deleted successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_OK);
    }


    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function getHierarchy(int $id):JsonResponse
    {
        $organizationUnitType = OrganizationUnitType::find($id);
        try {
            $response = optional($organizationUnitType->getHierarchy())->toArray();
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }
}
