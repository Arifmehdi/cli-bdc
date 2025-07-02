<?php

namespace App\Http\Controllers;

use App\Helpers\VehicleDetailCache;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\MainInventory;
use App\Models\UserTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ListingDetailController extends Controller
{
    public function autoDetails(Request $request, $vin, $slug)
    {
        // $vehicleCache = new VehicleDetailCache();

        // // Try to get from cache
        // $cachedData = $vehicleCache->getByVin($vin);

        // if ($cachedData) {
        //     // Track view from cache
        //     $this->trackView($request, $cachedData['vehicle']);
        //     return view('frontend.auto_details', [
        //         'inventory' => $cachedData['vehicle'],
        //         'relateds' => $cachedData['related_vehicles'],
        //         'shareButtons' => $this->getShareButtons($cachedData['share_data']),
        //         'stateRate' => $cachedData['tax_data']['state_rate'],
        //         'cityrate' => $cachedData['tax_data']['city_rate'],
        //         'other_vehicles' => $cachedData['other_vehicles'],
        //         'source' => 'cache'
        //     ]);
        // }

        $cachePath = storage_path("app/detail_cache/{$vin}.json");

        if (file_exists($cachePath)) {
            $cachedData = json_decode(file_get_contents($cachePath), true);

            // Track view from cache
            $this->trackView($request, $cachedData['vehicle']);

            return view('frontend.auto_details', [
                'inventory' => $cachedData['vehicle'],
                'relateds' => $cachedData['related_vehicles'],
                'shareButtons' => $this->getShareButtons($cachedData['share_data']),
                'stateRate' => $cachedData['tax_data']['state_rate'],
                'cityrate' => $cachedData['tax_data']['city_rate'],
                'other_vehicles' => $cachedData['other_vehicles'],
                'source' => '!!!'
            ]);
        }

        // $cachePath = "detail_cache/{$vin}.json";
        // // Check if cached file exists
        // if (Storage::exists($cachePath)) {
        //     $cachedData = json_decode(Storage::get($cachePath), true);

        //     // Track view from cache
        //     $this->trackView($request, $cachedData['vehicle']);

        //     return view('frontend.auto_details', [
        //         'inventory' => $cachedData['vehicle'],
        //         'relateds' => $cachedData['related_vehicles'],
        //         'shareButtons' => $this->getShareButtons($cachedData['share_data']),
        //         'stateRate' => $cachedData['tax_data']['state_rate'],
        //         'cityrate' => $cachedData['tax_data']['city_rate'],
        //         'other_vehicles' => $cachedData['other_vehicles'],
        //         // 'source' => 'cache'
        //         'source' => '!!!'
        //     ]);
        // }

        // If not in cache, fetch from database
        $inventory = MainInventory::with('additionalInventory', 'mainPriceHistory', 'dealer')
            ->where('vin', $vin)
            ->first();

        if (!$inventory) {
            return redirect()->route('car-not-found');
        }

        // Cache the data for future requests
        // $vehicleCache->cacheVehicleData($vin);

        // Prepare data for view
        $lowerPrice = (float)$inventory->price - 5000;
        $higherPrice = (float)$inventory->price + 5000;

        $other_vehicles = $this->getOtherVehicles($inventory, $lowerPrice, $higherPrice);
        $excludedVins = $other_vehicles->pluck('vin')->toArray();
        $relateds = $this->getRelatedVehicles($inventory, $lowerPrice, $higherPrice, $excludedVins);

        // Track view
        $this->trackView($request, $inventory);

        // Prepare share buttons
        $shareButtons = $this->getShareButtons($inventory);

        // Get tax rates
        $taxRates = $this->getTaxRates($inventory);

        return view('frontend.auto_details', [
            'inventory' => $inventory,
            'relateds' => $relateds,
            'shareButtons' => $shareButtons,
            'stateRate' => $taxRates['state_rate'],
            'cityrate' => $taxRates['city_rate'],
            'other_vehicles' => $other_vehicles,
            // 'source' => 'database'
            'source' => '@'
        ]);
    }

    protected function trackView($request, $inventory)
    {
        $image = $this->getPrimaryImage(is_array($inventory) ? $inventory['additional_inventory']['local_img_url'] : $inventory->local_img_url);
        $title = is_array($inventory)
            ? $inventory['year'] . ' ' . $inventory['make'] . ' ' . $inventory['model']
            : $inventory->year . ' ' . $inventory->make . ' ' . $inventory->model;

        $existingRecord = UserTrack::where([
            'ip_address' => $request->ip(),
            'type' => 'Viewed',
            'title' => $title,
        ])->whereDate('created_at', today())->first();

        if (!$existingRecord) {
            UserTrack::create([
                'type' => 'Viewed',
                'links' => $request->url(),
                'title' => $title,
                'image' => $image,
                'ip_address' => $request->ip(),
                'inventory_id' => is_array($inventory) ? $inventory['id'] : $inventory->id,
                'user_id' => Auth::id(),
                'count' => 1,
            ]);
        } else {
            $existingRecord->increment('count');
        }
    }

    protected function getShareButtons($shareData)
    {
        if (is_object($shareData)) {
            $url_id = $shareData->year . '-' . $shareData->make . '-' . $shareData->model . '-in-' . $shareData->dealer->city . '-' . strtoupper($shareData->dealer->state);
            $shareUrl = url('/best-used-cars-for-sale/listing/' . $shareData->vin . '/' . $url_id);
            $title = urlencode($shareData->title);
        } else {
            $url_id = $shareData['url_id'];
            $shareUrl = $shareData['share_url'];
            $title = urlencode($shareData['title']);
        }

        return [
            'facebook'  => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($shareUrl) . "&quote=" . $url_id,
            'twitter'   => "https://x.com/intent/tweet?url=" . urlencode($shareUrl) . "&text=" . $url_id . "&via=bestdreamcar",
            'linkedin'  => "https://www.linkedin.com/shareArticle?mini=true&url=" . urlencode($shareUrl) . "&title=" . $url_id,
            'whatsapp'  => "https://api.whatsapp.com/send?text=" . $title . "%20" . urlencode($shareUrl),
            'pinterest' => "https://pinterest.com/pin/create/button/?url=" . urlencode($shareUrl) . "&description=" . $url_id,
            'telegram'  => "https://t.me/share/url?url=" . urlencode($shareUrl) . "&text=" . $url_id
        ];
    }

    protected function getTaxRates($inventory)
    {
        $stateData = strtoupper(is_array($inventory) ? $inventory['dealer']['state'] : $inventory->dealer->state);
        $stateCity = ucwords(is_array($inventory) ? $inventory['dealer']['city'] : $inventory->dealer->city);

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

    protected function getOtherVehicles($inventory, $lowerPrice, $higherPrice)
    {
        $inventory = is_array($inventory) ? (object)$inventory : $inventory;

        return MainInventory::with([
            'additionalInventory' => function ($query) {
                $query->select('id', 'main_inventory_id', 'local_img_url');
            },
            'dealer' => function ($query) {
                $query->select('id', 'name', 'city', 'state');
            }
        ])
            ->where('body_formated', $inventory->body_formated)
            ->where('id', '!=', $inventory->id)
            ->where('deal_id', $inventory->deal_id)
            ->whereBetween('price', [$lowerPrice, $higherPrice])
            ->select(
                'id',
                'deal_id',
                'title',
                'type',
                'transmission',
                'price',
                'payment_price',
                'miles',
                'price_rating',
                'vin',
                'year',
                'make',
                'model'
            )
            ->take(12)
            ->get();
    }

    protected function getRelatedVehicles($inventory, $lowerPrice, $higherPrice, $excludedVins = [])
    {
        $inventory = is_array($inventory) ? (object)$inventory : $inventory;

        return MainInventory::with([
            'additionalInventory' => function ($query) {
                $query->select('id', 'main_inventory_id', 'local_img_url');
            },
            'dealer' => function ($query) {
                $query->select('id', 'name', 'city', 'state');
            }
        ])
            ->where('body_formated', $inventory->body_formated)
            ->where('id', '!=', $inventory->id)
            ->when(!empty($excludedVins), function ($query) use ($excludedVins) {
                $query->whereNotIn('vin', $excludedVins);
            })
            ->whereBetween('price', [$lowerPrice, $higherPrice])
            ->select(
                'id',
                'deal_id',
                'title',
                'type',
                'transmission',
                'price',
                'payment_price',
                'year',
                'make',
                'model',
                'miles',
                'price_rating',
                'vin'
            )
            ->take(12)
            ->get();
    }
}
