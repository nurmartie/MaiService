<?php

namespace App\Http\Controllers;

use App\Services\MaiApiService;

class MaiApiController extends Controller
{
    protected $service;

    public function __construct(MaiApiService $maiApiService)
    {
        $this->service = $maiApiService;
    }

    public function authApi()
    {
        $response = $this->service->authenticate();
        return response()->json($response);
    }

    public function getRegions()
    {
        $response = $this->service->fetchAndStoreRegions();
        return response()->json($response);
    }

    public function getHotels()
    {
        $response = $this->service->fetchAndStoreHotels();
        return response()->json($response);
    }
}
