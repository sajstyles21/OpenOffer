<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

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

Route::get('documentation', function () {
    return view('vendor.l5-swagger.index');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Api')->group(function () {
    Route::post('count_properties_by_zipcode', [ApiController::class,'getCountPropertiesByZipcode']);
    Route::post('avg_price_by_zipcode', [ApiController::class,'getAvgPriceByZipcode']);
    Route::post('avg_days_by_zipcode', [ApiController::class,'getAvgDaysByZipcode']);
});

