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

class NascibMemberPaymentViaSslService
{
    /**
     * @throws \Throwable
     */
    public function paymentInit($data, int $applicationType, int $paymentGateWayType)
    {

        /**Here you have to receive all the order data to initiate the payment.
         * Lets your oder transaction information are saving in a table called "orders"
         * In orders table order uniq identity is "transaction_id","status" field contain status of the transaction,
         * "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.
         */
        $organization = Organization::findOrFail(1);
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


        # SHIPMENT INFORMATION
        $postData['shipping_method'] = PaymentTransactionHistory::SSL_COMMERZ_SHIPPING_METHOD_NO;
        $postData['num_of_item'] = 1;
        $postData['product_name'] = $organization->title . " Membership Registration Fee";
        $postData['product_category'] = NascibMember::APPLICATION_TYPE[$applicationType];
        $postData['product_profile'] = PaymentTransactionHistory::SSL_COMMERZ_PRODUCT_PROFILE_NON_PHYSICAL_GOODS;

        $paymentConfig = $this->getPaymentConfig($industryAssociation->id, $paymentGateWayType);

        $sslc = new SslCommerzNotification($paymentConfig);
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        return $sslc->makePayment($postData, 'checkout', 'json');
    }

    public function success(Request $request)
    {
        echo "Transaction is Successful";

        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();

        #Check order status in order tabel against the transaction id or order id.
        $order_detials = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_detials->status == 'Pending') {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

            if ($validation == TRUE) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
                in order table as Processing or Complete.
                Here you can also sent sms or email for successfull transaction to customer
                */
                $update_product = DB::table('orders')
                    ->where('transaction_id', $tran_id)
                    ->update(['status' => 'Processing']);

                echo "<br >Transaction is successfully Completed";
            } else {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel and Transation validation failed.
                Here you need to update order status as Failed in order table.
                */
                $update_product = DB::table('orders')
                    ->where('transaction_id', $tran_id)
                    ->update(['status' => 'Failed']);
                echo "validation Fail";
            }
        } else if ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that transaction is completed. No need to udate database.
             */
            echo "Transaction is successfully Completed";
        } else {
            #That means something wrong happened. You can redirect customer to your product page.
            echo "Invalid Transaction";
        }


    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_detials->status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Failed']);
            echo "Transaction is Falied";
        } else if ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
            echo "Transaction is already Successful";
        } else {
            echo "Transaction is Invalid";
        }

    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_detials->status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Canceled']);
            echo "Transaction is Cancel";
        } else if ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
            echo "Transaction is already Successful";
        } else {
            echo "Transaction is Invalid";
        }


    }

    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {

            $tran_id = $request->input('tran_id');

            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->select('transaction_id', 'status', 'currency', 'amount')->first();

            if ($order_details->status == 'Pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($request->all(), $tran_id, $order_details->amount, $order_details->currency);
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_id', $tran_id)
                        ->update(['status' => 'Processing']);

                    echo "Transaction is successfully Completed";
                } else {
                    /*
                    That means IPN worked, but Transation validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_id', $tran_id)
                        ->update(['status' => 'Failed']);

                    echo "validation Fail";
                }

            } else if ($order_details->status == 'Processing' || $order_details->status == 'Complete') {

                #That means Order status already updated. No need to udate database.

                echo "Transaction is already successfully Completed";
            } else {
                #That means something wrong happened. You can redirect customer to your product page.

                echo "Invalid Transaction";
            }
        } else {
            echo "Invalid Data";
        }
    }

    private function getPaymentConfig(int $id, int $paymentGateWayType)
    {
        $industryAssociationConfig=IndustryAssociationConfig::where('industry_association_id');
    }
}
