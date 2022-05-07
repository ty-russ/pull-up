<?php

namespace App\Http\Controllers\Ussd;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;

class Menu extends Controller
{
    //
         public function mainMenuRegistered($name){
            //shows initial user menu for registered users
            $response = "Welcome " . $name . " Reply with\n";
            $response .= "1. To pay for Parking\n";
            $response .= "2. View Our Rates\n";
          
            return $response; 
        }

        public function mainMenuUnRegistered(){
            //shows initial user menu for unregistered users
            $response = "CON Welcome to PullUp. Reply with\n";
            $response .= "1. To pay for Parking\n";
            $response .= "2. View Our Rates\n";
            echo $response;
        }

        public function ParkingDetails($textArray, $phoneNumber){
          //building menu for user registration 
            $level = count($textArray);
            $vehicle_type;
            $section;
           if($level == 1){
                echo "CON Please enter your full name:";
           } else if($level == 2){
                echo "CON Please enter your ID number:";
           }else if($level == 3){
            $response = "CON Please select Vehicle type:\n";
            $response .= "1. Truck\n";
            $response .= "2. Car\n";
            $response .= "3. Tuktuk\n";
            $response .= "4. Bike\n";
            $response .= "4. Bus\n";
            echo $response;
           } else if ($level == 4) {
            $response = "CON Please select Parking Section:\n";
            $response .= "1. Vip\n";
            $response .= "2. Normal\n";
            if($textArray[3] == "1"){
                $vehicle_type = "Truck";
            }else if($textArray[3] == "2"){
                $vehicle_type = "Car";
            }else if($textArray[3] == "3"){
                $vehicle_type = "Tuktuk";
            }else if($textArray[3] == "4"){
                $vehicle_type = "Bike";
            }else if($textArray[3] == "5"){
                $vehicle_type = "Bus";
            }else {
                echo "END Invalid entry";
            }
            echo $response;
           }else if ($level == 5) {
            $response = "CON Please enter Vehicle Registration Number:\n";
            if($textArray[4] == "1"){
                $section = "Vip";
            }else if($textArray[4] == "2"){
                $section = "Normal";
            }else {
                echo "END Invalid entry";
            }
            echo $response;
           }
        else if($level == 6){
                $phone_number = $phoneNumber;
                $full_name= $textArray[1];
                $id_number = $textArray[2];
                $reg_number_plate = $textArray[5];
                if($textArray[3] == "1"){
                    $vehicle_type = "Truck";
                }else if($textArray[3] == "2"){
                    $vehicle_type = "Car";
                }else if($textArray[3] == "3"){
                    $vehicle_type = "Tuktuk";
                }else if($textArray[3] == "4"){
                    $vehicle_type = "Bike";
                }else if($textArray[3] == "5"){
                    $vehicle_type = "Bus";
                }

                if($textArray[4] == "1"){
                    $section = "Vip";
                }else if($textArray[4] == "2"){
                    $section = "Normal";
                }

                    //connect to DB and register a reservation. 
                    echo "END Success, Enter pin on POP-UP";
                   $park_data = [
                       'phone_number' =>  $phone_number,
                       'full_name' => $full_name,
                       'id_number' =>$id_number,
                       'vehicle_type' => $vehicle_type,
                       'section' => $section,
                       'reg_number_plate' => $reg_number_plate,
                   ];
                   //reserve parking
                   //check if to try catch
                $reservation_controller = new ReservationController;
               $reserve_response = $reservation_controller->validate_reservation($park_data);
                return $reserve_response;
                 
           }
        }

        public function sendMoneyMenu($textArray, $senderPhoneNumber){
            //building menu for user registration 
            $level = count($textArray);
            $receiver = null;
            $nameOfReceiver = null;
            $response = "";
            if($level == 1){
                echo "CON Enter mobile number of the receiver:";
            }else if($level == 2){
                echo "CON Enter amount:";
            }else if($level == 3){
                echo "CON Enter your PIN:";
            }else if($level == 4){
                $receiverMobile = $textArray[1];
                $receiverMobileWithCountryCode = $this->addCountryCodeToPhoneNumber($receiverMobile);
                
                $response .= "Send " . $textArray[2] . " to <Put a person's name here> - " . $receiverMobile . "\n";
                $response .= "1. Confirm\n";
                $response .= "2. Cancel\n";
                $response .= Util::$GO_BACK . " Back\n";
                $response .= Util::$GO_TO_MAIN_MENU .  " Main menu\n";
                echo "CON " . $response;
            }else if($level == 5 && $textArray[4] == 1){
                //a confirm
                //send the money plus
                //check if PIN correct
                //If you have enough funds including charges etc..
                $pin = $textArray[3];
                $amount = $textArray[2];

                //connect to DB
                //Complete transaction

                echo "END We are processing your request. You will receive an SMS shortly";


            }else if($level == 5 && $textArray[4] == 2){
                //Cancel
                echo "END Canceled. Thank you for using our service";
            }else if($level == 5 && $textArray[4] == Util::$GO_BACK){
                echo "END You have requested to back to one step - re-enter PIN";
            }else if($level == 5 && $textArray[4] == Util::$GO_TO_MAIN_MENU){
                echo "END You have requested to back to main menu - to start all over again";
            }else {
                echo "END Invalid entry"; 
            }
        }

        public function withdrawMoneyMenu($textArray){
            //TODO
            echo "CON To be implemented";
        }

        public function checkBalanceMenu($textArray){
            echo "CON To be implemented";
        }

        public function addCountryCodeToPhoneNumber($phone){
            return Util::$COUNTRY_CODE . substr($phone, 1);
//         }
        }
    }
// public function middleware($text){
//     //remove entries for going back and going to the main menu
//     return $this->goBack($this->goToMainMenu($text));
// }

//         public function goBack($text){
//             //1*4*5*1*98*2*1234
//             $explodedText = explode("*",$text);
//             while(array_search(Util::$GO_BACK, $explodedText) != false){
//                 $firstIndex = array_search(Util::$GO_BACK, $explodedText);
//                 array_splice($explodedText, $firstIndex-1, 2);
//             }
//             return join("*", $explodedText);
//         }

//         public function goToMainMenu($text){
//             //1*4*5*1*99*2*1234*99
//             $explodedText = explode("*",$text);
//             while(array_search(Util::$GO_TO_MAIN_MENU, $explodedText) != false){
//                 $firstIndex = array_search(Util::$GO_TO_MAIN_MENU, $explodedText);
//                 $explodedText = array_slice($explodedText, $firstIndex + 1);
//             }
//             return join("*",$explodedText);
//         }
// }
