<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Ussd\index;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ussd;
use App\Http\Controllers\MpesaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('reserve',[ReservationController::class,'reserve']);
Route::post('add-rate',[RateController::class,'create']);
Route::get('get-rates',[RateController::class,'get_rates']);
Route::post('ussd',[ussd::class,'ussd']);
Route::post('reserve/ussd',[index::class,'ussdIndex']);
Route::post('/mpesa_stk',[MpesaController::class,'mpesa_stk']);
Route::post('callback',[MpesaController::class,'callback']);

