<?php

use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/search', [HotelController::class, 'getAllHotels'])->name('search.form');
Route::post('/hotels/search', [HotelController::class, 'search'])->name('hotels.search');
