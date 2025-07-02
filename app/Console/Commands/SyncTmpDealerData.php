<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Latlongs;
use App\Models\TmpDealer;
use App\Models\VehicleMake;
use App\Models\MainInventory;
use App\Models\TmpInventories;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\MainPriceHistory;
use App\Models\AdditionalInventory;
use Exception;

class SyncTmpDealerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-tmp-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from Tmpdealer to main_inventories, additional_inventories, and price_history tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve all data from the Tmpdealer model
        $tmpDealers = TmpDealer::where('batch_no', 111)->get();

        $existingInventoryByDealer = MainInventory::select('deal_id', 'vin')
                                ->whereNotNull('vin')
                                ->get()
                                ->groupBy('dealer_id')
                                ->map(fn ($inventory) => $inventory->pluck('vin')->toArray());



        $inventory_sold = [];
        $inventory_added = [];
        $csvVINsByDealer = [];

        $tmp_imported = [];
        $tmp_updated = [];
        $tmp_skipped = [];

        $imported = [];
        $updated = [];
        $skipped = [];

        $inventory = MainInventory::with('dealer')->select('*')->get();   //vin not mention yegt
        $latestBatchNo = MainInventory::latest('batch_no')->value('batch_no');
        $batch_no = $latestBatchNo ? $latestBatchNo + 1 : 101;



        foreach ($tmpDealers as $tmpDealer) {
            $dealer_name_data = $tmpDealer->dealer_name;
            $zip_info = $tmpDealer->dealer_zip_code;
            $dealer_rating = $tmpDealer->dealer_rating;
            $dealer_review = $tmpDealer->dealer_review;
            $dealer_id_data = $tmpDealer->customer_id;


            $dealer_full_address_data = $tmpDealer->dealer_address;
            $dealer_address_data = $tmpDealer->dealer_street;
            $dealer_website = $tmpDealer->dealer_website;
            $brand_website = $tmpDealer->brand_website;
            $city_info = $tmpDealer->dealer_city;
            $state_info = $tmpDealer->dealer_region;

            $vin_data = $tmpDealer->vin;
            $year_data = $tmpDealer->year;
            $make_data = $tmpDealer->make;
            $model_data = $tmpDealer->model;
            $all_image_data = $tmpDealer->all_image;
            $body_type_data = $tmpDealer->body_type;
            $seller_note = $tmpDealer->seller_note;
            $source_url = $tmpDealer->dealer_website;
            $dealer_option_info = $tmpDealer->options;
            $titles_data = $tmpDealer->titles;
            $trim_data = $tmpDealer->trim_name;
            $exterior_color_data = $tmpDealer->exterior_color;
            $interior_color_data = $tmpDealer->interior_color;
            $city_mpg_data = $tmpDealer->city_mpg;
            $hwy_mpg_data = $tmpDealer->hwy_mpg;
            $engine_data = $tmpDealer->engine;
            $transmission_data = $tmpDealer->transmission;
            $type_data = $tmpDealer->type;
            $stock_num_data = $tmpDealer->stock_number;
            $feature_data = $tmpDealer->feature;
            $drive_train_data = $tmpDealer->drive_train;
            $price_history_data = $tmpDealer->price_history;
            $primary_image_data = $tmpDealer->primary_image;
            $all_images_data = $tmpDealer->all_image;
            $vin_image_data = $tmpDealer->vin_image;
            $batch_no_data = $tmpDealer->batch_no;

            $dealerPhone = $tmpDealer->dealer_sales_phone;
            $phone_data = ($dealerPhone && $dealerPhone != '----') ? preg_replace('/\D/', '', $dealerPhone) : null;
            $price_info = $tmpDealer->price;
            $price_data = ($price_info && is_string($price_info)) ? (float) preg_replace('/[^\d.]/', '', $price_info) : 0;
            // Log::info("Inserting price_data: " . var_export($price_data, true));
            $price_rating = $tmpDealer->price_rating;
            $mile_info = $tmpDealer->mileage;
            $mileage_data = ($mile_info) ? preg_replace('/\D/', '', $mile_info) : 0;


            $dataCollection = collect([
                'dealer_id' => $dealer_id_data ?? null,
                'dealer_name' => $dealer_name_data ?? null,
                'dealer_full_address' => $dealer_full_address_data ?? null,
                'dealer_address' => $dealer_address_data ?? null,
                'dealer_city' => $city_info ?? null,
                'dealer_region' => $state_info ?? null,
                'dealer_zip' => $zip_info ?? null,
                'dealer_phone' => $phone_data ?? null,
                'dealer_rating' => $dealer_rating ?? null,
                'dealer_review' => $dealer_review ?? null,
                'dealer_website' => $dealer_website ?? null,
                'seller_note' => $seller_note ?? null,
                'brand_website' => $brand_website ?? null,
                'source_url' => $source_url ?? null,
                'dealer_option' => $dealer_option_info ?? null,
                'titles' => $titles_data ?? null,
                'trim' => $trim_data ?? null,
                'make' => $make_data ?? null,
                'model' => $model_data ?? null,
                'exterior_color' => $exterior_color_data ?? null,
                'interior_color' => $interior_color_data ?? null,
                'price' => $price_data ?? 0,
                'price_rating' => $price_rating ?? null,
                'milage' => $mileage_data,
                'fuel' => $fuel_data ?? null,
                'city_mpg_data' => $city_mpg_data ?? null,
                'hwy_mpg_data' => $hwy_mpg_data ?? null,
                'engine' => $engine_data ?? null,
                'transmission' => $transmission_data ?? null,
                'year' => $year_data ?? null,
                'type' => $type_data ?? null,
                'stock_number' => $stock_num_data ?? null,
                'vin' => $vin_data ?? null,
                'body_type' => $body_type_data ?? null,
                'feature' => $feature_data ?? null,
                'drive_train' => $drive_train_data ?? null,
                'price_history_data' => $price_history_data ?? null,
                'primary_image' => $primary_image_data ?? null,
                'all_images' => $all_images_data ?? null,
                'vin_image_data' => $vin_image_data ?? null,
                'batch_no_data' => $batch_no_data ?? null,

                // // 'dealer_no' => $rowData['dealer no'] ?? null,
                // // 'avg_mpg' => $avg_mpg_data ?? null,
                // 'sales_status' => $rowData['salesstatus'] ?? null,
                // 'days_on_market' => $rowData['days on market'] ?? null,
                // 'inventory_status' => $inventory_status,
            ]);


            $latlongData = $this->getLatLong($zip_info);
            $dealer = User::where('name', $dealer_name_data)->where('zip', $zip_info)->first();

            if ($dealer) {
                $dealerId = $dealer->dealer_id;
                $csvVINsByDealer[$dealerId][] = $vin_data;

                if($dealer->rating == null && $dealer_rating){
                    $dealer->rating = $dealer_rating;
                    $dealer->review = $dealer_review;
                    $dealer->save();
                }
                $dealer_id = $dealer->dealer_id;
                $user_id = $dealer->id;
            }else{

                $custom_dealer_id = $dealer_id_data;

                $user = new User();
                $user->dealer_id           = $custom_dealer_id;
                $user->name                = $dealer_name_data;
                $user->phone               = $phone_data;
                $user->dealer_full_address = $dealer_full_address_data;
                $user->rating              = $dealer_rating;
                $user->review              = $dealer_review;
                $user->address             = $dealer_address_data;
                // $user->dealer_website      = $dealer_website;
                $user->brand_website       = $brand_website;
                $user->city                = $city_info;
                $user->state               = $state_info;
                $user->zip                 = $zip_info;
                $user->status              = 3;
                $user->dealer_iframe_map   =  null;
                $user->save();

                $user_id = $user->id;
                $dealer_id = $user->dealer_id;

                $role = 'dealer';
                $role =  Role::where('name', $role)->first();
                // $role = Role::find(2);
                if (!$role) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "The specified role does not exist or is not for the 'web' guard.",
                    ], 422);
                }
                $user->assignRole($role);

            }
            $errors = [];
            $saveDir = public_path('listing/' . $vin_data);
            $this->ensureDirectoryExists($saveDir);

            // $fileNameCustom = strtolower($year_data.'_'.$make_data.'_'.$model_data.'-pic-').$vin_data;
            $fileNameCustom = strtolower(str_replace(' ', '_', $year_data.'_'.$make_data.'_'.$model_data.'-pic-').$vin_data);
            $localImagePaths = $this->processImages($all_image_data, $saveDir, $vin_data, $fileNameCustom);
            $localImagePathsString = implode(',', $localImagePaths);

            $formattedDate = now()->format('Y-m-d');
            $monthlyPayment = $this->calculateMonthlyPayment((int) $price_data);
            $carBody = $this->determineCarBody(strtolower($body_type_data));

            $vehicleMakeData = VehicleMake::where('make_name', $make_data)->first();

            if (!$vehicleMakeData) {
                $vehicleMakeData = VehicleMake::firstOrCreate(['make_name' => $make_data]);
            }
            $vehicleMakeDataID = $vehicleMakeData->id;

            // // Check if VIN exists in main_inventories
            // $tmp_inventory = TmpInventories::where('vin', $vin_data)->first();
            $row = $tmpDealer->toarray();

            $userData = User::where('name', 'Super Admin')->first();
            $auth_user_id = Auth::check() ? Auth::user()->id : null;  // Check if user is logged in

            $userId = $auth_user_id ?? $userData->id;

            // if ($tmp_inventory) {
            //         // Update main_inventory
            //         $changes = $this->updateTmpInventory($tmp_inventory, $dataCollection, $localImagePathsString, $vehicleMakeDataID, $monthlyPayment, $carBody);
            //         if (!empty($changes)) {
            //             $tmp_updated[] = [
            //                 'vin' => $tmp_inventory->vin,
            //                 'title' => $tmp_inventory->title,
            //                 'changes' => $changes,
            //             ];
            //         }
            // } else {
            //         // Insert new inventory
            //         try {
            //             // $this->insertTmpInventory($rowData);
            //             $dato = $this->insertTmpInventory($row, $userId, $dealer_id, $user_id, $dataCollection, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate,$monthlyPayment,$carBody);

            //             $tmp_imported[] = [
            //                 'vin' => $vin_data,
            //                 'title' => $titles_data,
            //             ];
            //         } catch (\Exception $e) {
            //             $failed[] = $row; // Log failed rows
            //         }
            // }

            $mainInventory = MainInventory::with('mainPriceHistory','additionalInventory')->where('vin', $vin_data)->first();

            if ($mainInventory) {

                    // // Update main_inventory
                    // $changes = $this->updateInventory($mainInventory, $dataCollection, $localImagePathsString, $vehicleMakeDataID, $monthlyPayment, $carBody);

                    // if (!empty($changes)) {
                    //     $updated[] = [
                    //         'vin' => $inventory->vin,
                    //         'title' => $inventory->title,
                    //         'changes' => $changes,
                    //     ];
                    // }
            } else {
                    // Insert new inventory
                    // dd($mainInventory);
                    try {

                        $d =$this->insertInventory($row, $userId, $dealer_id, $user_id, $dataCollection,$latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate,$monthlyPayment,$carBody);

                        $inserted[] = [
                            'vin' => $vin_data,
                            'title' => $titles_data,
                        ];
                    } catch (\Exception $e) {
                        $failed[] = $row; // Log failed rows
                    }
            }

            $this->info("Synced data for product ID: {$tmpDealer->vin}");
        }

        $this->info('All data synced successfully!');

        return 0;
    }


    private function getLatLong($zipCode)
    {
        // Check if data already exists in the database
        $latlongData = Latlongs::where('zip_code', $zipCode)->select('zip_code', 'latitude', 'longitude')->first();

        if ($latlongData) {
            return [
                'zip_code' => $latlongData->zip_code,
                'latitude' => $latlongData->latitude,
                'longitude' => $latlongData->longitude
            ];
        }

        // API request to OpenCage
        $apiKey = '4b84ff4ad9a74c79ad4a1a945a4e5be1';
        $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},us&key={$apiKey}";

        try {
            $response = Http::get($url);

            // If API fails, log and return null values
            if (!$response->successful()) {
                throw new Exception("Geolocation API error: HTTP " . $response->status());
            }

            $data = $response->json();

            if (isset($data['results'][0]['geometry'])) {
                $geometry = $data['results'][0]['geometry'];

                // Save the data in the database
                $newLatlongData = Latlongs::create([
                    'zip_code' => $zipCode,
                    'latitude' => $geometry['lat'],
                    'longitude' => $geometry['lng']
                ]);

                return [
                    'zip_code' => $newLatlongData->zip_code,
                    'latitude' => $newLatlongData->latitude,
                    'longitude' => $newLatlongData->longitude
                ];
            } else {
                throw new Exception("Invalid response format: No geometry found.");
            }
        } catch (Exception $e) {
            // Log::warning("Failed to get lat/long for ZIP code $zipCode: " . $e->getMessage());
        }

        // Return null values if an error occurs
        return [
            'zip_code' => $zipCode,
            'latitude' => null,
            'longitude' => null
        ];
    }


    // private function getLatLong($zipCode)
    // {

    //     $latlongData = Latlongs::where('zip_code', $zipCode)->select('zip_code','latitude','longitude')->first();

    //     if ($latlongData) {
    //         // Return the existing data
    //         return [
    //             'zip_code' => $latlongData->zip_code,
    //             'latitude' => $latlongData->latitude,
    //             'longitude' => $latlongData->longitude
    //         ];
    //     }

    //         $apiKey = '4b84ff4ad9a74c79ad4a1a945a4e5be1';
    //         $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},us&key={$apiKey}";

    //         $response = Http::get($url);
    //         if ($response->successful()) {
    //             $data = $response->json();
    //             if (isset($data['results'][0]['geometry'])) {
    //                 $geometry = $data['results'][0]['geometry'];
    //                 // Save the data in the database
    //                 $newLatlongData = Latlongs::create([
    //                     'zip_code' => $zipCode,
    //                     'latitude' => $geometry['lat'],
    //                     'longitude' => $geometry['lng']
    //                 ]);
    //                 // Return the newly created data
    //                 return [
    //                     'zip_code' => $newLatlongData->zip_code,
    //                     'latitude' => $newLatlongData->latitude,
    //                     'longitude' => $newLatlongData->longitude
    //                 ];
    //             }
    //         }
    //     // Log a warning if no results were found and return null values
    //     Log::warning("No results found for ZIP code $zipCode");
    //     return [
    //         'zip_code' => $zipCode,
    //         'latitude' => null,
    //         'longitude' => null
    //     ];
    // }

    private function ensureDirectoryExists($directory)
    {
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
        }
    }

    private function processImages($imageString, $saveDir, $vin, $fileNameCustom)
    {
        $imageUrls = array_filter(explode(',', $imageString)); // Filter out empty values
        $imageCount = min(count($imageUrls), 5); // Limit to a maximum of 5 images
        $localImagePaths = [];

        // Ensure the directory exists
        $vinDir = $saveDir . '/' . $vin;
        if (!File::exists($vinDir)) {
            File::makeDirectory($vinDir, 0755, true); // Create the directory if it doesn't exist
        }

        // Process up to 5 images
        for ($index = 1; $index <= $imageCount; $index++) {
            $url = $imageUrls[$index - 1]; // Get the corresponding URL
            $fileName = sprintf('%s_%02d.jpg', $vin, $index); // Two-digit filename format
            $localPath = $vinDir . '/' . $fileName; // Full path to save the image

            // If the file already exists, skip downloading
            if (File::exists($localPath)) {
                // Log::info("Image already exists, skipping download: $localPath");
                $localImagePaths[] = 'listing/' . $vin . '/' . $fileName; // Add existing path to the array
                continue; // Move to the next image
            }

            // // Download only if URL is valid
            // if ($url) {
            //     try {
            //         $response = Http::get($url);
            //         if ($response->successful()) {
            //             File::put($localPath, $response->body()); // Save the file locally
            //             Log::info("Successfully downloaded image: $url");
            //         } else {
            //             Log::warning("Failed to download image: $url");
            //         }
            //     } catch (\Exception $e) {
            //         Log::error("Error downloading image $url: " . $e->getMessage());
            //     }

            //     sleep(rand(4, 6)); // Optional delay to avoid server overload
            // } else {
            //     Log::warning("No URL provided for image index: $index");
            // }

            // Add the local path (even if not downloaded) to the array
            $localImagePaths[] = 'listing/' . $vin . '/' . $fileName;
        }

        return $localImagePaths; // Return all processed local image paths
    }

    private function calculateMonthlyPayment(int $price)
    {
        try {
            $salesTaxRate = 0.08;
            $interestRate = 0.09;
            $loanTermMonths = 72;

            $salesTax = $price * $salesTaxRate;
            $loanAmount = $price + $salesTax;
            $monthlyInterestRate = $interestRate / 12;

            return ceil(
                ($loanAmount * $monthlyInterestRate) /
                (1 - pow(1 + $monthlyInterestRate, -$loanTermMonths))
            );
        } catch (\Throwable $th) {
            // Log::error("Error calculating monthly payment: " . $th->getMessage());
            return 0;
        }
    }

    private function determineCarBody($lowerString)
    {
        if (strpos($lowerString, 'coupe') !== false || strpos($lowerString, '2dr') !== false) {
            return 'Coupe';
        } elseif (strpos($lowerString, 'hetchback') !== false || strpos($lowerString, '3dr') !== false) {
            return 'Hatchback';
        } elseif (strpos($lowerString, 'sedun') !== false || strpos($lowerString, '4dr') !== false) {
            return 'Sedun';
        } elseif (strpos($lowerString, 'pickup') !== false) {
            return 'Truck';
        } elseif (strpos($lowerString, 'cargo') !== false) {
            return 'Cargo Van';
        } elseif (strpos($lowerString, 'passenger') !== false) {
            return 'Passenger Van';
        } elseif (strpos($lowerString, 'Mini-van') !== false) {
            return 'Minivan';
        } elseif (strpos($lowerString, 'sport') !== false) {
            return 'SUV';
        } else {
            return $lowerString; // default to the original string if no match is found
        }
    }


        // Update inventory and track changes
        private function updateTmpInventory($tmpinventory, $data, $localImagePathsString, $vehicleMakeDataID, $monthlyPayment, $carBody)
        {

            $changes = [];


            $fieldsToCheck = [
                'img_from_url' => $data['all_images'],
                'local_img_url' => $localImagePathsString,
                'vehicle_make_id' => $vehicleMakeDataID,
                'title' => $data['titles'],
                'year' => $data['year'],
                'make' => $data['make'],
                'model' => $data['model'],
                'price' => $data['price'],
                'price_rating' => $data['price_rating'],
                'miles' => $data['milage'],
                'type' => $data['type'],
                'trim' => $data['trim'],
                'stock' => $data['stock_number'],
                'engine_details' => $data['engine'],
                'transmission' => $data['transmission'],
                'vehicle_feature_description' => $data['feature'],
                'vehicle_additional_description' => $data['dealer_option'],
                'seller_note' => $data['seller_note'],
                'price_history' => $data['price_history_data'],
                'fuel' => $data['fuel'],
                'drive_info' => $data['drive_train'],
                // 'mpg' => $data['avg_mpg'],
                'mpg_city' => $data['city_mpg_data'],
                'mpg_highway' => $data['hwy_mpg_data'],
                'exterior_color' => $data['exterior_color'],
                'interior_color' => $data['interior_color'],
                'payment_price' => $monthlyPayment,
                'body_formated' => $carBody,
                // 'inventory_status' => $data['inventory_status'],
            ];

            foreach ($fieldsToCheck as $field => $newValue) {
                if ($tmpinventory->$field != $newValue) {
                    $changes[$field] = [
                        'old' => $tmpinventory->$field,
                        'new' => $newValue,
                    ];
                    $tmpinventory->$field = $newValue;
                }
            }
            // Save changes
            $tmpinventory->save();

            return $changes;
        }

            // Insert inventory into multiple tables
    public function insertTmpInventory(array $row, int $id, $dealer_id, int $user_id, $dataCollection, $latlongData, $localImagePaths, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody)
    {
        try {
            // Insert into tmp_inventories
            $tmpInventory = TmpInventories::create([
                'dealer_id' => $dealer_id,
                'deal_id' => $user_id,
                'zip_code' => $dataCollection['dealer_zip'],
                'latitude' => $latlongData['latitude'],
                'longitude' => $latlongData['longitude'],
                'detail_url' => $dataCollection['source_url'],
                'img_from_url' => $dataCollection['all_images'],
                'local_img_url' => $localImagePaths,
                'vehicle_make_id' => $vehicleMakeDataID,
                'title' => $dataCollection['titles'],
                'year' => $dataCollection['year'],
                'make' => $dataCollection['make'],
                'model' => $dataCollection['model'],
                'vin' => $dataCollection['vin'],
                'price' => $dataCollection['price'],
                'price_rating' => $dataCollection['price_rating'],
                'miles' => $dataCollection['milage'],
                'type' => $dataCollection['type'],
                'trim' => $dataCollection['trim'],
                'stock' => $dataCollection['stock_number'],
                'engine_details' => $dataCollection['engine'],
                'transmission' => $dataCollection['transmission'],
                'vehicle_feature_description' => $dataCollection['feature'],
                'vehicle_additional_description' => $dataCollection['dealer_option'],
                'seller_note' => $dataCollection['seller_note'],
                'price_history' => $dataCollection['price_history_data'],
                'fuel' => $dataCollection['fuel'],
                'drive_info' => $dataCollection['drive_train'],
                // 'mpg' => $dataCollection['avg_mpg'],
                'mpg_city' => $dataCollection['city_mpg_data'],
                'mpg_highway' => $dataCollection['hwy_mpg_data'],
                'exterior_color' => $dataCollection['exterior_color'],
                'interior_color' => $dataCollection['interior_color'],
                'created_date' => $formattedDate,
                'stock_date_formated' => $formattedDate,
                'user_id' => $id,
                'payment_price' => $monthlyPayment,
                'body_formated' => $carBody,
                'is_feature' => 0,
                'status' => 1,
                // 'inventory_status' => $dataCollection['inventory_status'],
                'batch_no' => $dataCollection['batch_no_data'],
            ]);

            // Return the inserted data to confirm success
            return $tmpInventory;

        } catch (\Exception $e) {
            // Log the error for debugging
            // Log::error('Error inserting tmp inventory: ' . $e->getMessage(), [
            //     'data' => $row,
            //     'error' => $e->getTraceAsString(),
            // ]);

            // Return null or handle the error as per your application's requirement
            return null;
        }



    }
    // inventory update

            // Update inventory and track changes
            private function updateInventory($mainInventory, $rowData, $localImagePathsString, $vehicleMakeDataID, $monthlyPayment, $carBody)
            {
                // dd($rowData['price_history_data'], $mainInventory->mainPriceHistory, $rowData);
                // dd($mainInventory,$rowData);
                // dd(($tmpinventory->inventory_status == $data['inventory_status']),$tmpinventory->inventory_status, $data['inventory_status']);
                // dd(($tmpinventory->body_formated == $carBody),$tmpinventory->body_formated, $carBody);
                DB::beginTransaction();

                try {

                    $changes = []; // Track changes for logging/debugging

                    // Update MainInventory fields
                    $mainFields = [
                        // 'deal_id' => $rowData['deal_id'],
                        // 'zip_code' => $rowData['zip_code'],
                        // 'latitude' => $rowData['latitude'],
                        // 'longitude' => $rowData['longitude'],
                        'vehicle_make_id' => $vehicleMakeDataID,
                        'title' => $rowData['titles'],
                        'year' => $rowData['year'],
                        'make' => $rowData['make'],
                        'model' => $rowData['model'],
                        'price' => $rowData['price'],
                        'price_rating' => $rowData['price_rating'],
                        'miles' => $rowData['milage'],
                        'type' => $rowData['type'],
                        'trim' => $rowData['trim'],
                        'stock' => $rowData['stock_number'],
                        'transmission' => $rowData['transmission'],
                        'engine_details' => $rowData['engine'],
                        'fuel' => $rowData['fuel'],
                        'drive_info' => $rowData['drive_train'],
                        'mpg' => $rowData['avg_mpg'],
                        'mpg_city' => $rowData['city_mpg_data'],
                        'mpg_highway' => $rowData['hwy_mpg_data'],
                        'exterior_color' => $rowData['exterior_color'],
                        'interior_color' => $rowData['interior_color'],
                        'payment_price' => $monthlyPayment,
                        'body_formated' => $carBody,
                        'inventory_status' => $rowData['inventory_status'],
                    ];

                    foreach ($mainFields as $field => $value) {
                        if ($mainInventory->$field != $value) {
                            $changes['mainInventory'][$field] = [
                                'old' => $mainInventory->$field,
                                'new' => $value,
                            ];
                            $mainInventory->$field = $value;
                        }
                    }

                    // Save changes to MainInventory
                    $mainInventory->save();


                    // Update PriceHistory relation
                    $priceHistory = $mainInventory->mainPriceHistory;


                    // khfjkhw ejfhwjf wejufghuiwhfui wghfyui yeyuif yuiegfyuegf yuergfyu eyufgwerugyuiwegywery yueryuiwegy er

                    $csvPriceHistoryRaw = $rowData['price_history_data'];
                        $csvRecords = explode(',', $csvPriceHistoryRaw);
                        $csvParsed = [];

                        foreach ($csvRecords as $record) {
                            $parts = explode(';', trim($record));
                            if (count($parts) === 3) {
                                $csvParsed[] = [
                                    'change_date' => Carbon::createFromFormat('m/d/y', trim($parts[0]))->format('Y-m-d'),
                                    'change_amount' => trim($parts[1]),
                                    'amount' => (float)str_replace(['$', ','], '', trim($parts[2])),
                                ];
                            }
                        }

                        // Iterate over parsed CSV records and compare with database
                        foreach ($csvParsed as $csvData) {
                            $existingRecord = $priceHistory->where('change_date', $csvData['change_date'])->first();

                            if ($existingRecord) {
                                foreach (['change_date', 'change_amount', 'amount'] as $field) {
                                    if ($existingRecord->$field != $csvData[$field]) {
                                        $changes[$field] = [
                                            'old' => $existingRecord->$field,
                                            'new' => $csvData[$field],
                                        ];
                                        $existingRecord->$field = $csvData[$field]; // Update field with new value
                                    }
                                }

                                // Save the record if changes were made
                                    $existingRecord->save();
                                    // Log::info("Updated PriceHistory record for date {$csvData['change_date']}.", $changes);
                            }
                        }
                    // khfjkhw ejfhwjf wejufghuiwhfui wghfyui yeyuif yuiegfyuegf yuergfyu eyufgwerugyuiwegywery yueryuiwegy er


                    // Update AdditionalInventory relation
                    $additionalInventory = $mainInventory->additionalInventory;

                    if ($additionalInventory) {
                        $additionalInventoryFields = [
                            'detail_url' => $rowData['source_url'],
                            'img_from_url' => $rowData['all_images'],
                            'local_img_url' => $localImagePathsString,
                            'vehicle_feature_description' => $rowData['feature'],
                            'vehicle_additional_description' => $rowData['dealer_option'],
                            'seller_note' => $rowData['seller_note'],
                        ];

                        foreach ($additionalInventoryFields as $field => $value) {
                            if ($additionalInventory->$field != $value) {
                                $changes['additionalInventory'][$field] = [
                                    'old' => $additionalInventory->$field,
                                    'new' => $value,
                                ];
                                $additionalInventory->$field = $value;
                            }
                        }


                        $additionalInventory->save();
                    }

                    // Log changes
                    if (!empty($changes)) {
                        // Log::info('Inventory and relations updated successfully.', $changes);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    // Log::error('Error updating inventory: ' . $e->getMessage());
                    throw $e;
                }

                return $changes;
            }




            private function insertInventory($row, $id, $dealer_id, $user_id, $dataCollection, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody)
            {
                // dd($dataCollection['price_history_data']);
                try {
                    // ✅ Insert into main_inventories
                    $mainInventory = MainInventory::create([
                        'deal_id' => $user_id,
                        'zip_code' => $dataCollection['dealer_zip'],
                        'latitude' => $latlongData['latitude'],
                        'longitude' => $latlongData['longitude'],
                        'vehicle_make_id' => $vehicleMakeDataID,
                        'title' => $dataCollection['titles'],
                        'year' => $dataCollection['year'],
                        'make' => $dataCollection['make'],
                        'model' => $dataCollection['model'],
                        'vin' => $dataCollection['vin'],
                        'price' => $dataCollection['price'],
                        'price_rating' => $dataCollection['price_rating'],
                        'miles' => $dataCollection['milage'],
                        'type' => $dataCollection['type'],
                        'trim' => $dataCollection['trim'],
                        'stock' => $dataCollection['stock_number'],
                        'transmission' => $dataCollection['transmission'],
                        'engine_details' => $dataCollection['engine'],
                        'fuel' => $dataCollection['fuel'],
                        'drive_info' => $dataCollection['drive_train'],
                        'mpg_city' => $dataCollection['city_mpg_data'],
                        'mpg_highway' => $dataCollection['hwy_mpg_data'],
                        'exterior_color' => $dataCollection['exterior_color'],
                        'interior_color' => $dataCollection['interior_color'],
                        'created_date' => $formattedDate,
                        'stock_date_formated' => $formattedDate,
                        'user_id' => $id,
                        'payment_price' => $monthlyPayment,
                        'body_formated' => $carBody,
                        'is_feature' => 0,
                        'status' => 1,
                        'batch_no' => $dataCollection['batch_no_data'],
                    ]);

                    // ✅ Get last inserted ID
                    $insertedMainInventoryId = $mainInventory->id;

                    // ✅ Insert into additional_inventories
                    AdditionalInventory::create([
                        'main_inventory_id' => $insertedMainInventoryId,
                        'detail_url' => $dataCollection['source_url'],
                        'img_from_url' => $dataCollection['all_images'],
                        'local_img_url' => $localImagePathsString,
                        'vehicle_feature_description' => $dataCollection['feature'],
                        'vehicle_additional_description' => $dataCollection['dealer_option'],
                        'seller_note' => $dataCollection['seller_note'],
                    ]);

                    // ✅ Check if price history data exists
                    if (!isset($dataCollection['price_history_data']) || empty($dataCollection['price_history_data'])) {
                        // Log::warning('⚠️ No price history data found.');
                        return;
                    }

                    $priceHistoryData = [];
                    $entries = explode(',', $dataCollection['price_history_data']);

                    foreach ($entries as $entry) {
                        $parts = array_map('trim', explode(';', $entry));

                        // 🚀 Skip invalid entries
                        if (count($parts) !== 3 || in_array('----', $parts)) {
                            // Log::warning("⚠️ Skipping invalid price history entry", ['entry' => $entry]);
                            continue;
                        }

                        // ✅ Parse Date
                        try {
                            $date = Carbon::createFromFormat('m/d/y', $parts[0])->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Log::error("❌ Invalid date format: " . $parts[0]);
                            continue;
                        }

                        // ✅ Parse Change Amount
                        $changeAmount = trim($parts[1]);
                        if (!isset($parts[2])) {
                            // Log::error('❌ Price history parsing error: missing amount in entry', ['entry' => $entry]);
                            continue;
                        }

                        $rawAmount = trim($parts[2]); // Use parts[2] instead of parts[3]
                        $cleanAmount = str_replace([',', '$', '+', '-'], '', $rawAmount);

                        if (!is_numeric($cleanAmount)) {
                            // Log::error('❌ Invalid amount value', ['value' => $rawAmount]);
                            continue;
                        }

                        $amount = floatval($cleanAmount);
                        // Log the parsed value to ensure it's correct
                        // Log::info('Parsed amount: ' . $amount); // This should help debug

                        // ✅ Determine status (1 = price changed, 0 = new listing)
                        // $status = (strpos($changeAmount, '+') !== false || strpos($changeAmount, '-') !== false) ? 1 : 0;

                        // ✅ Ensure `main_inventory_id` exists
                        if (!isset($insertedMainInventoryId) || empty($insertedMainInventoryId)) {
                            // Log::error('❌ Missing main_inventory_id for price history.');
                            return;
                        }

                        // ✅ Add to bulk insert array
                        $priceHistoryData[] = [
                            'main_inventory_id' => $insertedMainInventoryId,
                            'change_date' => $date,
                            'change_amount' => $changeAmount,
                            'amount' => $amount,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // ✅ Bulk Insert Price History
                    if (!empty($priceHistoryData)) {
                        DB::enableQueryLog();
                        MainPriceHistory::insert($priceHistoryData);
                        // Log::info('✅ Price history inserted successfully', ['query' => DB::getQueryLog()]);
                    } else {
                        // Log::warning('⚠️ No valid price history data to insert.');
                    }

            } catch (\Exception $e) {
                // ❌ Log the error
                Log::error('❌ Error inserting inventory: ' . $e->getMessage(), [
                    'data' => $row,
                    'error' => $e->getTraceAsString(),
                ]);

                return null; // Return null if insertion fails
            }
        }


        // private function insertInventory($row, $id, $dealer_id, $user_id, $dataCollection, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate,$monthlyPayment,$carBody)
        // {

        //         // dd($dealer_id, $user_id, $dataCollection, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate,$monthlyPayment,$carBody);
        //         try {
        //             // Insert into main_inventories
        //             $mainInventory = MainInventory::create([
        //                 // 'dealer_id' => $dealer_id,
        //                 'deal_id' => $user_id,
        //                 'zip_code' => $dataCollection['dealer_zip'],
        //                 'latitude' => $latlongData['latitude'],
        //                 'longitude' => $latlongData['longitude'],
        //                 'vehicle_make_id' => $vehicleMakeDataID,
        //                 'title' => $dataCollection['titles'],
        //                 'year' => $dataCollection['year'],
        //                 'make' => $dataCollection['make'],
        //                 'model' => $dataCollection['model'],
        //                 'vin' => $dataCollection['vin'],
        //                 'price' => $dataCollection['price'],
        //                 'price_rating' => $dataCollection['price_rating'],
        //                 'miles' => $dataCollection['milage'],
        //                 'type' => $dataCollection['type'],
        //                 'trim' => $dataCollection['trim'],
        //                 'stock' => $dataCollection['stock_number'],
        //                 'transmission' => $dataCollection['transmission'],
        //                 'engine_details' => $dataCollection['engine'],
        //                 'fuel' => $dataCollection['fuel'],
        //                 'drive_info' => $dataCollection['drive_info'],
        //                 'mpg_city' => $dataCollection['city_mpg_data'],
        //                 'mpg_highway' => $dataCollection['hwy_mpg_data'],
        //                 'exterior_color' => $dataCollection['exterior_color'],
        //                 'interior_color' => $dataCollection['interior_color'],
        //                 'created_date' => $formattedDate,
        //                 'stock_date_formated' => $formattedDate,
        //                 'user_id' => $id,
        //                 'payment_price' => $monthlyPayment,
        //                 'body_formated' => $carBody,
        //                 'is_feature' => 0,
        //                 'status' => 1,
        //                 // 'inventory_status' => $dataCollection['inventory_status'],
        //                 'batch_no' => $dataCollection['batch_no_data'],
        //             ]);

        //             // ✅ Get last inserted ID
        //             $insertedMainInventoryId = $mainInventory->id;
        //             $additiomalInventory = AdditionalInventory::create([

        //                 'main_inventory_id' => $insertedMainInventoryId,
        //                 'detail_url' => $dataCollection['source_url'],
        //                 'img_from_url' => $dataCollection['all_images'],
        //                 'local_img_url' => $localImagePathsString,
        //                 'vehicle_feature_description' => $dataCollection['feature'],
        //                 'vehicle_additional_description' => $dataCollection['dealer_option'],
        //                 'seller_note' => $dataCollection['seller_note'],
        //             ]);

        //             $entries = explode(',', $dataCollection['price_history_data']);
        //             $priceHistoryData = [];

        //             foreach ($entries as $entry) {
        //                 $parts = array_map('trim', explode(';', $entry));

        //                 // Skip invalid entries
        //                 if (in_array('----', $parts) || count($parts) !== 3) {
        //                     continue;
        //                 }

        //                 // Parse date
        //                 try {
        //                     $date = Carbon::createFromFormat('m/d/y', $parts[0])->format('Y-m-d');
        //                 } catch (\Exception $e) {
        //                     Log::error("Invalid date format: " . $parts[0]);
        //                     continue;
        //                 }

        //                 // Parse change_amount
        //                 $changeAmount = $parts[1]; // Store as VARCHAR directly

        //                 // Set status
        //                 $status = 0; // Default is 0 (e.g., "Listed")
        //                 if (strpos($parts[1], '+') !== false || strpos($parts[1], '-') !== false) {
        //                     $status = 1; // Change detected, set to 1
        //                 }
        //                 // Add to bulk insert array
        //                 $priceHistoryData[] = [
        //                     'main_inventory_id' => $inventoryId,
        //                     'change_date' => $date,
        //                     'change_amount' => $changeAmount, // VARCHAR as-is
        //                     'amount' => $amount, // DECIMAL(8,2)
        //                     'status' => $status, // Primarily 0 unless conditionally updated
        //                     'created_at' => now(),
        //                     'updated_at' => now(),
        //                 ];
        //             }

        //             // Bulk insert valid price history data
        //             if (!empty($priceHistoryData)) {
        //                 // PriceHistory::insert($priceHistoryData);
        //                 MainPriceHistory::insert($priceHistoryData);
        //             }

        //     } catch (\Exception $e) {
        //     // Log the error for debugging
        //     Log::error('Error inserting inventory: ' . $e->getMessage(), [
        //         'data' => $row,
        //         'error' => $e->getTraceAsString(),
        //     ]);

        //     // Return null or handle the error as per your application's requirement
        //     return null;
        //     }
        // }
}
