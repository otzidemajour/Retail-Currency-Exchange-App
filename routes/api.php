<?php

use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(ExchangeRateController::class)->middleware('auth:sanctum')->group(function() {
    Route::post('/exchange-rate', 'getExchangeRate');
});

Route::controller(TestController::class)->group(function () {
    Route::get('/test', 'index');
});
