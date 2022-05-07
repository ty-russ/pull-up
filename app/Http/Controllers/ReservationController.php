<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\RateController;
use App\Models\Reservation;
use Validator;
class ReservationController extends Controller
{

  public function validate_reservation($request){
     //validate request data
     $rules = [
       'reg_number_plate'=> 'required',
       'full_name'=> 'required',
      'phone_number'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
       'id_number'=>'required|min:8',
       'vehicle_type'=>'required',
       'section'=>'required'
     ];
    error_log(json_encode($request));
     $validator = Validator::make($request, $rules);
      if ($validator->passes()) {
        $data = $request;
        error_log('====Reservation validated====');
      $reserve = $this->Reserve($data);
       return $reserve;
      } else {
      error_log('not validated');
        //TODO Handle your error
        dd($validator->errors()->all());
        return ["Error" => "INVALID VEHICLE TYPE!"];
      }
  }
    public function Reserve ($det) {
      $details= (object) $det;
      // Instantiate Client controller class 
        $clients_controller = new ClientController;
        //check if user exists
        $client_det = $clients_controller->clientExists($details->phone_number);
              error_log(json_encode($client_det));
        if($client_det == null ){
              //create new client 
              error_log("===creating new client====");

              $clientDetails = (array) $details;
           
               error_log(json_encode("==saving===="));
               $client = $clients_controller->create($clientDetails);
               error_log(json_encode("== saved new client ===$client"));
        } else {
          $client = $client_det;
        }

       //format number plate
      $formattedPlate = $this->formatPlateNumber($det['reg_number_plate']);
       error_log(json_encode("===plate formatted====$formattedPlate"));

       //push to vehicle table
   //check if vehicle exists
      $vehicles_controller = new VehicleController;
      error_log(json_encode("== checking vehicle exists==="));
      $vehicle_det = $vehicles_controller->vehicle_exists($formattedPlate,($client[0]->client_id  ?? $client -> id));
      error_log('=== vehicleexists====');
      error_log(json_encode($vehicle_det));
      error_log('===vehicleexists====');

      
      if($vehicle_det == null){
        //push to cleint table
        $vehicleDetails = (array)$details;
        $vehicleDetails = [
          'reg_number_plate' => $formattedPlate,
          'client_ref' => ($client[0]->client_id?? $client->id),
          'vehicle_type'=>$details->vehicle_type
        ];
        // error_log(json_encode($vehicleDetails));
      
       // error_log(json_encode($clientDetails));
      global $vehicle;
      $vehicle = $vehicles_controller->create($vehicleDetails);
      error_log('===created vehicle====');
      error_log(json_encode($vehicle));
      error_log('===created vehicle====');

      } else {
        $vehicle = $vehicle_det;
      }
      //fetch rates by section
      $rates_controller = new RateController();
      $rates = $rates_controller->get_rates($details->section);
       //attach rate
     
       if(strtoupper($details->vehicle_type) != 'TRUCK' && strtoupper($details->vehicle_type) !== 'TUKTUK' && strtoupper($details->vehicle_type) !=='CAR' && strtoupper($details->vehicle_type) !=='BIKE'  ) {
        return ["Error" => "INVALID VEHICLE TYPE!"];
     };
       if(strtoupper($details->vehicle_type)  == 'TUKTUK') {
          $rateApplied = $rates[0]->rate_tuktuk;
       };
       if(strtoupper($details->vehicle_type)  == 'TRUCK') {
        $rateApplied = $rates[0]->rate_truck;
     };
      if(strtoupper($details->vehicle_type)  == 'CAR') {
          $rateApplied = $rates[0]->rate_car;
      };
      if(strtoupper($details->vehicle_type)  == 'BIKE') {
        $rateApplied = $rates[0]->rate_bike;
       };
      
       error_log(json_decode($rateApplied));
       //error_log($vehicle);
       $data = [
         "reg_number_plate"=> $formattedPlate,
         'user_id'=>($client[0]->client_id ?? $client->id),
         'vehicle_id'=>($vehicle[0]->vehicle_id ?? $vehicle->id),
         'rate'=>$rateApplied,
         'section'=>strtoupper($details->section),
         'phone_number'=>$details->phone_number
       ];
     
       //save
       $this->save($data);
    }
    private function formatPlateNumber(string $plate) {
        //format number plate
       //removes spaces in string
    //    $formattedPlate = str_replace(' ', '', $plate);
       //removes tabs etc
       $formattedPlate = preg_replace('/\s+/', '', $plate);

       //
      return strtoupper($formattedPlate);
    }
    private function save($data) {
      error_log(json_encode($data));
     $reserved = Reservation::create([
        'reg_number_plate' =>$data['reg_number_plate'],
        'user_id'=>$data['user_id'],
        'vehicle_id'=>$data['vehicle_id'],
        'rate'=>$data['rate'],
        'section'=>$data['section']
    ]);
    // pay for service
    $mpesacontroller = new MpesaController();
     $checkout = $mpesacontroller->mpesa_stk($data['phone_number'],$reserved->id,$reserved->reg_number_plate,$data['rate']);
     return $checkout;
    }

    public function ussd (Request $request) {

      error_log(json_encode($request));
          // Read the variables sent via POST from our API
          //alow inovybouss u mpesa 
          $sessionId   = $request["sessionId"];
          $serviceCode = $request["serviceCode"];
          $phoneNumber = $request["phoneNumber"];
          $text        = $request["text"];
          
          if ($text == "") {
              // This is the first request. Note how we start the response with CON
              $response  = "CON What would you want to check \n";
              $response .= "1. My Account \n";
              $response .= "2. My phone number";
  
          } else if ($text == "1") {
              // Business logic for first level response
              $response = "CON Choose account information you want to view \n";
              $response .= "1. Account number \n";
  
          } else if ($text == "2") {
              // Business logic for first level response
              // This is a terminal request. Note how we start the response with END
              $response = "END Your phone number is ".$phoneNumber;
  
          } else if($text == "1*1") { 
              // This is a second level response where the user selected 1 in the first instance
              $accountNumber  = "ACC1001";
  
              // This is a terminal request. Note how we start the response with END
              $response = "END Your account number is ".$accountNumber;
  
          }
  
          // Echo the response back to the API
          header('Content-type: text/plain');
          return $response;
     }
}
