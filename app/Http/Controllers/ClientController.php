<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Validator;

class ClientController extends Controller
{
    //create  new client
    public function create ($clientDetails) {
    
         //validate request data
         $rules = [
            'full_name'=> 'required',
            'phone_number'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'id_number'=>'required|min:8',
    
         ];
     
         $validator = Validator::make($clientDetails, $rules);
         if ($validator->passes()) {
             $data = $clientDetails;
             error_log('validated');
           return $savedClient =  $this->save($data);
         } else {
          error_log('not validated');
             //TODO Handle your error
             dd($validator->errors()->all());
         }

    }

    private function save($data)
    {
     
      return Client::create([
        'full_name' => $data['full_name'],
        
        'id_number' => $data['id_number'],
        'phone_number' => $data['phone_number'],
       
      ]);
    }
    //check if client exists
    public function clientExists(string $phone_number) {
        $Client = Client::Where('phone_number',$phone_number)->get();
        $client_det = null;
        if(count($Client) != 0){
          $client_det = $Client;
        };
        return $client_det;


    }
}
