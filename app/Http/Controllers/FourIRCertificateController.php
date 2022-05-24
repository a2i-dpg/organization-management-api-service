<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Services\FourIRServices\FourIRCertificateService;
use App\Services\FourIRServices\FourIrEmploymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use stdClass;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class FourIRCertificateController extends Controller
{
    public FourIRCertificateService $fourIRCertificateService;
    public Carbon $startTime;

    /**
     * @param FourIRCertificateService $fourIRCertificateService
     */
    public function __construct(FourIRCertificateService $fourIRCertificateService)
    {
        $this->fourIRCertificateService = $fourIRCertificateService;
        $this->startTime = Carbon::now();
    }

    public function getCertificates(Request $request, int $fourIrInitiativeId): \Illuminate\Http\JsonResponse
    {
        $certificates = $this->fourIRCertificateService->getCertificateList($request->all(), $fourIrInitiativeId);
        $this->authorize('viewSingleInitiativeStep', FourIRInitiative::class);
        $youthIds = array_column($certificates, 'youth_id') ?? [];

        $employments = app(FourIrEmploymentService::class)->getEmploymentByYouthIds($youthIds, $fourIrInitiativeId, $this->startTime) ?? [];


        foreach ($certificates as &$certifications) {
            if (!empty($employments[$certifications['youth_id']])) {
                $certifications['employment_status'] = $employments[$certifications['youth_id']]['employment_status'];
                if (in_array($employments[$certifications['youth_id']]['employment_status'],[1,3])) {
                    $certifications['employment_info'] = new stdClass();
                } else {
                    $certifications['employment_info'] = $employments[$certifications['youth_id']];
                }
            } else {
                $certifications['employment_status'] = 1;
                $certifications['employment_info'] = new stdClass();
            }
        }

        $response = [
            "data" => $certificates,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, $response['_response_status']['code']);
    }


}
