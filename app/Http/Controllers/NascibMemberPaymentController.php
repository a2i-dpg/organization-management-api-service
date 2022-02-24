<?php

namespace App\Http\Controllers;

use App\Services\NascibMemberPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NascibMemberPaymentController extends Controller
{
    public NascibMemberPaymentService $nascibMemberPaymentService;
    public Carbon $startDate;

    /**
     * @param NascibMemberPaymentService $nascibMemberPaymentService
     */
    public function __construct(NascibMemberPaymentService $nascibMemberPaymentService)
    {
        $this->nascibMemberPaymentService = $nascibMemberPaymentService;
        $this->startDate = Carbon::now();
    }


}
