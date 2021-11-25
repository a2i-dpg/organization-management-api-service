<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Services\PublicationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class PublicationController extends Controller
{
    /**
     * @var PublicationService
     */
    public PublicationService $publicationService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * PublicationController constructor.
     * @param PublicationService $publicationService
     */
    public function __construct(PublicationService $publicationService)
    {
        $this->startTime = Carbon::now();
        $this->publicationService = $publicationService ;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function getList(Request $request)
    {
        $filter = $this->publicationService->filterValidator($request)->validate();
        $response = $this->publicationService->getPublicationList($filter,$this->startTime);
        return \Illuminate\Support\Facades\Response::json($response,ResponseAlias::HTTP_OK);

    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $publication = $this->publicationService->getOnePublication($id, $this->startTime);
        return \Illuminate\Support\Facades\Response::json($publication,ResponseAlias::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->publicationService->validator($request)->validate();
        $data = $this->publicationService->store($validated);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "publication added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return \Illuminate\Support\Facades\Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $Publication = Publication::findOrFail($id);
        $validated = $this->publicationService->validator($request, $id)->validate();

        $data = $this->publicationService->update($Publication, $validated);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Publication updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return \Illuminate\Support\Facades\Response::json($response, ResponseAlias::HTTP_CREATED);
    }



    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $publication = Publication::findOrFail($id);

        $this->publicationService->destroy($publication);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "publication deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return \Illuminate\Support\Facades\Response::json($response, ResponseAlias::HTTP_OK);
    }





    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $publication = Publication::onlyTrashed()->findOrFail($id);
        $this->publicationService->restore($publication);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "publication restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return \Illuminate\Support\Facades\Response::json($response, ResponseAlias::HTTP_OK);
    }



    /**
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $Publication = Publication::onlyTrashed()->findOrFail($id);
        $this->publicationService->forceDelete($Publication);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Publication permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return \Illuminate\Support\Facades\Response::json($response, ResponseAlias::HTTP_OK);
    }

}
