<?php

namespace App\Http\Controllers;

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
        $certificates = $this->fourIRCertificateService->getCertificateList($request->all(),$fourIrInitiativeId);

        $youthIds = array_column($certificates, 'youth_id') ?? [];

        $employments= app(FourIrEmploymentService::class)->getEmploymentByYouthIds($youthIds,$fourIrInitiativeId ,$this->startTime) ?? [];

        $employed = array_filter($employments, function ($employment) {
            return ($employment['employment_status'] == 2);
        }) ?? [];
        $notApplicable= array_filter($employments, function ($employment) {
            return ($employment['employment_status'] == 3);
        }) ?? [];

        Log::info("Certificate ".json_encode(
                $certificates
            ,JSON_PRETTY_PRINT));

        foreach ($certificates as &$certifications){
            if(in_array($certifications['youth_id'],array_column($employed, 'user_id'))){
                $certifications['employment_status']=2;
                $certifications['employment_info']=$employments[array_search($certifications['youth_id'], array_column($employments, 'user_id'))] ?? new stdClass();

            }else if(in_array($certifications['youth_id'],array_column($notApplicable, 'user_id'))){
                $certifications['employment_status']=3;
                $certifications['employment_info']=$employments[array_search($certifications['youth_id'], array_column($employments, 'user_id'))] ?? new stdClass();
            }else{
                $certifications['employment_status']=1;
                $certifications['employment_info']=$employments[array_search($certifications['youth_id'], array_column($employments, 'user_id'))] ?? new stdClass();
            }
        }

        Log::info("Certificate ".json_encode([
                $certificates,
                $youthIds,
                $employments,
                $employed,
                $notApplicable
            ],JSON_PRETTY_PRINT));

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
