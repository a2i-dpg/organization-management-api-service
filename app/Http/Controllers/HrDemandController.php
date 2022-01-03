<?php

namespace App\Http\Controllers;

use App\Models\HrDemand;
use Illuminate\Http\Request;

class HrDemandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read()
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
        $this->authorize('create', HrDemand::class);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HrDemand  $hrDemand
     * @return \Illuminate\Http\Response
     */
    public function update(HrDemand $hrDemand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HrDemand  $hrDemand
     * @return \Illuminate\Http\Response
     */
    public function destroy(HrDemand $hrDemand)
    {
        //
    }
}
