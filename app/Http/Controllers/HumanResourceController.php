<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\HumanResource;
use App\Services\HumanResourceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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
     *
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->humanResourceService->getHumanResourceList($request);
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

        return Response::json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HumanResource  $humanResource
     * @return \Illuminate\Http\Response
     */
    public function show(HumanResource $humanResource)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HumanResource  $humanResource
     * @return \Illuminate\Http\Response
     */
    public function edit(HumanResource $humanResource)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HumanResource  $humanResource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HumanResource $humanResource)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HumanResource  $humanResource
     * @return \Illuminate\Http\Response
     */
    public function destroy(HumanResource $humanResource)
    {
        //
    }
}
