<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaiApiController;

Route::group(['prefix' => 'mai'], function(){
    Route::get('auth',[MaiApiController::class,'authApi']);
    Route::post('regions', [MaiApiController::class, 'getRegions']);
    Route::post('hotels', [MaiApiController::class, 'getHotels']);
});