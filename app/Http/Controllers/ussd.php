<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ussd extends Controller
{
   public function ussd (Request $request) {

    error_log(json_encode($request));
        // Read the variables sent via POST from our API
        $sessionId   = $request["sessionId"];
        $serviceCode = $request["serviceCode"];
        $phoneNumber = $request["phoneNumber"];
        $text        = $request["text"];

        $rates = [
            "Truck" => "500",
            "Car" =>  "200",
            "Bike" => "100",
            "Tuktuk" => "50"
        ];
        
        if ($text == "") {
            // This is the first request. Note how we start the response with CON
            $response  = "CON Welcome to pull-up! \n";
            $response .= "1. To pay for parking \n";
            $response .= "2. View our Rates \n";
       
        } else if ($text == "1") {
            // Business logic for first level response
            $response = "CON Choose vehicle type. \n";
            $response .= "1. Truck \n";
            $response .= "2. Car \n";
            $response .= "3. Bike \n";
            $response .= "4. Tuktuk \n";
            $response .= "4. Bus \n";

        } else if ($text == "2") {
            // Business logic for first level response
            // This is a terminal request. Note how we start the response with END
            $response = "END All rates are fixed".$rates;

        } else if($text == "1*1") { 
            // This is a second level response where the user selected 1 in the first instance and one in the second
            // This is a terminal request. Note how we start the response with END
            $vehicle_type = "Truck";
            $response = "CON Choose section. \n ";
            $response = "1. VIP \n ";
            $response = "2. Normal \n ";
            
        } else if($text == "1*2") { 
            // This is a second level response where the user selected 1 in the first instance and one in the second
            // This is a terminal request. Note how we start the response with END
            $vehicle_type = "Car";
            $response = "CON Choose section. \n ";
            $response = "1. VIP \n ";
            $response = "2. Normal \n ";
            
        }
        else if($text == "1*3") { 
            // This is a second level response where the user selected 1 in the first instance and one in the second
            // This is a terminal request. Note how we start the response with END
            $vehicle_type = "Bike";
            $response = "CON Choose section. \n ";
            $response = "1. VIP \n ";
            $response = "2. Normal \n ";
            
        }else if($text == "1*4") { 
            // This is a second level response where the user selected 1 in the first instance and one in the second
            // This is a terminal request. Note how we start the response with END
            $vehicle_type = "Tuktuk";
            $response = "CON Choose section. \n ";
            $response = "1. VIP \n ";
            $response = "2. Normal \n ";
            
        }else if($text == "1*5") { 
            // This is a second level response where the user selected 1 in the first instance and one in the second
            // This is a terminal request. Note how we start the response with END
            $vehicle_type = "Bus";
            $response = "CON Choose section. \n ";
            $response = "1. VIP \n ";
            $response = "2. Normal \n ";
            
        }
        
        
        
        
        else if($text == "1*1*1") { 
            // This is a second level response where the user selected 1 in the first instance and 1 in the second instance            
        
            
            $response = "CON Enter Vehicle Registartion Plate No.. \n ";
          

        }else if($text == "1*3") { 
            // This is a second level response where the user selected 1 in the first instance
            
        

            $response = "CON Pay for parking  \n ";
            $response = "1. Yes \n ";
            $response = "2. No \n ";

        }else if($text == "1*4") { 
            // This is a second level response where the user selected 1 in the first instance
            
      

            $response = "END Thank You \n ";
          

        }

        // Echo the response back to the API
        header('Content-type: text/plain');
        return $response;
   }

}
