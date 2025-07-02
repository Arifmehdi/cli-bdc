<?php

namespace App\Console\Commands\CSVToDB;

use App\Models\User;
use App\Models\Latlongs;
use App\Models\LocationZip;
use App\Models\VehicleMake;
use App\Models\MainInventory;
use Illuminate\Support\Carbon;
use App\Models\CSVTmpInventory;
use Illuminate\Console\Command;
use App\Models\MainPriceHistory;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\AdditionalInventory;
use App\Models\LocationCity;
use App\Models\LocationState;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AddCSVDataToMainInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db-to-main-inventory {--limit=500}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        // $datas = DB::table('csv_tmp_inventories')->where('status',0)->limit($limit)->get();
        $datas = CSVTmpInventory::where('status', 0)->limit($limit)->get();

        // $inventory_sold = [];
        // $inventory_added = [];
        $csvVINsByDealer = [];

        // $tmp_imported = [];
        // $tmp_updated = [];
        // $tmp_skipped = [];

        $imported = [];
        $updated = [];
        $skipped = [];
        $vinMessages = [];
        foreach($datas as $data )
        {

            // dd($data);
            $dealer_name_data = $data->dealer_name;
            $vin_data = $data->vin;
            $dealer_rating = $data->dealer_rating != '----' ? $data->dealer_rating : null ;
            $dealer_review = $data->dealer_review != '----' ? $data->dealer_review : null ;
            $dealer_id_data = $data->dealer_id != '----' ? $data->dealer_id : null ;
            $dealerPhone = $data->dealer_sales_phone != '----' ? preg_replace('/\D/', '',$data->dealer_sales_phone) : null ;;
            $dealer_full_address_data = $data->dealer_address != '----' ? $data->dealer_address : null ;
            $dealer_address_data = $data->dealer_street != '----' ? $data->dealer_street : null ;
            $dealer_website = $data->dealer_website != '----' ? $data->dealer_website : null ;
            $brand_website = $data->brand_website != '----' ? $data->brand_website : null ;
            $city_info = $data->dealer_city != '----' ? $data->dealer_city : null ;
            $state_info = $data->dealer_region != '----' ? $data->dealer_region : null ;
            $zip_info =$data->dealer_zip_code != '----' ?$data->dealer_zip_code : null ;

            $year_data = $data->year != '----' ? $data->year : null ;
            $make_data = $data->make != '----' ? $data->make : null ;
            $model_data = $data->model != '----' ? $data->model : null ;
            $all_images_data = $data->all_image != '----' ? $data->all_image : null ;;
            $body_type_data = $data->body_type != '----' ? $data->body_type : null ;

            // $price_data = $data->price != '----' ? preg_replace('/\D/', '',$data->price) : 0 ;
            $price_data = $data->price != '----' ? (int) preg_replace('/\D/', '', $data->price) : 0;

            // $existingInventoryByDealer = MainInventory::select('deal_id', 'vin')
            // ->whereNotNull('vin')
            // ->get()
            // ->groupBy('deal_id')
            // ->map(fn($inventory) => $inventory->pluck('vin')->toArray());


            // $inventory = MainInventory::with('dealer')->select('*')->get();   //vin not mention yegt

            $latestBatchNo = MainInventory::latest('batch_no')->value('batch_no');
            $batch_no = $latestBatchNo ? $latestBatchNo + 1 : 101;

            $latlongData = $this->getLatLong($zip_info);
            $dealer = User::where('name', $dealer_name_data)->where('zip', $zip_info)->first();

            if ($dealer) {
                $dealerId = $dealer->dealer_id;
                if (!isset($csvVINsByDealer[$dealerId])) {
                    $csvVINsByDealer[$dealerId] = [];
                }
                $csvVINsByDealer[$dealerId][] = $vin_data;

                if ($dealer->rating == null && $dealer_rating) {
                    $dealer->rating = $dealer_rating;
                    $dealer->review = $dealer_review;
                    $dealer->save();
                }
                $dealer_id = $dealer->dealer_id;
                $user_id = $dealer->id;
            } else {

                $custom_dealer_id = $dealer_id_data;

                $user = new User();
                $user->dealer_id           = $custom_dealer_id;
                $user->name                = $dealer_name_data;
                $user->phone               = $dealerPhone;
                $user->dealer_full_address = $dealer_full_address_data;
                $user->rating              = $dealer_rating;
                $user->review              = $dealer_review;
                $user->address             = $dealer_address_data;
                $user->dealer_website      = $dealer_website;
                $user->brand_website       = $brand_website;
                $user->city                = $city_info;
                $user->state               = $state_info;
                $user->zip                 = $zip_info;
                $user->status              = 3;
                $user->dealer_iframe_map   =  null;
                $user->save();

                $user_id = $user->id;
                $dealer_id = $user->dealer_id;

                $role_data = 'dealer';
                $role =  Role::where('name', $role_data)->first();

                // $role = Role::find(2);
                if (!$role) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "The specified role does not exist or is not for the 'web' guard.",
                    ], 422);
                }
                $user->assignRole($role);
            }

            // $errors = [];
            $saveDir = public_path('listing/' . $vin_data);
            $this->ensureDirectoryExists($saveDir);
            // dd('palestoine',$latlongData);

            $fileNameCustom = str_replace(' ','-',strtolower($year_data . '_' . $make_data . '_' . $model_data . '-pic-') . $vin_data);
            $localImagePaths = $this->processImages($all_images_data, $saveDir, $vin_data, $fileNameCustom);
            $localImagePathsString = implode(',', $localImagePaths);

            $formattedDate = now()->format('Y-m-d');
            $monthlyPayment = $this->calculateMonthlyPayment($price_data);
            $carBody = $this->determineCarBody(strtolower($body_type_data));

            $data->status = 1;
            $data->save();

            $vehicleMakeData = VehicleMake::firstOrCreate(
                ['make_name' => $make_data], // Search criteria
                ['status' => 1, 'is_read' => 0] // Default values if a new record is created
            );

            $vehicleMakeDataID = $vehicleMakeData->id;

            // there have some logic you can recheck on InventoryImportController  storeCSVInventory method
            $existingInventory = MainInventory::where('vin', $vin_data)->first();
            if(Auth::check()) {
                $userId = Auth::user()->id;
            } else{
                $userId = User::where('name','Super Admin')->first()->id;
            }

            // $filePath, $dataCollection, $row
            if ($existingInventory) {
                // Update logic here
                // $this->insertOrUpdateInventory($row, $userId, $dealer_id, $user_id, $dataCollection, $filePath, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody);
                $this->insertOrUpdateInventory($userId, $dealer_id, $user_id,  $data, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody);
                $updated[] = $vin_data;
                $vinMessages[] = "Updated: {$vin_data}";
            } else {
                // Insert logic here
                // $this->insertOrUpdateInventory($row, $userId, $dealer_id, $user_id, $dataCollection, $filePath, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody);
                $this->insertOrUpdateInventory($userId, $dealer_id, $user_id,  $data, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody);
                $imported[] = $vin_data;
                $vinMessages[] = "Inserted: {$vin_data}";
            }
        }

            // Display all individual VIN messages
            foreach ($vinMessages as $message) {
                $this->info($message);
            }

            // Prepare and display summary after processing all rows
            $summary = [
                'imported' => count($imported),
                'updated'  => count($updated),
                'skipped'  => count($skipped),
            ];
            $this->info('Summary: ' . json_encode($summary, JSON_PRETTY_PRINT));
    }

    private function getLatLong($zipCode)
    {

        // $latlongData = Latlongs::where('zip_code', $zipCode)->select('zip_code', 'latitude', 'longitude')->first();
        // $latlongData = LocationZip::where('zip_code', $zipCode)->pluck('latitude', 'longitude')->first();  // use pluck here
        $latlongData = DB::table('location_zips')->where('zip_code', $zipCode)->select('zip_code','latitude', 'longitude')->first();  // use pluck here

        if ($latlongData) {
            // Return the existing data
            return [
                'zip_code' => $latlongData->zip_code,
                'latitude' => $latlongData->latitude,
                'longitude' => $latlongData->longitude
            ];
        }

        $apiKey = '4b84ff4ad9a74c79ad4a1a945a4e5be1';
        $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},us&key={$apiKey}";

        $response = Http::get($url);
        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['results'][0]['geometry'])) {
                $geometry = $data['results'][0]['geometry'];

                $state_info = $data['results'][0]['components']['state'];
                $short_name_info = $data['results'][0]['components']['state_code'];
                $county_info = $data['results'][0]['components']['county'];

                $location_state_data = LocationState::firstOrCreate(
                    ['state_name' => $state_info], // Check if a record exists with the given state name
                    ['short_name' => $short_name_info] // Default value for short_name if the record is created
                );
                $location_state_id = $location_state_data->id;

                $location_cities_data = LocationCity::firstOrCreate(
                    ['location_state_id' => $location_state_id],
                    ['city_name' => $state_info],
                    ['latitude' => $geometry['lat']],
                    ['longitude' => $geometry['lng']]
                );
                $location_cities_id = $location_cities_data->id;

                $location_zips_data = LocationZip::firstOrCreate(
                    ['location_city_id' => $location_cities_id],
                    ['county' => $county_info],
                    ['latitude' => $geometry['lat']],
                    ['longitude' => $geometry['lng']] ,
                    ['zip_code' => $zipCode],
                    ['sales_tax' => 8],
                    ['url' => $url],
                );

                // Return the newly created data
                return [
                    'zip_code' => $location_zips_data->zip_code,
                    'latitude' => $location_zips_data->latitude,
                    'longitude' => $location_zips_data->longitude
                ];
            }
        }
        // Log a warning if no results were found and return null values
        // Log::warning("No results found for ZIP code $zipCode");
        return [
            'zip_code' => $zipCode,
            'latitude' => null,
            'longitude' => null
        ];
    }

    private function ensureDirectoryExists($directory)
    {
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
        }
    }

    private function processImages($imageString, $saveDir, $vin)
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

    private function calculateMonthlyPayment($price)
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
            Log::error("Error calculating monthly payment: " . $th->getMessage());
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


    // public function insertOrUpdateInventory(array $row, int $id, $dealer_id, int $user_id, $dataCollection, $filePath, $latlongData, $localImagePaths, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody)
    public function insertOrUpdateInventory(int $id, $dealer_id, int $user_id, $dataCollection , $latlongData,  $localImagePaths, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody)
    {

        $price = preg_replace('/\D/', '',$dataCollection->price);
        $price_history = $dataCollection->price_history != '----' ? $dataCollection->price_history :  null;
        $price_rating = $dataCollection->price_rating != '----' ? $dataCollection->price_rating :  null;
        $mileage = $dataCollection->mileage != '----' ? preg_replace('/\D/', '',$dataCollection->mileage) :  null;

        try {
            // Insert into tmp_inventories
            // $mainInventory = MainInventory::create([
            $mainInventory = MainInventory::updateOrCreate(
                ['vin' => $dataCollection->vin],
                [
                    // 'dealer_id' => $dealer_id,
                    'deal_id' => $user_id,
                    'zip_code' => $dataCollection->dealer_zip_code,
                    'latitude' => $latlongData['latitude'],
                    'longitude' => $latlongData['longitude'],
                    'vehicle_make_id' => $vehicleMakeDataID,
                    'year' => $dataCollection->year,
                    'title' => $dataCollection->titles,
                    'make' => $dataCollection->make,
                    'model' => $dataCollection->model,
                    'vin' => $dataCollection->vin,
                    'price' => $price,
                    'price_rating' => $price_rating,
                    'miles' => $mileage,
                    'type' => $dataCollection->type,
                    'trim' => $dataCollection->trim_name,
                    'stock' => $dataCollection->stock_number,
                    'transmission' => $dataCollection->transmission,
                    'engine_details' => $dataCollection->engine,
                    'fuel' => $dataCollection->fuel,
                    'drive_info' => $dataCollection->drive_train,
                    'mpg' => null,
                    'mpg_city' => $dataCollection->city_mpg,
                    'mpg_highway' => $dataCollection->hwy_mpg,
                    'exterior_color' => $dataCollection->exterior_color,
                    'interior_color' => $dataCollection->interior_color,
                    'created_date' => $formattedDate,
                    'stock_date_formated' => $formattedDate,
                    'user_id' => $id,
                    'payment_price' => $monthlyPayment,
                    'body_formated' => $carBody,
                    'is_feature' => 0,
                    'batch_no' => $dataCollection->batch_no,
                    'status' => 1,

                    // // 'detail_url' => $dataCollection['source_url'],
                    // 'img_from_url' => $dataCollection['all_images'],
                    // 'local_img_url' => $localImagePaths,
                    // 'vehicle_feature_description' => $dataCollection['feature'],
                    // 'vehicle_additional_description' => $dataCollection['dealer_option'],
                    // 'seller_note' => $dataCollection['seller_note'],
                    // 'price_history' => $dataCollection['price_history_data'],
                    // 'inventory_status' => $dataCollection['inventory_status'],
                ]
            );

            $mainInventoryId = $mainInventory->id;

            // $additionalInventory = AdditionalInventory::create([
            AdditionalInventory::updateOrCreate(
                ['main_inventory_id' => $mainInventoryId], // Ensure it's linked correctly
                [
                    // 'dealer_id' => $dealer_id,
                    'main_inventory_id' => $mainInventoryId,
                    'detail_url' => $dataCollection->source_url,
                    'img_from_url' => $dataCollection->all_image,
                    'local_img_url' => $localImagePaths,
                    'vehicle_feature_description' => $dataCollection->feature,
                    'vehicle_additional_description' => $dataCollection->options,
                    'seller_note' => $dataCollection->seller_note,
                ]
            );

            if (isset($price_history) && $price_history != null) {
                $priceHistoryData = [];
                $entries = explode(',', $price_history);

                foreach ($entries as $entry) {
                    $parts = array_map('trim', explode(';', $entry));

                    // Skip invalid entries
                    if (count($parts) !== 3 || in_array('----', $parts)) {
                        continue;
                    }

                    // Parse Date
                    try {
                        $date = Carbon::createFromFormat('m/d/y', $parts[0])->format('Y-m-d');
                    } catch (\Exception $e) {
                        continue;
                    }

                    // Parse Change Amount
                    $changeAmount = trim($parts[1]);
                    if (!isset($parts[2])) {
                        continue;
                    }

                    $rawAmount = trim($parts[2]);
                    $cleanAmount = str_replace([',', '$', '+', '-'], '', $rawAmount);

                    if (!is_numeric($cleanAmount)) {
                        continue;
                    }

                    // Calculate amount before adding to the array
                    $amount = floatval($cleanAmount);

                    // Add to bulk insert array
                    $priceHistoryData[] = [
                        'main_inventory_id' => $mainInventoryId,
                        'change_date'       => $date,
                        'change_amount'     => $changeAmount,
                        'amount'            => $amount,
                        'status'            => 1,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];

                }

                // Bulk Insert Price History if there is data
                if (!empty($priceHistoryData)) {
                    MainPriceHistory::insert($priceHistoryData);
                }
            }


        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error inserting tmp inventory: ' . $e->getMessage(), [
                'data' => $dataCollection,
                'error' => $e->getTraceAsString(),
            ]);

            // Return null or handle the error as per your application's requirement
            return null;
        }
    }


}
