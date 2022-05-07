<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\rate;

class RateController extends Controller
{
    //create rate 
    public function create(Request $request) {
       $request ->validate([
           'section'=>'required',
           'rate_bike'=>'required',
           'rate_car'=>'required',
           'rate_truck'=>'required',
           'rate_tuktuk'=>'required',
           'rate_bus'=>'required'

       ]);
       $data = $request->all();
       $this->save($data);

    }
    private function save($data) {
        rate::create([
            'section' =>$data['section'],
            'rate_bike'=>$data['rate_bike'],
            'rate_car'=>$data['rate_car'],
            'rate_truck'=>$data['rate_truck'],
            'rate_tuktuk'=>$data['rate_tuktuk'],
            'rate_bus'=>$data['rate_bus'],
        ]);

    }
    public function get_rates($section){
         $rates = rate::Where('section',$section)->get();
         return $rates;
    }
}
