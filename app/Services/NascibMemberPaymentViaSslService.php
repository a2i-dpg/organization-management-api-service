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
use App\Services\CommonServices\CodeGenerateService;
use App\Services\PaymentService\Library\SslCommerzNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class NascibMemberPaymentViaSslService
{
    /**
     * @throws Throwable
     */
    public function paymentInit(int $organizationId, string $applicationType, int $paymentGatewayType)
    {

        /**Here you have to receive all the order data to initiate the payment.
         * Lets your oder transaction information are saving in a table called "orders"
         * In orders table order uniq identity is "transaction_id","status" field contain status of the transaction,
         * "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.
         */
        $organization = Organization::findOrFail($organizationId);
        $industryAssociationOrganization = $organization->industryAssociations()->firstOrFail()->pivot;
        $memberShipTypeId = $industryAssociationOrganization->membership_type_id;
        $paymentStatus = $industryAssociationOrganization->payment_status;
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
        $postData['currency'] = "BDT";
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
        $postData['ship_name'] = "testnise81sk";
        $postData['ship_add1'] = "New Eskaton Road";
        $postData['ship_add2'] = "";
        $postData['ship_city'] = "Dhaka";
        $postData['ship_state'] = "Dhaka";
        $postData['ship_postcode'] = "1000";
        $postData['ship_phone'] = "01767111434";
        $postData['ship_country'] = "Bangladesh";

        $postData['shipping_method'] = PaymentTransactionHistory::SSL_COMMERZ_SHIPPING_METHOD_NO;
        $postData['num_of_item'] = 1;
        $postData['product_name'] = $organization->title . " Membership Registration Fee";
        $postData['product_category'] = NascibMember::APPLICATION_TYPE[$applicationType];
        $postData['product_profile'] = PaymentTransactionHistory::SSL_COMMERZ_PRODUCT_PROFILE_NON_PHYSICAL_GOODS;

        # OPTIONAL PARAMETERS
        $postData['value_a'] = "ref001";
        $postData['value_b'] = "ref002";
        $postData['value_c'] = "ref003";
        $postData['value_d'] = "ref004";

        $paymentConfig = $this->getPaymentConfig($industryAssociation->id, $paymentGatewayType);

        throw_if(empty($paymentConfig), new \Exception("The payment configuration is invalid"));

        $sslc = new SslCommerzNotification($paymentConfig);

        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        return $sslc->makePayment($postData, 'checkout', 'json');
    }

    private function getPaymentConfig(int $id, int $paymentGateWayType)
    {
        $industryAssociationConfig = IndustryAssociationConfig::where('industry_association_id', $id)
            ->where("row_status", BaseModel::ROW_STATUS_ACTIVE)
            ->firstOrFail();
        $paymentGate = $industryAssociationConfig->payment_gateways;
        $configKeyType = env('IS_SANDBOX', false) ? 'sandbox' : 'production';
        return $paymentGate[$paymentGateWayType][$configKeyType] ?? [];
    }

    public function paymentInitValidate(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'payment_gateway_type.in' => 'Payment gateway type must be within ' . implode(array_values(PaymentTransactionHistory::PAYMENT_GATEWAYS)) . '. [30000]'
        ];
        $rules = [
            "payment_gateway_type" => [
                "required",
                "integer",
                Rule::in(array_values(PaymentTransactionHistory::PAYMENT_GATEWAYS))
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }
}
