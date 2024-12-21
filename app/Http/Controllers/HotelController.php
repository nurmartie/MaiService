<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function search(Request $request)
    {
        dd($request);
        $validated = $request->validate([
            'hotel_id' => 'nullable|exists:hotels,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
        ]);
        $query = RoomType::query()->with('hotel');
        if (!empty($validated['hotel_id'])) {
            $query->where('hotel_id', $validated['hotel_id']);
        }
        $query->where('quota', '>=', $validated['guests']);
        $query->whereHas('hotel', function ($q) use ($validated) {
            $q->where('region_id', '!=', null);
        });
        $roomTypes = $query->get();
        return view('results', [
            'roomTypes' => $roomTypes,
            'searchParams' => $validated,
        ]);
    }

    public function getAllHotels()
    {
        $hotels = Hotel::all();
        return view('search', compact('hotels'));
    }

}
