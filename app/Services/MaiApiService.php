<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Hotel;
use App\Models\Region;
use App\Models\RoomType;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;

class MaiApiService
{
    private $client;
    private $baseUrl;
    private $username;
    private $password;
    private $cookieJar;

    public function __construct()
    {
        $this->baseUrl = env('MAI_API_URL');
        $this->username = env('MAI_API_USER');
        $this->password = env('MAI_API_PASS');
        $this->cookieJar = new CookieJar();
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'cookies' => $this->cookieJar,
        ]);
    }

    public function authenticate()
    {
        try {
            $url = "api/Integratiion/AgencyLogin?username={$this->username}&password={$this->password}";
            $response = $this->client->get($url, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function fetchAndStoreRegions()
    {
        $authResponse = $this->authenticate();
        if (isset($authResponse['error'])) {
            return ['error' => 'Authentication failed: ' . $authResponse['error']];
        }
        if (!isset($authResponse['RecId'])) {
            return ['error' => 'RecId not found in authentication response'];
        }
        $operatorId = $authResponse['RecId'];
        $regionsResponse = $this->getMainRegions($operatorId);
        if (isset($regionsResponse['error'])) {
            return ['error' => 'Failed to fetch regions: ' . $regionsResponse['error']];
        }
        return $this->storeRegions($regionsResponse);
    }

    public function getMainRegions($operatorId)
    {
        try {
            $url = "api/Integratiion/GetMainRegions?operatorId={$operatorId}";
            $response = $this->client->post($url, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function storeRegions($regions)
    {
        try {
            if (!is_array($regions) || empty($regions)) {
                return ['error' => 'No regions to store'];
            }
            foreach ($regions as $region) {
                if (isset($region['Id'], $region['Code'], $region['Remark'])) {
                    Region::updateOrCreate(
                        ['region_id' => $region['Id']],
                        [
                            'code' => $region['Code'],
                            'remark' => $region['Remark'],
                            'country' => $region['Country'] ?? null,
                        ]
                    );
                }
            }
            return ['success' => 'Regions successfully stored in the database'];
        } catch (\Exception $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function fetchAndStoreHotels()
    {
        $authResponse = $this->authenticate();
        if (isset($authResponse['error'])) {
            return ['error' => 'Authentication failed: ' . $authResponse['error']];
        }
        if (!isset($authResponse['RecId'])) {
            return ['error' => 'RecId not found in authentication response'];
        }
        $operatorId = $authResponse['RecId'];
        $hotelsResponse = $this->getActiveHotels($operatorId);
        if (isset($hotelsResponse['error'])) {
            return ['error' => 'Failed to fetch regions: ' . $hotelsResponse['error']];
        }
        return $this->storeHotels($hotelsResponse);
    }

    public function getActiveHotels($operatorId)
    {
        try {
            $url = "api/Integratiion/GetHotelList?operatorId={$operatorId}&isActive=true";
            $response = $this->client->post($url, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function storeHotels($hotels)
    {
        try {
            if (!is_array($hotels) || empty($hotels)) {
                return ['error' => 'No hotels to store'];
            }
            foreach ($hotels as $hotel) {
                $hotelModel = Hotel::create([
                    'id_hotel' => $hotel['Id'],
                    'code' => $hotel['Code'],
                    'remark' => $hotel['Remark'],
                    'region_id' => $hotel['RegionId'],
                    'main_region_id' => $hotel['MainRegionId'],
                    'category_id' => $hotel['CategoryId'],
                    'address' => $hotel['Address'] ?? null,
                ]);
                foreach ($hotel['RoomTypes'] as $room) {
                    $roomModel = RoomType::create([
                        'hotel_id' => $hotelModel->id_hotel,
                        'id_room' => $room['Id'],
                        'code' => $room['Code'],
                        'remark' => $room['Remark'],
                        'quota' => $room['Quota'],
                        'on_request' => $room['OnRequest'],
                        'min_paid_adult' => $room['MinPaidAdult'],
                        'max_adult' => $room['MaxAdult'],
                        'description' => $room['Description'],
                    ]);
                    foreach ($room['Boards'] as $board) {
                        Board::create([
                            'room_type_id' => $roomModel->id,
                            'id_board' => $board['Id'],
                            'code' => $board['Code'],
                            'remark' => $board['Remark'],
                        ]);
                    }
                }
            }
            return ['success' => 'Hotels successfully stored in the database'];
        } catch (\Exception $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

}
