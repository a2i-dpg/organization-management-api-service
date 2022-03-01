<?php

namespace App\Http\Controllers;

use App\Services\CommonServices\CodeGenerateService;
use App\Services\NascibMemberPaymentViaSslService;
use App\Services\PaymentService\Library\SslCommerzNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class NascibMemberPaymentController extends Controller
{
    public NascibMemberPaymentViaSslService $nascibMemberPaymentViaSslService;
    public Carbon $startTime;

    public function __construct(NascibMemberPaymentViaSslService $nascibMemberPaymentViaSslService)
    {
        $this->nascibMemberPaymentViaSslService = $nascibMemberPaymentViaSslService;
        $this->startTime = Carbon::now();
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function payViaSsl(Request $request): JsonResponse
    {
        $validateData = $this->nascibMemberPaymentViaSslService->paymentInitValidate($request)->validate();

        $isTokenValid = CodeGenerateService::verifyJwt($validateData['member_identity_key']);
        throw_if(!$isTokenValid, new \Exception("Customer identification key is invalid"));

        $payloadClaim = CodeGenerateService::jwtPayloadClaims($validateData['member_identity_key']);

        throw_if((empty($payloadClaim['purpose']) && empty($payloadClaim['purpose_related_id'])), new \Exception("Customer identification key is invalid"));

        $responseData = $this->nascibMemberPaymentViaSslService->paymentInit($validateData, $payloadClaim['purpose_related_id'], $payloadClaim['purpose'], $validateData['payment_gateway_type']);

        $httpStatusCode = $responseData['status'] == 'success' ? ResponseAlias::HTTP_CREATED : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;

        if (!empty($responseData['gateway_page_url'])) {
            $response['gateway_page_url'] = $responseData['gateway_page_url'];
        }
        $response['_response_status'] = [
            "success" => $responseData['status'] === 'success',
            "code" => $httpStatusCode,
            "message" => $responseData['message'],
            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
        ];
        return Response::json($response, $httpStatusCode);

    }

    public function success(Request $request)
    {
        $this->nascibMemberPaymentViaSslService->successPayment($request);
    }

    public
    function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');
        echo "Transaction is already Successful in " . $tran_id;

    }

    public
    function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');
        echo "Transaction is already Successful" . $tran_id;
    }

    public
    function ipn(Request $request)
    {

        Log::debug("IPN", $request->all());
        $tran_id = $request->input('tran_id');
        Log::info("ipn-hit" . $tran_id);
    }


}
