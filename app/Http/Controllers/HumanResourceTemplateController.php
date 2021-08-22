<?php

namespace App\Http\Controllers;

use App\Models\HumanResourceTemplate;
use App\Services\HumanResourceTemplateService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


/**
 * Class HumanResourceTemplateController
 * @package App\Http\Controllers
 */
class HumanResourceTemplateController extends Controller
{
    /**
     * @var HumanResourceTemplateService
     */
    public HumanResourceTemplateService $humanResourceTemplateService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * HumanResourceTemplateController constructor.
     * @param HumanResourceTemplateService $humanResourceTemplateService
     */
    public function __construct(HumanResourceTemplateService $humanResourceTemplateService)
    {
        $this->humanResourceTemplateService = $humanResourceTemplateService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request):JsonResponse
    {
        try {
            $response = $this->humanResourceTemplateService->getHumanResourceTemplateList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id):JsonResponse
    {
        try {
            $response = $this->humanResourceTemplateService->getOneHumanResourceTemplate($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    function store(Request $request):JsonResponse
    {

        $validatedData = $this->humanResourceTemplateService->validator($request)->validate();
        try {
            $data = $this->humanResourceTemplateService->store($validatedData);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Human Resource Template added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id):JsonResponse
    {
        $humanResourceTemplate = HumanResourceTemplate::findOrFail($id);
        $validated = $this->humanResourceTemplateService->validator($request,$id)->validate();
        try {
            $data = $this->humanResourceTemplateService->update($humanResourceTemplate, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Human Resource Template updated successfully.",
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
        $humanResourceTemplate = HumanResourceTemplate::findOrFail($id);
        try {
            $this->humanResourceTemplateService->destroy($humanResourceTemplate);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "HumanResource Template delete successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
