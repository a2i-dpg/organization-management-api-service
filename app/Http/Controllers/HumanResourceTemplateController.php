<?php

namespace App\Http\Controllers;

use App\Models\HumanResourceTemplate;
use App\Services\HumanResourceTemplateService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', HumanResourceTemplate::class);

        $filter = $this->humanResourceTemplateService->filterValidator($request)->validate();
        $response = $this->humanResourceTemplateService->getHumanResourceTemplateList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $humanResourceTemplate = $this->humanResourceTemplateService->getOneHumanResourceTemplate($id);
        $this->authorize('view', $humanResourceTemplate);
        $response = [
            "data" => $humanResourceTemplate,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('create', HumanResourceTemplate::class);

        $validatedData = $this->humanResourceTemplateService->validator($request)->validate();
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
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $humanResourceTemplate = HumanResourceTemplate::findOrFail($id);

        $this->authorize('update', $humanResourceTemplate);

        $validated = $this->humanResourceTemplateService->validator($request, $id)->validate();
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
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $humanResourceTemplate = HumanResourceTemplate::findOrFail($id);
        $this->authorize('delete', $humanResourceTemplate);
        $this->humanResourceTemplateService->destroy($humanResourceTemplate);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "HumanResource Template delete successfully.",
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
        $response = $this->humanResourceTemplateService->getTrashedHumanResourceTemplateList($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $humanResourceTemplate = HumanResourceTemplate::onlyTrashed()->findOrFail($id);
        $this->humanResourceTemplateService->restore($humanResourceTemplate);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "HumanResource Template restored successfully",
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
        $humanResourceTemplate = HumanResourceTemplate::onlyTrashed()->findOrFail($id);
        $this->humanResourceTemplateService->forceDelete($humanResourceTemplate);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "HumanResource Template permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
