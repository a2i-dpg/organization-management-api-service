<?php

namespace App\Http\Controllers;

use App\Services\FourIRServices\FourIRCertificateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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
        $responseData = $this->fourIRCertificateService->getCertificateList($request->all(),$fourIrInitiativeId);
        $response = [
            "data" => $responseData,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, $response['_response_status']['code']);
    }
}
