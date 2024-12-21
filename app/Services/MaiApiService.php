<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Hotel;
use App\Models\Region;
use App\Models\RoomType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

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

    public function hotelPriceSearch(array $data): array
    {
        $authResponse = $this->authenticate();
        if (isset($authResponse['error'])) {
            return ['error' => 'Authentication failed: ' . $authResponse['error']];
        }
        if (!isset($authResponse['RecId'])) {
            return ['error' => 'RecId not found in authentication response'];
        }
        $operatorId = $authResponse['RecId'];
        $hotel = Hotel::where('id_hotel', $data['hotel_id'])->first();
        if (!$hotel) {
            return ['error' => 'Hotel not found'];
        }
        $roomTypes = RoomType::where('hotel_id', $hotel->id_hotel)->get();
        if ($roomTypes->isEmpty()) {
            return ['error' => 'No room types found for the hotel'];
        }
        $roomTypeId = $roomTypes->first()->id_room;
        $boardCode = $roomTypes->first()->boards()->first()->code;
        $responseData = [
            'OperatorId' => $operatorId,
            'RegionId' => $hotel->region_id,
            'BeginDate' => Carbon::parse($data['check_in_date'])->toIso8601String(),
            'EndDate' => Carbon::parse($data['check_out_date'])->toIso8601String(),
            'HotelId' => $data['hotel_id'],
            'Pax' => $data['guests'],
            'Childs' => 0,
            'ChildInfo' => [],
            'RemainderQuotaCheck' => true,
            'SaleDate' => Carbon::now()->toIso8601String(),
            'IsAvailable' => true,
            'WithoutInformation' => true,
            'RoomTypeId' => $roomTypeId,
            'MainRegionId' => $hotel->main_region_id,
            'SubregionId' => $hotel->region_id,
            'BoardCode' => $boardCode,
            'HotelList' => []
        ];

        try {
            $url = "api/Integratiion/HotelPriceSearch";
            $response = $this->client->post($url, [
                'json' => $responseData,
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
