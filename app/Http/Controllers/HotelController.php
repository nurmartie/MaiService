<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Services\MaiApiService;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    protected $service;
    public function __construct(MaiApiService $maiApiService)
    {
        $this->service = $maiApiService;
    }
    public function search(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'numeric',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
        ]);
        $response = $this->service->hotelPriceSearch($validated);
        return view('results', ['response' => $response]);
    }

    public function getAllHotels()
    {
        $hotels = Hotel::all();
        return view('search', compact('hotels'));
    }

}
