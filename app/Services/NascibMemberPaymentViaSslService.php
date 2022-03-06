<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\IndustryAssociationConfig;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use App\Models\MembershipType;
use App\Models\NascibMember;
use App\Models\Organization;
use App\Models\PaymentTransactionHistory;
use App\Models\PaymentTransactionLog;
use App\Services\CommonServices\CodeGenerateService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use App\Services\PaymentService\Library\SslCommerzNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class NascibMemberPaymentViaSslService
{
    /**
     * @throws Throwable
     */

    public function paymentInit(array $requestData, int $industryAssociationOrganizationId, string $applicationType, int $paymentGatewayType)
    {

        /**Here you have to receive all the order data to initiate the payment.
         * Lets your oder transaction information are saving in a table called "orders"
         * In orders table order uniq identity is "transaction_id","status" field contain status of the transaction,
         * "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.
         */

        $industryAssociationOrganization = DB::table('industry_association_organization')->where('id', $industryAssociationOrganizationId)->first();

        throw_if(empty($industryAssociationOrganization), new Exception("Row is not found in industry_association_organization table"));

        $organization = Organization::findOrFail($industryAssociationOrganization->organization_id);

        $memberShipTypeId = $industryAssociationOrganization->membership_type_id;

        $paymentStatus = $industryAssociationOrganization->payment_status;

        if ($paymentStatus == BaseModel::ROW_STATUS_REJECTED) {
            return [
                "status" => "fail",
                "message" => "You are Rejected"
            ];
        }

        if ($paymentStatus == PaymentTransactionHistory::PAYMENT_SUCCESS && $applicationType == NascibMember::APPLICATION_TYPE_NEW) {
            return [
                "status" => "fail",
                "message" => "You have already completed your payment"
            ];
        }

        $memberShipType = MembershipType::findOrFail($memberShipTypeId);
        $applicationFee = $applicationType == NascibMember::APPLICATION_TYPE_RENEW ? $memberShipType->renewal_fee : $memberShipType->fee;
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationOrganization->industry_association_id);
        $invoicePrefix = $industryAssociation->code;

        $locDivision = LocDivision::find($organization->loc_division_id);
        $locDistrict = LocDistrict::find($organization->loc_district_id);
        $locUpazila = LocUpazila::find($organization->loc_district_id);
        $districtTitle = $locDistrict->title ?? "";
        $upazilaTitle = $locUpazila->title ?? "";
        $customerCity = $upazilaTitle . "," . $districtTitle;

        $postData = array();
        $postData['total_amount'] = $applicationFee; // You can't  pay less than 10
        $postData['currency'] = PaymentTransactionHistory::CURRENCY_BDT ?? "BDT";
        /** tran_id must be unique */
        $postData['tran_id'] = CodeGenerateService::getNewInvoiceCode($invoicePrefix, PaymentTransactionHistory::SSL_COMMERZ_INVOICE_SIZE);

        # CUSTOMER INFORMATION
        $postData['cus_name'] = $organization->contact_person_name;
        $postData['cus_email'] = $organization->contact_person_email;
        $postData['cus_phone'] = $organization->contact_person_mobile;
        $postData['cus_fax'] = $organization->fax_no ?? "";
        $postData['cus_country'] = "Bangladesh";
        $postData['cus_state'] = $locDivision->title ?? "";
        $postData['cus_city'] = $customerCity ?? "";
        $postData['cus_postcode'] = "";
        $postData['cus_add1'] = $organization->address;
        $postData['cus_add2'] = "";

        # SHIPMENT INFORMATION
        $postData['ship_name'] = $industryAssociation->title;
        $postData['ship_add1'] = $industryAssociation->address;
        $postData['ship_add2'] = "";
        $postData['ship_city'] = LocDistrict::find($industryAssociation->loc_district_id)->title ?? "";
        $postData['ship_state'] = LocDivision::find($industryAssociation->loc_division_id)->title ?? "";
        $postData['ship_postcode'] = "";
        $postData['ship_phone'] = $industryAssociation->mobile;
        $postData['ship_country'] = $industryAssociation->country;

        $postData['shipping_method'] = PaymentTransactionHistory::SSL_COMMERZ_SHIPPING_METHOD_NO;
        $postData['num_of_item'] = 1;
        $postData['product_name'] = $organization->title . " Membership Registration Fee";
        $postData['product_category'] = NascibMember::APPLICATION_TYPE[$applicationType];
        $postData['product_profile'] = PaymentTransactionHistory::SSL_COMMERZ_PRODUCT_PROFILE_NON_PHYSICAL_GOODS;

        # OPTIONAL PARAMETERS
        $postData['value_a'] = "";
        $postData['value_b'] = "";
        $postData['value_c'] = "";
        $postData['value_d'] = "";

        $paymentConfig = $this->getPaymentConfig($industryAssociation->id, $paymentGatewayType);
        Log::channel('ssl_commerz')->info("ssl-config: " . json_encode($paymentConfig, JSON_PRETTY_PRINT));
        throw_if(empty($paymentConfig), new \Exception("The payment configuration is invalid"));
        $paymentConfig = array_merge($paymentConfig, $requestData['feed_uri']);

        $sslc = new SslCommerzNotification($paymentConfig);

        /** initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payment gateway here )*/
        $sslPayment = $sslc->makePayment($postData);

        Log::channel('ssl_commerz')->info("ssl-payment: " . json_encode($sslPayment));

        /**
         * @params postData
         * @params industryAssociationOrganization id as payment purpose related id
         * @params applicationType is either New Application or Renew Application
         * @params paymentGatewayType is either SSL Commerz or Ekpay
         */
        $this->storePaymentLog($postData, $industryAssociationOrganization->id, $applicationType, $paymentGatewayType);

        return $sslc->formatResponse($sslPayment);
    }


    /**
     * @throws Throwable
     */
    public function successPayment(Request $request): array
    {
        Log::channel('ssl_commerz')->info("IPN RESPONSE: " . json_encode($request->all()));

        $tranId = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $status = 0;
        $message = "Invalid Transaction";
        if ($tranId) {
            $paymentLog = PaymentTransactionLog::where('mer_trnx_id', $tranId)
                ->where("amount", $amount)
                ->where("trnx_currency", $currency)
                ->first();

            Log::channel('ssl_commerz')->info("paymentLog: " . json_encode($paymentLog));

            throw_if(empty($paymentLog), new \Exception('Your Requested Payload invalid'));

            if ($paymentLog->status != PaymentTransactionHistory::PAYMENT_SUCCESS) {


                $industryAssociationOrganization = DB::table('industry_association_organization')
                    ->where('id', $paymentLog->payment_purpose_related_id)
                    ->first();

                Log::channel('ssl_commerz')->info("industryAssociationOrganization: " . json_encode($industryAssociationOrganization));

                throw_if(empty($industryAssociationOrganization), new \Exception('Invalid Transaction'));

                $config = $this->getPaymentConfig($industryAssociationOrganization->industry_association_id, $paymentLog->payment_gateway_type);

                $sslc = new SslCommerzNotification($config);

                $validation = $sslc->orderValidate($request->all(), $tranId, $amount, $currency);


                if ($validation == TRUE) {
                    $request->offsetSet('payment_status', BaseModel::PAYMENT_SUCCESS);
                    $this->completePaymentHistoryStore($paymentLog, $request->all());


                    app(NascibMemberService::class)->updateMembershipExpireDate(
                        $industryAssociationOrganization->industry_association_id,
                        $industryAssociationOrganization->organization_id,
                        $industryAssociationOrganization->membership_type_id
                    );

                    $message = "Transaction is successfully Completed";
                    $status = 1;
                }

            } else {
                $message = "Transaction is successfully Completed";
                $status = 1;
            }
        }
        return [
            $status,
            $message
        ];
    }

    public function ipnHandler(Request $request)
    {
        Log::channel('ssl_commerz')->info("IPN RESPONSE: " . json_encode($request->all()));

        $tranId = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $status = 0;
        $message = "Invalid Transaction";
        if ($tranId) {
            $paymentLog = PaymentTransactionLog::where('mer_trnx_id', $tranId)
                ->where("amount", $amount)
                ->where("trnx_currency", $currency)
                ->first();

            Log::channel('ssl_commerz')->info("paymentLog: " . json_encode($paymentLog));
            if (!empty($paymentLog)) {

                if ($paymentLog->status != PaymentTransactionHistory::PAYMENT_SUCCESS) {

                    $industryAssociationOrganization = DB::table('industry_association_organization')
                        ->where('id', $paymentLog->payment_purpose_related_id)
                        ->first();

                    Log::channel('ssl_commerz')->info("industryAssociationOrganization: " . json_encode($industryAssociationOrganization));

                    if (!empty($industryAssociationOrganization)) {
                        $config = $this->getPaymentConfig($industryAssociationOrganization->industry_association_id, $paymentLog->payment_gateway_type);

                        $sslc = new SslCommerzNotification($config);

                        $validation = $sslc->orderValidate($request->all(), $tranId, $amount, $currency);

                        if ($validation == TRUE) {
                            $request->offsetSet('payment_status', BaseModel::PAYMENT_SUCCESS);
                            $this->completePaymentHistoryStore($paymentLog, $request->all());

                            app(NascibMemberService::class)->updateMembershipExpireDate(
                                $industryAssociationOrganization->industry_association_id,
                                $industryAssociationOrganization->organization_id,
                                $industryAssociationOrganization->membership_type_id
                            );

                            $message = "Transaction is successfully Completed";
                            $status = 1;
                        } else {
                            $message = 'OrderValidate validation is false';
                        }
                    } else {
                        $message = 'The row of IndustryAssociationOrganization is empty';
                    }

                } else {
                    $message = "Transaction is successfully Completed";
                    $status = 1;
                }
            } else {
                $message = 'Your Requested Payload invalid, So The Model PaymentTransactionLog is empty';
            }
        }
        Log::debug("IPN-DEBUG-LOG: ", [
            "status" => $status,
            "message" => $message
        ]);
    }

    public function failPayment(Request $request): bool
    {
        $tranId = $request->input('tran_id');
        /** @var PaymentTransactionLog $paymentLog */
        $paymentLog = PaymentTransactionLog::findOrFail('mer_trnx_id', $tranId);
        $paymentLog->status = PaymentTransactionHistory::PAYMENT_FAIL;
        $paymentLog->response_message = $request->all();
        return $paymentLog->save();

    }

    public function cancelPayment(Request $request): bool
    {
        $tranId = $request->input('tran_id');
        /** @var PaymentTransactionLog $paymentLog */
        $paymentLog = PaymentTransactionLog::findOrFail('mer_trnx_id', $tranId);
        $paymentLog->status = PaymentTransactionHistory::PAYMENT_CANCEL;
        $paymentLog->response_message = $request->all();
        return $paymentLog->save();

    }

    /**
     * @throws Throwable
     */
    public function completePaymentHistoryStore(PaymentTransactionLog $paymentLog, array $responseData)
    {
        $paymentLog->response_message = $responseData;
        $paymentLog->trnx_id = $responseData['bank_tran_id'];
        $paymentLog->paid_amount = $responseData['amount'];
        $paymentLog->status = $responseData['payment_status'];
        $paymentLog->transaction_completed_at = Carbon::now();
        $paymentLog->save();

        if ($responseData['payment_status'] == PaymentTransactionHistory::PAYMENT_SUCCESS) {
            $paymentHistoryPayload = $paymentLog->toArray();
            $paymentHistoryPayload['customer_name'] = $paymentLog->request_payload['cus_name'];
            $paymentHistoryPayload['customer_email'] = $paymentLog->request_payload['cus_email'];;
            $paymentHistoryPayload['customer_mobile'] = $paymentLog->request_payload['cus_phone'];;
            $paymentHistoryPayload['status'] = $responseData['payment_status'];
            $paymentHistory = new PaymentTransactionHistory();
            $paymentHistory->fill($paymentHistoryPayload);
            $paymentHistory->save();
            $paymentLog->payment_transaction_history_id = $paymentHistory->id;
            $paymentLog->save();
            $this->confirmationMailAndSmsSend($paymentHistory);
        }

    }


    /**
     * @throws Throwable
     */
    public function confirmationMailAndSmsSend(PaymentTransactionHistory $paymentHistory)
    {
        if (!empty($paymentHistory)) {
            /** Mail send*/
            $to = array($paymentHistory->customer_email);
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "Nascib Membership Registration";
            $message = "Congratulation, You are successfully complete your payment. You are  an active member.";
            $messageBody = MailService::templateView($message);
            $mailService = new MailService($to, $from, $subject, $messageBody);
            $mailService->sendMail();

            /** Sms send */
            $recipient = $paymentHistory->customer_mobile;
            $smsMessage = "Congratulation,  You are successfully complete your payment";
            $smsService = new SmsService();
            $smsService->sendSms($recipient, $smsMessage);
        }

    }


    /**
     * @param array $payload
     * @param int $purposeRelatedId
     * @param string $paymentPurpose
     * @param $paymentGatewayType
     * @return void
     */
    public function storePaymentLog(array $payload, int $purposeRelatedId, string $paymentPurpose, $paymentGatewayType)
    {
        /** Invoice id is the invoice id */
        $data['invoice'] = $payload['tran_id'];
        $data['mer_trnx_id'] = $payload['tran_id'];
        $data['payment_purpose_related_id'] = $purposeRelatedId;
        $data['payment_purpose'] = $paymentPurpose;
        $data['payment_gateway_type'] = $paymentGatewayType;
        $data['trnx_currency'] = $payload['currency'];
        $data['amount'] = $payload['total_amount'];
        $data['request_payload'] = $payload;
        $data['transaction_created_at'] = Carbon::now();

        $paymentLog = new PaymentTransactionLog();
        $paymentLog->fill($data);
        $paymentLog->save();
    }


    /**
     * @param int $id
     * @param int $paymentGateWayType
     * @return array
     */
    private function getPaymentConfig(int $id, int $paymentGateWayType): array
    {
        $industryAssociationConfig = IndustryAssociationConfig::where('industry_association_id', $id)
            ->where("row_status", BaseModel::ROW_STATUS_ACTIVE)
            ->firstOrFail();
        $paymentGate = $industryAssociationConfig->payment_gateways;
        $configKeyType = env('IS_SANDBOX', false) ? 'sandbox' : 'production';
        return $paymentGate[$paymentGateWayType][$configKeyType] ?? [];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function paymentInitValidate(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'payment_gateway_type.in' => 'Payment gateway type must be within ' . implode(array_values(PaymentTransactionHistory::PAYMENT_GATEWAYS)) . '. [30000]'
        ];
        $rules = [
            'member_identity_key' => [
                "required",
                "string"
            ],
            "payment_gateway_type" => [
                "required",
                "integer",
                Rule::in(array_values(PaymentTransactionHistory::PAYMENT_GATEWAYS))
            ],
            "feed_uri.success_url" => [
                "required",
                "string"
            ],
            "feed_uri.failed_url" => [
                "required",
                "string"
            ],
            "feed_uri.cancel_url" => [
                "required",
                "string"
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

}
