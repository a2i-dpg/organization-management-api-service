<?php

namespace App\Http\Controllers;

use App\Models\HrDemand;
use App\Services\HrDemandService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class HrDemandController extends Controller
{
    public HrDemandService $hrDemandService;
    private Carbon $startTime;

    /**
     * HrDemandController constructor.
     * @param HrDemandService $hrDemandService
     */
    public function __construct(HrDemandService $hrDemandService)
    {
        $this->hrDemandService = $hrDemandService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getList()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function read()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', HrDemand::class);
        $validatedData = $this->hrDemandService->validator($request)->validate();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HrDemand  $hrDemand
     * @return Response
     */
    public function update(HrDemand $hrDemand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id) : JsonResponse
    {
        $hrDemand = HrDemand::findOrFail($id);

        //$this->authorize('delete', $course);

        $this->hrDemandService->destroy($hrDemand);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "HR Demand Deleted Successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
