<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Validator;
class VehicleController extends Controller
{
    //create vehicle
    public function create($vehicleDetails) {
        //validate request data
        $rules = [
            'vehicle_type'=> 'required',
            'client_ref'=>'required',
            'reg_number_plate'=>'required',
            
        ];

        $validator = Validator::make($vehicleDetails, $rules);
        if ($validator->passes()) {
            $data = $vehicleDetails;
            error_log('validated');
        return $savedVehicle =  $this->save($data);
        } else {
        error_log('not validated');
            //TODO Handle your error
            dd($validator->errors()->all());
        }

    }
    private function save ($data){
        return Vehicle::create([
            'vehicle_type' => $data['vehicle_type'],
            'client_ref' => $data['client_ref'],
            'reg_number_plate' => $data['reg_number_plate'],
           
          ]);
    }
    //vehicle exists
    public function vehicle_exists($reg,$client_ref) {
        $matchThese = ['reg_number_plate' => $reg, 'client_ref' => $client_ref];
        $Vehicle = Vehicle::Where($matchThese)->get();;
        $vehicle_det = null;
        if(count($Vehicle) != 0){
          $vehicle_det = $Vehicle;
        };
        return $vehicle_det;
    }
}
