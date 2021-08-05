<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Services\SkillService;
use Illuminate\Http\Request;

/**
 * Class SkillController
 * @package App\Http\Controllers
 */
class SkillController extends Controller
{
    /**
     * @var SkillService
     */
    public SkillService $skillService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * SkillController constructor.
     * @param SkillService $skillService
     */
    public function __construct(SkillService $skillService)
    {
        $this->skillService = $skillService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->skillService->getSkillList($request, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->skillService->getOneSkill($id, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->skillService->validator($request)->validate();
        try {
            $data = $this->skillService->store($validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Skill added successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
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
        $skill = Skill::findOrFail($id);
        $validated = $this->skillService->validator($request,$id)->validate();

        try {
            $data = $this->skillService->update($skill, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Skill updated successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $skill = Skill::findOrFail($id);

        try {
            $this->skillService->destroy($skill);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Skill deleted successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_OK);
    }
}
