<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;

class PaymentsController extends Controller
{
     public function register_new_payment ($transdet){
        error_log("====validating payment ====");
        $transaction = (array) $transdet;
         $request = new Request();
         $request->replace($transaction);
         $request -> validate([
          'CheckoutRequestID'=>'required',
          'MerchantRequestID'=>'required',
          'reg_number_plate'=>'required',
          'reservation_id'=>'required',
      ]);
      
       $data = $request->all();
       $this->save($data);

     }

     private function save ($data) {
        error_log("====saving payment====");
        Payments::create([
            'CheckoutRequestID' => $data['CheckoutRequestID'],
            'MerchantRequestID' => $data['MerchantRequestID'],
            'reg_number_plate' => $data['reg_number_plate'],
            'reservation_id' => $data['reservation_id'],
        ]);
     }

     public function pay_parking_fee($ref,$update) {
        Payments::where('CheckoutRequestID',$ref)->update([
            'Amount'=>$update->Amount,
            'MpesaReceiptNumber'=>$update->MpesaReceiptNumber,
            'TransactionDate'=>$update->TransactionDate,
            'status'=>'completed',
            'PaidBy'=>$update->PaidBy

        ]);

     }


}
