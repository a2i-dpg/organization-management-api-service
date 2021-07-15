<?php

namespace App\Http\Controllers;

use App\Services\OrganizationService;
use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\Organization;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;
/**
 * Class OrganizationController
 * @package App\Http\Controllers
 */
class OrganizationController extends Controller
{
    /**
     * @var OrganizationService
     */
    protected OrganizationService $organizationService;

    /**
     * OrganizationController constructor.
     * @param OrganizationService $organizationService
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     *
     */
    public function getList(Request $request)
    {
        $response = $this->organizationService->OrganizationsList($request);
        return response()->json($response);

    }

    public function read($id)
    {
        $response = $this->organizationService->singleOrganization($id);
        return response()->json($response);
    }

    public function store(Request $request)
    {

    }

    public function update($id)
    {

    }

    public function destroy($id)
    {

    }


}
