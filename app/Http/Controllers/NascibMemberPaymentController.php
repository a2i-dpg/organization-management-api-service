<?php

namespace App\Http\Controllers;

use App\Models\MembershipType;
use App\Models\NascibMember;
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
        DB::beginTransaction();
        $httpStatusCode = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
        try {
            $isTokenValid = CodeGenerateService::verifyJwt($validateData['member_identity_key']);

            if (!$isTokenValid) {
                $response['_response_status'] = [
                    "success" => false,
                    "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                    "message" => "Member Identification number is invalid",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ];
            }

            $payloadClaim = CodeGenerateService::jwtPayloadClaims($validateData['member_identity_key']);

            if (!empty($payloadClaim['purpose']) && !empty($payloadClaim['purpose_related_id']) && in_array($payloadClaim['purpose'], array_keys(NascibMember::APPLICATION_TYPE))) {
                $responseData = $this->nascibMemberPaymentViaSslService->paymentInit($validateData, $payloadClaim['purpose_related_id'], $payloadClaim['purpose'], $validateData['payment_gateway_type']);

                $httpStatusCode = $responseData['status'] == 'success' ? ResponseAlias::HTTP_CREATED : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;

                if (!empty($responseData['gateway_page_url'])) {
                    $response['gateway_page_url'] = $responseData['gateway_page_url'];
                }

                $response['_response_status'] = [
                    "success" => $responseData['status'] == 'success',
                    "code" => $httpStatusCode,
                    "message" => $responseData['message'],
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ];
            } else {
                $response['_response_status'] = [
                    "success" => false,
                    "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                    "message" => "Member Identification number is invalid",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ];
            }

            DB::commit();

        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, $httpStatusCode);

    }

    /**
     * @throws Throwable
     */
    public function success(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            [$status, $message] = $this->nascibMemberPaymentViaSslService->successPayment($request);
            $httpStatusCode = $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
            $response['_response_status'] = [
                "success" => (bool)$status,
                "code" => $httpStatusCode,
                "message" => $message,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, $httpStatusCode);
    }

    /**
     * @throws Throwable
     */
    public function ipn(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            [$status, $message] = $this->nascibMemberPaymentViaSslService->successPayment($request);
            $httpStatusCode = $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
            $response['_response_status'] = [
                "success" => (bool)$status,
                "code" => $httpStatusCode,
                "message" => $message,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, $httpStatusCode);
    }

    /**
     * @throws Throwable
     */
    public function fail(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $status = $this->nascibMemberPaymentViaSslService->failPayment($request);
            $httpStatusCode = $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
            $response['_response_status'] = [
                "success" => $status,
                "code" => $httpStatusCode,
                "message" => $status ? "Your Payment is Failed" : "Unprocessable Request",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, $httpStatusCode);

    }

    public function cancel(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $status = $this->nascibMemberPaymentViaSslService->cancelPayment($request);
            $httpStatusCode = $status ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
            $response['_response_status'] = [
                "success" => $status,
                "code" => $httpStatusCode,
                "message" => $status ? "Your Payment is Canceled" : "Unprocessable Request",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, $httpStatusCode);
    }

}
