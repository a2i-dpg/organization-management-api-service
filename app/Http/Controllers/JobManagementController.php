<?php

namespace App\Http\Controllers;


use App\Models\CompanyInfoVisibility;
use App\Models\AdditionalJobInformation;
use App\Models\PrimaryJobInformation;
use App\Services\JobManagementServices\AdditionalJobInformationService;
use App\Services\JobManagementServices\CompanyInfoVisibilityService;
use App\Services\JobManagementServices\JobContactInformationService;
use App\Services\JobManagementServices\PrimaryJobInformationService;
use App\Services\JobManagementServices\CandidateRequirementsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use function Symfony\Component\Translation\t;


class JobManagementController extends Controller
{

}
