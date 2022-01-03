<?php

namespace App\Http\Controllers;

use App\Models\HrDemand;
use App\Services\HrDemandService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

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
     * @param  \App\Models\HrDemand  $hrDemand
     * @return Response
     */
    public function destroy(HrDemand $hrDemand)
    {
        //
    }
}
