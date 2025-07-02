<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Latlongs;
use App\Models\Inventory;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use App\Models\TmpInventories;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class InventoryImportController extends Controller
{
    public function index()
    {
        $users = User::whereNotNull('name')->get();
        $authUser = Auth::user();
        if($authUser->hasAllaccess())
        {
            $inventories = Inventory::orderBy('id', 'desc')->paginate(12);
        }else
        {
            $inventories = Inventory::where('deal_id',$authUser->id)->orderBy('id', 'desc')->paginate(12);

        }
            return view('backend.admin.import.inventory_import', compact('users', 'inventories'));
        }

    public function storeInventory(Request $request)
    {
        //// abort_if(! auth()->user()->can('hrm_bulk_attendance_import_store'), 403, 'Access forbidden');
        $request->validate([
            'user' => 'required',
            'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
        ], [
            'user.required' => 'User field is required',
            'import_file.required' => 'Import field is required',
            'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
        ]);

        // ***********carguru data store start here
        $originalName = $request->file('import_file')->getClientOriginalName();
        // $parts = explode('_', $originalName);
        // $namePart = $parts[0];

        // if($namePart == 'carguru'){
        //     $dataInform = $this->storeInventoryForCarguru($request);
        //     return 'ok done';
        // }
        //********** */ carguru data store end here

        $userId = $request->user;
        $directory = public_path('uploads/import');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $originalFileName = $request->file('import_file')->getClientOriginalName();
        $modifiedFileName = $userId.'_'.date('mdY') . '_' . $originalFileName;
        $request->file('import_file')->move($directory, $modifiedFileName);

        return response()->json([
            'status' => 'success',
            'message' => "Non-Server CSV Stored Successfully",
        ], 200);
        // move file in local path set return here

        // $filePath = $directory . '/' . $modifiedFileName;
        // $file_rows = Excel::toArray([], $filePath)[0];

        // $existingInventoryByDealer = Inventory::select('dealer_id', 'vin')
        //                                         ->whereNotNull('vin')
        //                                         ->get()
        //                                         ->groupBy('dealer_id')
        //                                         ->map(fn ($inventory) => $inventory->pluck('vin')->toArray());

        // // dd($existingInventoryByDealer);

        // $inventory_sold = [];
        // $inventory_added = [];

        // $csvVINsByDealer = [];

        // $inventory = Inventory::with('dealer')->select('*')->get();   //vin not mention yegt

        // if (!empty($file_rows)) {
        //     foreach (array_slice($file_rows, 1) as $row) {
        //         $dealer = User::where('phone', $row[2])->first();
        //         if ($dealer) {
        //             $dealerId = $dealer->dealer_id;
        //             $csvVINsByDealer[$dealerId][] = $row[16];
        //         }


        //         $inventoryItemFound = false;
        //         $newInventoryItemFound = true;

        //         foreach ($inventory as $inventoryDetails) {
        //             // Check if VIN matches
        //             if ($inventoryDetails['vin'] == $row[16]) {
        //                 $inventoryItemFound = true;
        //             }

        //             // Check if stock matches
        //             if ($inventoryDetails['vin'] == $row[16]) {
        //                 $newInventoryItemFound = false;
        //             }

        //             // If both conditions are determined, break the loop
        //             if ($inventoryItemFound && !$newInventoryItemFound) {
        //                 break;
        //             }
        //         }

        //         // Take action based on whether inventoryItemFound and newInventoryItemFound
        //         if (!$inventoryItemFound) {
        //             // dd($inventoryDetails);
        //             // Inventory::where('vin', $inventoryDetails['vin'])->update(['status' => 0]);
        //         }

        //         if ($newInventoryItemFound) {
        //             $inventory_added[] = $row[22];

        //             $dealer = User::where('phone', $row[2])->first();
        //             if ($dealer) {
        //                 $dealer_id = $dealer->dealer_id;
        //                 $user_id = $dealer->id;
        //             } else {
        //                 $custom_dealer_id = $row[0];

        //                 if (!$dealer && !empty($row[2])) {
        //                     $user = new User();
        //                     $user->dealer_id        = $custom_dealer_id;
        //                     $user->name             = $row[1];
        //                     $user->phone            = $row[2];
        //                     $user->address          = $row[4];
        //                     $user->city             = $row[5];
        //                     $user->state            = $row[6];
        //                     $user->dealer_iframe_map = $row[7];
        //                     $user->save();

        //                     $user_id = $user->id;
        //                     $dealer_id = $user->dealer_id;

        //                     $role = 'dealer';
        //                     $role =  Role::where('name', $role)->first();
        //                     if (!$role) {
        //                         return response()->json([
        //                             'status' => 'error',
        //                             'message' => "The specified role does not exist or is not for the 'web' guard.",
        //                         ], 422);
        //                     }
        //                     $user->assignRole($role);
        //                 }
        //             }
        //             $this->createTmpInventory($row, $userId, $dealer_id, $user_id);

        //         }
        //     }
        // }
        // foreach ($existingInventoryByDealer as $dealerId => $vinList) {
        //     if (isset($csvVINsByDealer[$dealerId])) {
        //         $missingVINs = array_diff($vinList, $csvVINsByDealer[$dealerId]);

        //         foreach ($missingVINs as $vin) {
        //             Inventory::where('dealer_id', $dealerId)
        //                 ->where('vin', $vin)
        //                 ->update(['status' => 0]);

        //             $inventory_sold[] = $vin;
        //         }
        //     }
        // }

        // $totalAdded = count($inventory_added);
        // $totalSold = count($inventory_sold);
        // return response()->json(['add' => $inventory_added, 'total_add' => $totalAdded, 'sold' => $inventory_sold, 'total_sold' => $totalSold]);
    }

    public function storeCSVInventory(Request $request)
    {
        //// abort_if(! auth()->user()->can('hrm_bulk_attendance_import_store'), 403, 'Access forbidden');
        $request->validate([
            'csvFileName' => 'required',
        ], [
            'csvFileName.required' => 'CSV File is required',
        ]);


        // move file in local path set return here
        $modifiedFileName = $request->csvFileName;
        $directory = public_path('uploads/import');
        $filePath = $directory . '/' . $modifiedFileName;

        $fileRows = Excel::toArray([], $filePath)[0];


        if (empty($fileRows)) {
            return response()->json(['status' => 'error', 'message' => 'CSV file is empty'], 422);
        }


        $headers = array_map('strtolower', array_map('trim', $fileRows[0]));

        $file_rows = Excel::toArray([], $filePath)[0];

        $existingInventoryByDealer = Inventory::select('dealer_id', 'vin')
                                                ->whereNotNull('vin')
                                                ->get()
                                                ->groupBy('dealer_id')
                                                ->map(fn ($inventory) => $inventory->pluck('vin')->toArray());

        // dd($existingInventoryByDealer);
        $inventory_sold = [];
        $inventory_added = [];
        $csvVINsByDealer = [];

        $inventory = Inventory::with('dealer')->select('*')->get();   //vin not mention yegt

        // $headers = array_map('strtolower', array_map('trim', $file_rows[0]));
        if (!empty($file_rows)) {
            foreach (array_slice($file_rows, 1) as $index => $row) {
                $rowData = array_combine($headers, $row);
                // dd($rowData);
                $index = $index + 1;
                // Example: Access data by header names
                $dealer_id_data = $rowData['dealer id'] ?? 'AT-'. 1000 +$index ;
                $dealer_no_data = $rowData['dealer no'] ?? null;
                $dealer_name_data = $rowData['dealer name'] ?? null;
                $dealer_address_data = $rowData['dealer address'] ?? null;

                if ($dealer_address_data) {
                    // Split the address into parts by commas
                    $dealer_address_explode_data = explode(',', $dealer_address_data);

                    // Ensure the second part exists before accessing it
                    if (isset($dealer_address_explode_data[1])) {
                        // Extract the last word (city) from the second part
                        $lastSpacePosition = strrpos($dealer_address_explode_data[0], ' '); // Find the last space
                        $city_info = trim(substr($dealer_address_explode_data[0], $lastSpacePosition + 1));
                        // Split the second part by spaces for state and zip
                        $dealer_city_explode_data = explode(' ', trim($dealer_address_explode_data[1]));

                        $state_info = $dealer_city_explode_data[0] ?? null; // State
                        $zip_info = $dealer_city_explode_data[1] ?? null;   // Zip
                    } else {
                        $city_info = $state_info = $zip_info = null; // Default if second part doesn't exist
                    }
                } else {
                    $city_info = $state_info = $zip_info = null; // Default if address is null
                }

                // // Debugging Output
                // dd([
                //     'city' => $city_info,
                //     'state' => $state_info,
                //     'zip' => $zip_info,
                // ]);

                // dd($city_info,$state_info,$zip_info,$dealer_city_explode_data[0],$dealer_city_explode_data[1], $dealer_address_explode_data);
                $phone_digitsOnly = preg_replace('/\D/', '', $rowData['dealer sales phone']);

                $dealer_city_data = $rowData['dealer city'] ?? $city_info ??  null;
                $dealer_region_data = $rowData['dealer region'] ?? $state_info ??  null;
                $dealer_zip_data = $rowData['dealer zip code'] ?? $zip_info ?? null;
                $dealer_phone_info = $rowData['phone'] ?? null;
                $dealerPhone = $rowData['phon sms'] ?? $phone_digitsOnly ??  null;
                $dealer_rating= $rowData['dealer rating'] ?? null;
                $dealer_review = $rowData['dealer review'] ?? null;
                $source_url = $rowData['source_url'] ?? null;
                $titles_data = $rowData['titles'] ?? null;
                $trim_data = $rowData['trim name'] ?? null;
                $make_data = $rowData['make'] ?? null;
                $model_data = $rowData['model'] ?? null;
                $exterior_color_data = $rowData['exterior color'] ?? null;
                $interior_color_data = $rowData['interior color'] ?? null;

                // Price
                $price_data = $rowData['price'] ?? 0;
                $price_digitsOnly = preg_replace('/\D/', '', $price_data); // Removes non-digit characters

                // Mileage
                $mileage_data = $rowData['mileage'] ?? 0; // Use 'mileage' consistently
                $cus_mileage = preg_replace('/\D/', '', $mileage_data); // Removes non-digit characters
                // dd($price_data, $price_digitsOnly, $mileage_data, $cus_mileage);
                $fuel_data = $rowData['fuel'] ?? null;
                $avg_mpg_data = $rowData['avg_mpg'] ?? null;
                $engine_data = $rowData['engine'] ?? null;
                $transmission_data = $rowData['transmission'] ?? null;
                $year_data = $rowData['year'] ?? null;
                $type_data = $rowData['type'] ?? null;
                $stock_num_data = $rowData['stock number'] ?? null;
                $vin = $rowData['vin'] ?? null;
                $body_type_data = $rowData['body type'] ?? null;
                $dealer_option_info = $rowData['option'] ?? null;
                $drive_train_data = $rowData['drive train'] ?? null;
                $primary_image_data = $rowData['primary image'] ?? $rowData['all image'] ?? null;
                $all_images_data = $rowData['all images'] ?? $rowData['all image'] ?? null;
                $salesstatus_data = $rowData['salesstatus'] ?? null;
                $days_on_market_data = $rowData['days on market'] ?? null;
                $deal_rating_data = $rowData['deal rating'] ?? null;

                // dd($days_on_market_data,$deal_rating_data,$primary_image_data,$all_images_data,$drive_train_data);
                // dd($fuel_data,$avg_mpg_data,$engine_data,$transmission_data,$year_data,$type_data,$salesstatus_data,$stock_num_data,$body_type_data,$drive_train_data);
                // dd($vin,$source_url,$titles_data,$trim_data,$make_data,$model_data,$exterior_color_data,$interior_color_data,$price_data,$milage_data);
                // dd($dealer_id_data,$dealer_no_data,$dealer_name_data,$dealer_city_data,$dealer_region_data,$dealer_zip_data,$dealer_phone_onfo,$dealerPhone,$dealer_rating,$dealer_review);
                $dataCollection = collect([
                    'dealer_id' => $dealer_id_data ?? null,
                    // 'dealer_no' => $rowData['dealer no'] ?? null,
                    'dealer_name' => $dealer_name_data ?? null,
                    'dealer_address' => $dealer_address_data ?? null,
                    'dealer_city' => $dealer_city_data ?? null,
                    'dealer_region' => $dealer_region_data ?? null,
                    'dealer_zip' => $dealer_zip_data ?? null,
                    'dealer_phone' => $rowData['phone'] ?? null,
                    'dealer_phone_sms' => $dealerPhone ?? null,
                    'dealer_rating' => $dealer_rating ?? null,
                    'dealer_review' => $dealer_review ?? null,
                    'dealer_option' => $dealer_option_info ?? null,
                    'vin' => $vin ?? null,
                    'source_url' => $source_url ?? null,
                    'titles' => $titles_data ?? null,
                    'trim' => $trim_data ?? null,
                    'make' => $make_data ?? null,
                    'model' => $model_data ?? null,
                    'exterior_color' => $exterior_color_data ?? null,
                    'interior_color' => $interior_color_data ?? null,
                    'price' => isset($price_digitsOnly) ? (int)$price_digitsOnly : 0,
                    'milage' => isset($cus_mileage) ? (int)$cus_mileage : 0,
                    'fuel' => $fuel_data ?? null,
                    'avg_mpg' => $avg_mpg_data ?? null,
                    'engine' => $engine_data ?? null,
                    'transmission' => $transmission_data ?? null,
                    'year' => $year_data ?? null,
                    'type' => $type_data ?? null,
                    'sales_status' => $rowData['salesstatus'] ?? null,
                    'stock_number' => $stock_num_data ?? null,
                    'body_type' => $body_type_data ?? null,
                    'drive_train' => $drive_train_data ?? null,
                    'days_on_market' => $rowData['days on market'] ?? null,
                    'deal_rating' => $dealer_rating ?? null,
                    'primary_image' => $primary_image_data ?? null,
                    'all_images' => $all_images_data ?? null,
                ]);
                // $dataCollection = collect([
                //     'dealer_id' => $rowData['dealer id'] ?? null,
                //     'dealer_no' => $rowData['dealer no'] ?? null,
                //     'dealer_name' => $rowData['dealer name'] ?? null,
                //     'dealer_city' => $rowData['dealer city'] ?? null,
                //     'dealer_region' => $rowData['dealer region'] ?? null,
                //     'dealer_zip' => $rowData['dealer zip code'] ?? null,
                //     'dealer_phone' => $rowData['phone'] ?? null,
                //     'dealer_phone_sms' => $rowData['phon sms'] ?? null,
                //     'dealer_rating' => $rowData['dealer rating'] ?? null,
                //     'dealer_review' => $rowData['dealer review'] ?? null,
                //     'dealer_option' => $rowData['option'] ?? null,
                //     'vin' => $rowData['vin'] ?? null,
                //     'source_url' => $rowData['source_url'] ?? null,
                //     'titles' => $rowData['titles'] ?? null,
                //     'trim' => $rowData['trim name'] ?? null,
                //     'make' => $rowData['make'] ?? null,
                //     'model' => $rowData['model'] ?? null,
                //     'exterior_color' => $rowData['exterior color'] ?? null,
                //     'interior_color' => $rowData['interior color'] ?? null,
                //     'price' => $rowData['price'] ?? null,
                //     'milage' => $rowData['milage'] ?? null,
                //     'fuel' => $rowData['fuel'] ?? null,
                //     'avg_mpg' => $rowData['avg_mpg'] ?? null,
                //     'engine' => $rowData['engine'] ?? null,
                //     'transmission' => $rowData['transmission'] ?? null,
                //     'year' => $rowData['year'] ?? null,
                //     'type' => $rowData['type'] ?? null,
                //     'sales_status' => $rowData['salesstatus'] ?? null,
                //     'stock_number' => $rowData['stock number'] ?? null,
                //     'body_type' => $rowData['body type'] ?? null,
                //     'drive_train' => $rowData['drive train'] ?? null,
                //     'days_on_market' => $rowData['days on market'] ?? null,
                //     'deal_rating' => $rowData['deal rating'] ?? null,
                //     'primary_image' => $rowData['primary image'] ?? null,
                //     'all_images' => $rowData['all images'] ?? null,
                // ]);


                $dealer = User::where('phone', $dealerPhone)->first();

                if ($dealer) {
                    $dealerId = $dealer->dealer_id;
                    $csvVINsByDealer[$dealerId][] = $vin;
                }

                $inventoryItemFound = false;
                $newInventoryItemFound = true;
// djkhg ghudgh uidgu uguig uieygue rtuyeurtyuery ueryuery uty ereuryueryyttyuertuet
                // foreach ($inventory as $inventoryDetails) {
                //     // Check if VIN matches
                //     if ($inventoryDetails['vin'] == $vin) {
                //         $inventoryItemFound = true;
                //     }

                //     // Check if stock matches
                //     if ($inventoryDetails['stock'] == $stock_num_data) {
                //         $newInventoryItemFound = false;
                //     }

                //     // If both conditions are determined, break the loop
                //     if ($inventoryItemFound && !$newInventoryItemFound) {
                //         break;
                //     }
                // }

                // Take action based on whether inventoryItemFound and newInventoryItemFound
                if (!$inventoryItemFound) {
                    // dd($inventoryDetails);
                    // Inventory::where('vin', $inventoryDetails['vin'])->update(['status' => 0]);
                }
//   spofjiowe iwriweiuiwu ri wuriu weirw iwer wieriwruwit wyrwyr wuryweuruwertuweru wrtuweyrt wuryweuirwuirw rweuy
                if ($newInventoryItemFound) {
                    $inventory_added[] = $vin;

                    $dealer = User::where('phone', $dealerPhone)->first();


                    if ($dealer) {
                        $dealer_id = $dealer->dealer_id;
                        $user_id = $dealer->id;
                    } else {
                        $custom_dealer_id = $dealer_id_data;


                        if (!$dealer && !empty($dealerPhone)) {
                            $user = new User();
                            $user->dealer_id        = $custom_dealer_id;
                            $user->name             = $dealer_name_data;
                            $user->phone            = $dealerPhone;
                            $user->address          = $dealer_address_data;
                            $user->city             = $dealer_city_data;
                            $user->state            = $dealer_region_data;
                            $user->dealer_iframe_map = $row[7] ?? null;
                            $user->save();

                            $user_id = $user->id;
                            $dealer_id = $user->dealer_id;

                            $role = 'dealer';
                            $role =  Role::where('name', $role)->first();
                            if (!$role) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => "The specified role does not exist or is not for the 'web' guard.",
                                ], 422);
                            }
                            $user->assignRole($role);
                        }
                    }
                    $userId = (int) substr(explode('_', $request->csvFileName)[0], 0, 1);
                    $dato = $this->createTmpInventory($row, $userId, $dealer_id, $user_id, $dataCollection);
                    // dd($dato);
                }
            }
        }
        foreach ($existingInventoryByDealer as $dealerId => $vinList) {
            if (isset($csvVINsByDealer[$dealerId])) {
                $missingVINs = array_diff($vinList, $csvVINsByDealer[$dealerId]);

                foreach ($missingVINs as $vin) {
                    Inventory::where('dealer_id', $dealerId)
                        ->where('vin', $vin)
                        ->update(['status' => 0]);

                    $inventory_sold[] = $vin;
                }
            }
        }

        $totalAdded = count($inventory_added);
        $totalSold = count($inventory_sold);
        return response()->json(['add' => $inventory_added, 'total_add' => $totalAdded, 'sold' => $inventory_sold, 'total_sold' => $totalSold]);
    }

    private function createTmpInventory(array $row, int $id, $dealer_id, int $user_id, $dataCollection )
    {
        // dd($dataCollection['price'], $dataCollection);
        if (empty($dataCollection['all_images'])) {
            throw new \Exception('No images found in dataCollection[\'all_images\']');
        }

        $localImagePaths = [];
        $saveDir = public_path('frontend/uploads/autotrader/'.$dataCollection['vin']);

        // Ensure the directory exists
        if (!File::exists($saveDir)) {
            File::makeDirectory($saveDir, 0777, true, true);
        }

        // Process image URLs
        $imageString = $dataCollection['all_images'];
        $imageUrls = explode(',', $imageString);
        $imageUrls = array_slice($imageUrls, 0, 5);

        foreach ($imageUrls as $url) {
            $fileName = basename($url);
            $localPath = $saveDir . '/' . $fileName;

            // Skip download if the file already exists
            if (File::exists($localPath)) {
                $localImagePaths[] = asset('frontend/uploads/autotrader/' .$dataCollection['vin']. $fileName);
                continue;
            }

            try {
                // Download the image
                $response = Http::get(trim($url)); // Trim to avoid extra spaces
                if ($response->successful()) {
                    File::put($localPath, $response->body());
                    $localImagePaths[] = asset('frontend/uploads/autotrader/' .$dataCollection['vin']. $fileName);
                } else {
                    Log::warning("Failed to download image: $url");
                }
            } catch (\Exception $e) {
                Log::error("Error downloading image $url: " . $e->getMessage());
            }
            sleep(rand(4, 6));
        }

        // Convert localImagePaths array to a string
        $localImagePathsString = implode(',', $localImagePaths);

        // dd($localImagePaths);
        $errors = [];
        // $formattedDate = date('Y-m-d', strtotime($row[32])) ?? now();
        $formattedDate = now()->format('Y-m-d');

        $monthly_payment = 0;
        $price = (int)$dataCollection['price']; // Example price
        $sales_tax_percentage = 8 / 100; // Sales tax rate
        $interest_rate = 9 / 100; // Annual interest rate (APR)
        $loan_term_months = 72; // Loan term in months

        try {
            // Calculate sales tax
            $sales_tax = $price * $sales_tax_percentage;
            // Calculate total loan amount
            $loan_amount = $price + $sales_tax;
            // Monthly interest rate
            $monthly_interest_rate = $interest_rate / 12;
            // Calculate monthly payment
            $monthly_payment = ceil(
                ($loan_amount * $monthly_interest_rate) /
                (1 - pow(1 + $monthly_interest_rate, -$loan_term_months))
            );

            // echo "Monthly Payment: $" . $monthly_payment . PHP_EOL;
        } catch (\Throwable $th) {
            echo "Error in calculation: " . $th->getMessage();
        }

        $lowerString = strtolower($dataCollection['body_type']);
        $car_body = $this->determineCarBody($lowerString);

        $vehicleMakeData = VehicleMake::where('make_name', $dataCollection['make'])->first();
        $inventory = Inventory::where('vin',$dataCollection['vin'])->first();
        $tmp_inventory = TmpInventories::where('vin',$dataCollection['vin'])->first();

        if(!$inventory){
            if (!$vehicleMakeData) {
                $inventory_make_data = VehicleMake::create([
                    'make_name' => $dataCollection['make'],
                    // 'status' => 1,
                ]);
                $makeId = $inventory_make_data->id;
            } else {
                $makeId = $vehicleMakeData->id;
            }
            // dd($row, $id, $dealer_id, $user_id, $formattedDate, $monthly_payment,$row[17], $row[35], $car_body, $vehicleMakeData, $makeId, 'love you');
            $zipCode =  $dataCollection['dealer_zip'];
            $latlong_data = Latlongs::where('zip_code', $zipCode)->first();

            if(!$latlong_data){
                $api_key = '4b84ff4ad9a74c79ad4a1a945a4e5be1';
                $country_code = 'us';
                $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$country_code}&key={$api_key}";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $response = curl_exec($ch);

                // Check for cURL errors
                if(curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    curl_close($ch);
                    return null;
                }

                // Close cURL session
                curl_close($ch);

                // Decode the JSON response
                $data = json_decode($response, true);

                if (isset($data['results']) && count($data['results']) > 0) {
                    // Extract latitude, longitude, and city name
                    $geometry = $data['results'][0]['geometry'];
                    $latitude = $geometry['lat'];
                    $longitude = $geometry['lng'];
                    $city_name = isset($data['results'][0]['components']['city']) ? $data['results'][0]['components']['city'] : '';

                    $latlong_data = new Latlongs();
                    $latlong_data->zip_code = $zipCode;
                    $latlong_data->latitude = $latitude;
                    $latlong_data->longitude = $longitude;
                    $latlong_data->save();

                    // Return the latitude, longitude, and city name
                } else {
                    echo "No results found for this ZIP code.";
                }

            }else{
                $latitude = $latlong_data->latitude;
                $longitude = $latlong_data->longitude;
            }

            // $output = $row[18];
            // preg_match('/\d+,\d+/', $output, $matches);
            // ($matches != []) ? $numericValue = (int)str_replace(',', '', $matches[0]) : $numericValue = 0;



            if (!$dealer_id) {
                $dealer_id = $dataCollection['dealer_id'];  // Default value if dealer_id is not set
            }
            $clear_miles = trim(str_replace('mi', '', $dataCollection['milage']));
            if($clear_miles == 'N/A'){
                $clear_miles = 0;
            }
            try {
                // DB::beginTransaction();

                // Define the common data
                $inventoryData = [
                    'dealer_id' => $dealer_id,
                    'deal_id' => $user_id,
                    'zip_code' => $dataCollection['dealer_zip'],
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'detail_url' => $dataCollection['source_url'],
                    'img_from_url' => $dataCollection['primary_image'],
                    'local_img_url' => $localImagePathsString,
                    'vehicle_make_id' => $makeId,
                    'title' => $dataCollection['titles'],
                    'year' => $dataCollection['year'],
                    'make' => $dataCollection['make'],
                    'model' => $dataCollection['model'],
                    'vin' => $dataCollection['vin'],
                    'price' => $price,
                    'miles' => $clear_miles,
                    'type' => $dataCollection['type'],
                    'trim' => $dataCollection['trim'],
                    'stock' => $dataCollection['stock_number'],
                    'engine_details' => $dataCollection['engine'],
                    'transmission' => $dataCollection['transmission'],
                    'body_description' => $car_body,
                    'vehicle_feature_description' => $dataCollection['dealer_option'],
                    'fuel' => $dataCollection['fuel'],
                    'drive_info' => $dataCollection['drive_train'],
                    'mpg' => $dataCollection['avg_mpg'],
                    'mpg_city' => null,
                    'mpg_highway' => null,
                    'exterior_color' => $dataCollection['exterior_color'],
                    // 'star' => $dataCollection['interior_color'],
                    // 'created_date' => $dataCollection[32],
                    'created_date' => $formattedDate,
                    'batch_no' => 110,
                    'stock_date_formated' => $formattedDate,
                    'user_id' => $id,
                    'payment_price' => $monthly_payment,
                    'body_formated' => $car_body,
                    'is_feature' => 0,
                    'status' => 1,
                ];

                // Insert into Inventory
                $inventory_table_data = Inventory::create($inventoryData);

                // Insert into TmpInventory
                if(!$tmp_inventory){
                    // $tmp_inventory->delete();
                    $tmp_inventory_table_data = TmpInventories::create($inventoryData);
                }

                // Commit the transaction if both inserts succeed
                // DB::commit();

            } catch (\Exception $e) {
                // Rollback the transaction on error
                // DB::rollBack();
                $errors[] = "Error inserting inventory data: " . $e->getMessage();
            }

            if (!empty($errors)) {
                echo "The following errors occurred:\n";
                foreach ($errors as $error) {
                    echo $error . "\n";
                }
            }
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
}
