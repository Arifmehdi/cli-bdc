<?php

namespace App\Helpers;

use App\Models\LocationCity;
use App\Models\LocationState;
use Illuminate\Support\Facades\Storage;
use App\Models\MainInventory;

class VehicleDetailCache
{
    protected $cacheDir = 'vehicle_details/';
    protected $cacheExpiration = 86400; // 24 hours in seconds

    public function getByVin($vin)
    {
        if (!$this->hasCache($vin) || !$this->isCacheValid($vin)) {
            return null;
        }

        return $this->getFromCache($vin);
    }

    public function cacheVehicleData($vin)
    {
        // main caching data
        $vehicle = MainInventory::with([
                'additionalInventory',
                'mainPriceHistory',
                'dealer' => function($query) {
                    $query->select('id', 'name', 'city', 'state','dealer_full_address','rating','review');
                }
            ])
            ->where('vin', $vin)
            ->first();

        if (!$vehicle) {
            return false;
        }

        // Prepare related data
        $lowerPrice = (float)$vehicle->price - 5000;
        $higherPrice = (float)$vehicle->price + 5000;

        $otherVehicles = $this->getRelatedVehicles($vehicle, $lowerPrice, $higherPrice);
        $excludedVins = $otherVehicles->pluck('vin')->toArray();
        $relatedVehicles = $this->getRelatedVehicles($vehicle, $lowerPrice, $higherPrice, $excludedVins);

        // Prepare share data
        $url_id = $vehicle->year . '-' . $vehicle->make . '-' . $vehicle->model . '-in-' . $vehicle->dealer->city . '-' . strtoupper($vehicle->dealer->state);
        $shareUrl = url('/best-used-cars-for-sale/listing/' . $vin . '/' . $url_id);

        $data = [
            'vehicle' => $vehicle->toArray(),
            'other_vehicles' => $otherVehicles->toArray(),
            'related_vehicles' => $relatedVehicles->toArray(),
            'share_data' => [
                'url_id' => $url_id,
                'share_url' => $shareUrl,
                'title' => $vehicle->title,
                'image' => $this->getPrimaryImage($vehicle->local_img_url),
            ],
            'tax_data' => $this->getTaxRates($vehicle),
            'cached_at' => now()->toDateTimeString(),
        ];

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        Storage::put($this->cacheDir . $this->getCacheFilename($vin), $jsonData);

        return true;
    }

    protected function getRelatedVehicles($vehicle, $lowerPrice, $higherPrice, $excludedVins = [])
    {
        return MainInventory::with([
                'additionalInventory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'local_img_url');
                },
                'dealer' => function($query) {
                    $query->select('id', 'name', 'city', 'state','zip');
                }
            ])
            ->where('body_formated', $vehicle->body_formated)
            ->where('id', '!=', $vehicle->id)
            ->when(!empty($excludedVins), function($query) use ($excludedVins) {
                $query->whereNotIn('vin', $excludedVins);
            })
            ->whereBetween('price', [$lowerPrice, $higherPrice])
            ->select('id', 'deal_id', 'title', 'type', 'transmission', 'price', 'payment_price',
                    'year', 'make', 'model', 'miles', 'price_rating', 'vin')
            ->take(12)
            ->get();
    }

    protected function getTaxRates($vehicle)
    {
        $stateData = strtoupper($vehicle->dealer->state);
        $stateCity = ucwords($vehicle->dealer->city);

        $stateRate = optional(LocationState::where('short_name', $stateData)->first())->sales_tax ?? 0;
        $cityRate = optional(LocationCity::where('city_name', $stateCity)->first())->sales_tax ?? 0;

        return [
            'state_rate' => (float)$stateRate,
            'city_rate' => (float)$cityRate,
        ];
    }

    protected function getPrimaryImage($imageString)
    {
        $image_splice = explode(',', $imageString);
        return str_replace(["[", "'", "]"], "", $image_splice[0]);
    }

    public function hasCache($vin)
    {
        return Storage::exists($this->cacheDir . $this->getCacheFilename($vin));
    }

    public function isCacheValid($vin)
    {
        $lastModified = Storage::lastModified($this->cacheDir . $this->getCacheFilename($vin));
        return (time() - $lastModified) < $this->cacheExpiration;
    }

    public function getFromCache($vin)
    {
        $content = Storage::get($this->cacheDir . $this->getCacheFilename($vin));
        return json_decode($content, true);
    }

    protected function getCacheFilename($vin)
    {
        $cleanVin = preg_replace('/[^a-zA-Z0-9]/', '', $vin);
        return strtolower($cleanVin) . '.json';
    }

    public function clearCache($vin = null)
    {
        if ($vin) {
            $filename = $this->getCacheFilename($vin);
            if (Storage::exists($this->cacheDir . $filename)) {
                Storage::delete($this->cacheDir . $filename);
            }
        } else {
            Storage::deleteDirectory($this->cacheDir);
            Storage::makeDirectory($this->cacheDir);
        }
    }
}
