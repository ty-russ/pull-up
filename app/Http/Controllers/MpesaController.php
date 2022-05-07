<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentsController;

class MpesaController extends Controller
{
  
    public function get_auth () {
     
         $CONSUMER_KEY = env('CONSUMER_KEY');
         $CONSUMER_SECRET = env('CONSUMER_SECRET');
         $Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
       //OAuth
       $curl_Tranfer = curl_init();
       curl_setopt($curl_Tranfer, CURLOPT_URL, $Token_URL);
       $credentials = base64_encode( $CONSUMER_KEY . ':' . $CONSUMER_SECRET);
       curl_setopt($curl_Tranfer, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
       curl_setopt($curl_Tranfer, CURLOPT_HEADER, false);
       curl_setopt($curl_Tranfer, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($curl_Tranfer, CURLOPT_SSL_VERIFYPEER, false);
       $curl_Tranfer_response = curl_exec($curl_Tranfer);
       
       $token = json_decode($curl_Tranfer_response)->access_token;
       // error_log($token);
        return  $token;
    }
   public  function mpesa_stk ($phoneNumber,$reservation,$regNumberPlate,$Amount) {
     
       
        // Initialize the variables
        error_log('====stk===');
       error_log($phoneNumber);
        $Business_Code = '174379';
        $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $Type_of_Transaction = 'CustomerPayBillOnline';
        $phone_number =$phoneNumber;
        $OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $total_amount ='1'; //$Amount;
        $CallBackURL = env('MPESA_TEST_URL').'/api/callback' ;
        $Time_Stamp = date("Ymdhis");
        $password = base64_encode($Business_Code . $Passkey . $Time_Stamp);


       //OAuth
       $token = $this->get_auth();
       //error_log($token);
         //stkpush
        $curl_Tranfer2 = curl_init();
            curl_setopt($curl_Tranfer2, CURLOPT_URL, $OnlinePayment);
            curl_setopt($curl_Tranfer2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));

            $curl_Tranfer2_post_data = [
                'BusinessShortCode' => $Business_Code,
                'Password' => $password,
                'Timestamp' =>$Time_Stamp,
                'TransactionType' =>$Type_of_Transaction,
                'Amount' => $total_amount,
                'PartyA' => $phone_number,
                'PartyB' => $Business_Code,
                'PhoneNumber' => $phone_number,
                'CallBackURL' => $CallBackURL,
                'AccountReference' => 'Registration_servicetest',
                'TransactionDesc' => 'Test lipa na mpesa',

            ];

            $data2_string = json_encode($curl_Tranfer2_post_data);
           // error_log($data2_string);
            curl_setopt($curl_Tranfer2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_Tranfer2, CURLOPT_POST, true);
            curl_setopt($curl_Tranfer2, CURLOPT_POSTFIELDS, $data2_string);
            curl_setopt($curl_Tranfer2, CURLOPT_HEADER, false);
            curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYHOST, 0);
            $curl_Tranfer2_response = json_decode(curl_exec($curl_Tranfer2));

            error_log( json_encode($curl_Tranfer2_response, JSON_PRETTY_PRINT));
            if(property_exists($curl_Tranfer2_response,'ResponseCode')) {
                if($curl_Tranfer2_response->ResponseCode =="0" ) {
                    $curl_Tranfer2_response->reservation_id=$reservation;
                    $curl_Tranfer2_response->reg_number_plate=$regNumberPlate;
       
                    error_log("====request response ====");
                    error_log(json_encode($curl_Tranfer2_response));
                    $this->register_payment_request($curl_Tranfer2_response);
                    return ["Response"=>'Success requested'];
                }
            }else {
                return json_encode($curl_Tranfer2_response, JSON_PRETTY_PRINT);
            }


   }

   public function register_payment_request($transactionDet){
     error_log("====registering new payment request====");
    $payment_ctrl = new PaymentsController();
    $payment_ctrl->register_new_payment($transactionDet);

}

   //function to recieve response from daraja api
   public function callback (Request $request ) {
     //try catch
       $req = $request->Body->stkCallback;
       error_log($req);

       if($req->ResultCode == '0') {
           $transaction_details = $req->CallbackMetadata->Item;
           foreach($transaction_details as $item) {
            if($item->Name == 'Amount') {
                $Amount = $item->Value;
                error_log("===Amount====");
                error_log($Amount);
            }
            if($item->Name == 'MpesaReceiptNumber') {
                $MpesaReceiptNumber = $item->Value;
                error_log("===MpesaReceiptNumber====");
                error_log($MpesaReceiptNumber);
            }
            if($item->Name == 'TransactionDate') {
                $TransactionDate = $item->Value;
                error_log("===TransactionDate====");
                error_log($TransactionDate);
            }
            if($item->Name == 'PhoneNumber') {
                $PhoneNumber = $item->Value;
                error_log("===Paid By====");
                error_log($PhoneNumber);
            }

        }
        $checkoutRequestId = $req->CheckoutRequestID;
        $MerchantRequestID = $req->MerchantRequestID;
        $update = [
            "Amount"=>$Amount,
            "MpesaReceiptNumber"=>$MpesaReceiptNumber,
            "TransactionDate"=>$TransactionDate,
            "PaidBy"=>$PhoneNumber,
        ];
        $paymentcontroller = new PaymentsController();
        $paymentscontroller->pay_parking_fee($checkoutRequestId,$update);

        //success update status reservation
       }
  
    return ["Success"];
   }



   public function register_urls () {
      
       $data = [
        "ShortCode"=> 600982,
        "ResponseType"=> "Completed",
        "ConfirmationURL"=> env('MPESA_TEST_URL').'/api/confirmation',
        "ValidationURL"=> env('MPESA_TEST_URL') .'/api/validation'
       ];
       $url = env('MPESA_REGISTER_URL');
        $response = $this->makeHttp($url,$data);

        return  $response;
   }

   private function makeHttp ($url,$body) {
           
            $access_token = $this->get_auth();
                $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($body));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
   }

   public function validation (Request $request){
         error_log('Validation hit');
         log::info($request->all());
   }
   public function confirmation (Request $request){
    error_log('Confirmation hit');
    log::info($request->all());
   }
  

    
  
    
}
