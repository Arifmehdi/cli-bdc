<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Latlongs;
use App\Models\TmpInventories;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

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

        if (!empty($file_rows)) {
            foreach (array_slice($file_rows, 1) as $row) {
                $dealer = User::where('phone', $row[2])->first();
                if ($dealer) {
                    $dealerId = $dealer->dealer_id;
                    $csvVINsByDealer[$dealerId][] = $row[16];
                }

                $inventoryItemFound = false;
                $newInventoryItemFound = true;

                foreach ($inventory as $inventoryDetails) {
                    // Check if VIN matches
                    if ($inventoryDetails['vin'] == $row[16]) {
                        $inventoryItemFound = true;
                    }

                    // Check if stock matches
                    if ($inventoryDetails['vin'] == $row[16]) {
                        $newInventoryItemFound = false;
                    }

                    // If both conditions are determined, break the loop
                    if ($inventoryItemFound && !$newInventoryItemFound) {
                        break;
                    }
                }

                // Take action based on whether inventoryItemFound and newInventoryItemFound
                if (!$inventoryItemFound) {
                    // dd($inventoryDetails);
                    // Inventory::where('vin', $inventoryDetails['vin'])->update(['status' => 0]);
                }

                if ($newInventoryItemFound) {
                    $inventory_added[] = $row[16];

                    $dealer = User::where('phone', $row[2])->first();
                    if ($dealer) {
                        $dealer_id = $dealer->dealer_id;
                        $user_id = $dealer->id;
                    } else {
                        $custom_dealer_id = $row[0];

                        if (!$dealer && !empty($row[2])) {
                            $user = new User();
                            $user->dealer_id        = $custom_dealer_id;
                            $user->name             = $row[1];
                            $user->phone            = $row[2];
                            $user->address          = $row[4];
                            $user->city             = $row[5];
                            $user->state            = $row[6];
                            $user->dealer_iframe_map = $row[7];
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
                    $dato = $this->createTmpInventory($row, $userId, $dealer_id, $user_id);
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

    private function createTmpInventory(array $row, int $id, $dealer_id, int $user_id)
    {
        $errors = [];
        $formattedDate = date('Y-m-d', strtotime($row[32]));

        $monthly_payment = 0;
        try {
            if (is_numeric($row[17])) {

                $payment_price =  $row[17];
                $interest_rate = (5.82 / 100);
                $down_payment_percentage = 10 / 100;

                $down_payment = $payment_price * $down_payment_percentage;
                $loan_amount = $payment_price - $down_payment;
                $calculateMonthValue = 72;
                $monthly_interest_rate = $interest_rate / 12;

                $monthly_payment = ceil(($loan_amount * $monthly_interest_rate) / (1 - pow(1 + $monthly_interest_rate, -$calculateMonthValue)));
            }
        } catch (\Throwable $th) {
            $monthly_payment = 0;
        }


        $lowerString = strtolower($row[25]);
        $car_body = $this->determineCarBody($lowerString);

        $vehicleMakeData = VehicleMake::where('make_name', $row[14])->first();
        $inventory = Inventory::where('vin',$row[16])->first();

        if(!$inventory){
            if (!$vehicleMakeData) {
                $inventory_make_data = VehicleMake::create([
                    'make_name' => $row[14],
                    // 'status' => 1,
                ]);
                $makeId = $inventory_make_data->id;
            } else {
                $makeId = $vehicleMakeData->id;
            }
            // dd($row, $id, $dealer_id, $user_id, $formattedDate, $monthly_payment,$row[17], $row[35], $car_body, $vehicleMakeData, $makeId, 'love you');
            $zipCode =  $row[8];
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
                $dealer_id = $row[0];  // Default value if dealer_id is not set
            }
            $clear_miles = trim(str_replace('mi', '', $row[18]));
            if($clear_miles == 'N/A'){
                $clear_miles = 0;
            }
            try {
                // DB::beginTransaction();

                // Define the common data
                $inventoryData = [
                    'dealer_id' => $dealer_id,
                    'deal_id' => $user_id,
                    'zip_code' => $row[8],
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'detail_url' => $row[9],
                    'img_from_url' => $row[10],
                    'local_img_url' => $row[11],
                    'vehicle_make_id' => $makeId,
                    'title' => $row[12],
                    'year' => $row[13],
                    'make' => $row[14],
                    'model' => $row[15],
                    'vin' => $row[16],
                    'price' => $payment_price,
                    'miles' => $clear_miles,
                    'type' => $row[19],
                    'trim' => $row[21],
                    'stock' => $row[22],
                    'engine_details' => $row[23],
                    'transmission' => $row[24],
                    'body_description' => $car_body,
                    'vehicle_feature_description' => $row[3],
                    'fuel' => $row[26],
                    'drive_info' => $row[39],
                    'mpg' => $row[37],
                    'mpg_city' => $row[28],
                    'mpg_highway' => $row[29],
                    'exterior_color' => $row[30],
                    'star' => $row[31],
                    // 'created_date' => $row[32],
                    'created_date' => $formattedDate,
                    'batch_no' => $row[33],
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
                $tmp_inventory_table_data = TmpInventories::create($inventoryData);

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
