<?php

namespace App\Http\Controllers;

use App\Models\HumanResource;
use App\Services\HumanResourceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class HumanResourceController extends Controller
{

    /**
     * @var HumanResourceService
     */
    public HumanResourceService $humanResourceService;
    private Carbon $startTime;

    /**
     * HumanResourceController constructor.
     * @param HumanResourceService $humanResourceService
     */
    public function __construct(HumanResourceService $humanResourceService)
    {
        $this->humanResourceService = $humanResourceService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request):JsonResponse
    {
        try {
            $response = $this->humanResourceService->getHumanResourceList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }

        return Response::json($response);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id):JsonResponse
    {
        try {
            $response = $this->humanResourceService->getOneHumanResource($id, $this->startTime);
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
    public function store(Request $request):JsonResponse
    {
        $validatedData = $this->humanResourceService->validator($request)->validate();
        try {
            $data = $this->humanResourceService->store($validatedData);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Human Resource added successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
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
    public function update(Request $request, int $id):JsonResponse
    {
        $humanResource = HumanResource::findOrFail($id);

        $validated = $this->humanResourceService->validator($request, $id)->validate();
        try {
            $data = $this->humanResourceService->update($humanResource, $validated);

            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Human Resource updated successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];

        } catch (Throwable $e) {
            return $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id):JsonResponse
    {
        $humanResource = HumanResource::findOrFail($id);

        try {
            $this->humanResourceService->destroy($humanResource);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Human Resource deleted successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
