<?php

namespace App\Http\Controllers\Ussd;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller\Ussd;
use App\Models\Client;

use Illuminate\Http\Request;

class index extends Controller
{ 
    public function ussdIndex (Request $request) {
            // Read the variables sent via POST from our API

          
           
            $sessionId   = $request["sessionId"];
            $serviceCode = $request["serviceCode"];
            $phoneNumber = "254790247803";//$request["phoneNumber"];
            $text        = $request->text;
             
            $isUserExist = true;
            $user = Client::Where('phone_number', $phoneNumber)->get();
            if ($user === null) {
                  // user doesn't exist
              $isUserExist = false;
            }

           

            $menu = new Menu();
  
            if($text == "" && $isUserExist == true){
                 //user is client and string is is empty
                // error_log($user);
               
                echo "CON " . $menu->mainMenuRegistered($user[0]->first_name);
            }else if($text == "" && $isUserExist== false){
                 //user is unregistered and string is is empty
                 $menu->mainMenuUnRegistered();
            }else if($isUserExist== false){
                //user is unregistered and string is not empty
                $textArray = explode("*", $text);
                return $textArray;
                switch($textArray[0]){
                    case "1": 
                       $reserve_response= $menu->ParkingDetails($textArray, $phoneNumber);
                        echo $reserve_response;
                    break;
                    case 2: 
                        $menu->ParkingRates($textArray, $phoneNumber);
                    break;
                    default:
                        echo "END Invalid choice. Please try again";
                }
            }else{
                //user is registered and string is not empty
                $textArray = explode("*", $text);
            
                switch($textArray[0]){
                    case "1": 
                         $reserve_response = $menu->ParkingDetails($textArray, $phoneNumber);
                        echo $reserve_response;
                    break;
                    case 2: 
                        $menu->ParkingRates($textArray, $phoneNumber);
                    break;
                   
                    default:
                        echo "END Invalid menu\n";
                }
            }


    }
    
}
