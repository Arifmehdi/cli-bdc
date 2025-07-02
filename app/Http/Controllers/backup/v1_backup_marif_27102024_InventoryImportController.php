<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\TmpInventories;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class InventoryImportController extends Controller
{
    public function index()
    {
        $users = User::whereNotNull('name')->get();
        $inventories = Inventory::orderBy('id', 'desc')->paginate(12);
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
        $modifiedFileName = date('mdY') . '_' . $originalFileName;
        $request->file('import_file')->move($directory, $modifiedFileName);

        $filePath = $directory . '/' . $modifiedFileName;
        $file_rows = Excel::toArray([], $filePath)[0];

        $inventory_sold = [];
        $inventory_added = [];

        $inventory = Inventory::with('dealer')->select('*')->get();   //vin


        // if (!empty($file_rows)) {
        //     foreach ($inventory as $inventoryDetails) {
        //         $inventoryItemFound = false;

        //         foreach ($file_rows as $key => $row) {
        //             if ($key < 1) continue;

        //             if ($inventoryDetails['vin'] == $row[16]) {
        //                 $inventoryItemFound = true;
        //                 break;
        //             }
        //         }

        //         if (!$inventoryItemFound) {
        //             // $inventory_sold[] = $inventoryDetails['stock'];
        //             // Inventory::where('vin', $inventoryDetails['vin'])->update(['status' => 0]);
        //         }
        //     }

        //     foreach ($file_rows as $key => $row) {
        //         if ($key < 1) continue;
        //         $newInventoryItemFound = true;

        //         foreach ($inventory as $inventoryDetails) {
        //             if ($inventoryDetails['stock'] == $row[22]) {
        //                 $newInventoryItemFound = false;
        //                 break;
        //             }
        //         }

        //         if ($newInventoryItemFound) {

        //             $inventory_added[] = $row[22];

        //             $dealer = User::where('phone', $row[2])->first();
        //             if ($dealer) {
        //                 $dealer_id = $dealer->dealer_id;
        //                 $user_id = $dealer->id;
        //             } else {
        //                 $custom_dealer_id = $row[0];

        //                 // old marif code 
        //                 // if (!isset($row[1]) || empty(trim($row[1])) || !isset($row[2]) || empty(trim($row[2]))) {
        //                 //     return;
        //                 // }
        //                 // $max_dealer = User::where('dealer_id', 'LIKE', 'DDRI-%')
        //                 //     ->orderByDesc(DB::raw('CAST(SUBSTRING(dealer_id, 6) AS UNSIGNED)'))
        //                 //     ->first();

        //                 // // Determine the numeric part of the max dealer_id
        //                 // if ($max_dealer) {
        //                 //     $max_dealer_id_numeric = intval(substr($max_dealer->dealer_id, 5));  // Extract numeric part from the max dealer_id
        //                 // } else {
        //                 //     $max_dealer_id_numeric = 0;      // If no DDRI prefixed dealer_id is found, start from 0
        //                 // }
        //                 // $new_dealer_id_numeric = $max_dealer_id_numeric + 1;    // Increment the numeric part for new dealer_id
        //                 // $custom_dealer_id = "DDRI-" . $new_dealer_id_numeric;    // Construct the new dealer_id with "DDRI-" prefix
        //                 // $dealer = User::where('phone', $row[2])->where('phone', '!=', 'null')->first(); // array_walk($file_rows, function($row) use (&$max_dealer_id) {

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


        // uiif woiefoiw friweroi wu2ir wieriw froiweuri wuriuqwriu woiruwiruiqwruiwuri wroiu oiuwiruiqwuri iwur iwuir wiru uriowuri wu riweuriweu irwueir 
        if (!empty($file_rows)) {
            foreach (array_slice($file_rows, 1) as $row) {
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
                    // Inventory::where('vin', $inventoryDetails['vin'])->update(['status' => 0]);
                }
            
                if ($newInventoryItemFound) {
                    $inventory_added[] = $row[22];

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
                    $this->createTmpInventory($row, $userId, $dealer_id, $user_id);

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
        if (!($row[17] == 'Contact for Price')) {
            $payment_price =  $row[17];
            $interest_rate = (5.82 / 100);
            $down_payment_percentage = 10 / 100;

            $down_payment = $payment_price * $down_payment_percentage;
            $loan_amount = $payment_price - $down_payment;
            $calculateMonthValue = 72;
            $monthly_interest_rate = $interest_rate / 12;

            $monthly_payment = ceil(($loan_amount * $monthly_interest_rate) / (1 - pow(1 + $monthly_interest_rate, -$calculateMonthValue)));
        } 

        // dd($row, $id, $dealer_id, $user_id, $formattedDate, $monthly_payment,$row[17], $row[35],'love you');
        // else {
        //     $payment_price = 0;
        // }

        $lowerString = strtolower($row[25]);

        if (strpos($lowerString, 'coupe') !== false || strpos($lowerString, '2dr') !== false) {
            $car_body = 'Coupe';
        } elseif (strpos($lowerString, 'hetchback') !== false || strpos($lowerString, '3dr') !== false) {
            $car_body = 'Hatchback';
        } elseif (strpos($lowerString, 'sedun') !== false || strpos($lowerString, '4dr') !== false) {
            $car_body = 'Sedun';
        } elseif (strpos($lowerString, 'pickup') !== false || strpos($lowerString, 'Crew Cab Pickup') !== false || strpos($lowerString, 'Regular Cab Pickup') !== false || strpos($lowerString, 'Extended Cab Pickup') !== false) {
            $car_body = 'Truck';
        } elseif (strpos($lowerString, 'cargo') !== false || strpos($lowerString, 'Full-size Cargo Van') !== false || strpos($lowerString, 'Mini-van, Cargo') !== false) {
            $car_body = 'Cargo Van';
        } elseif (strpos($lowerString, 'cargo') !== false) {
            $car_body = 'Cargo Van';
        } elseif (strpos($lowerString, 'passenger') !== false || strpos($lowerString, 'Full-size Passenger Van') !== false) {
            $car_body = 'Passenger Van';
        } elseif (strpos($lowerString, 'Mini-van') !== false || strpos($lowerString, 'Mini-van, Passenger') !== false) {
            $car_body = 'Minivan';
        } elseif (strpos($lowerString, 'sport') !== false || strpos($lowerString, 'Sport Utility') !== false || strpos($lowerString, 'Full Size SUV') !== false) {
            $car_body = 'SUV';
        } else {
            $car_body = $row[25];
        }


        $vehicleMakeData = VehicleMake::where('make_name', $row[14])->first();

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

        // $output = $row[18];
        // preg_match('/\d+,\d+/', $output, $matches);
        // ($matches != []) ? $numericValue = (int)str_replace(',', '', $matches[0]) : $numericValue = 0;

        $latitude = $row[42];
        $longitude = $row[43];

        try {
            DB::beginTransaction();
        
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
                'miles' => $row[18],
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
                'created_date' => $row[32],
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
            DB::commit();
        
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            $errors[] = "Error inserting inventory data: " . $e->getMessage();
        }

        if (!empty($errors)) {
            echo "The following errors occurred:\n";
            foreach ($errors as $error) {
                echo $error . "\n";
            }
        }
    }



    public function handleRequest($namePart, $request)
    {
        dd($namePart);
        if ($namePart == 'carguru') {
            $dataInform = $this->storeInventoryForCarguru($request);
            return response()->json([
                'add' => $dataInform['inventory_added'],
                'total_add' => count($dataInform['inventory_added']),
                'sold' => $dataInform['inventory_sold'],
                'total_sold' => count($dataInform['inventory_sold'])
            ]);
        }
    }



    // protected function storeInventoryForCarguru($request)
    // {
    //         $userId = $request->user;
    //         $directory = public_path('uploads/import');

    //         if (!is_dir($directory)) {
    //             mkdir($directory, 0755, true);
    //         }

    //         $originalFileName = $request->file('import_file')->getClientOriginalName();
    //         $modifiedFileName = date('mdY') . '_' . $originalFileName;
    //         $request->file('import_file')->move($directory, $modifiedFileName);

    //         $filePath = $directory . '/' . $modifiedFileName;
    //         $file_rows = Excel::toArray([], $filePath)[0];

    //         $inventory_sold = [];
    //         $inventory_added = [];

    //         $inventory = Inventory::select('*')->get();

    //         if (!empty($file_rows)) {
    //             foreach ($inventory as $inventoryDetails) {
    //                 $inventoryItemFound = false;

    //                 foreach ($file_rows as $key => $row) {
    //                     if ($key < 1) continue;
    //                     if ($inventoryDetails['stock'] == $row[43]) {
    //                         $inventoryItemFound = true;
    //                         break;
    //                     }
    //                 }

    //                 if (!$inventoryItemFound) {
    //                     // $inventory_sold[] = $inventoryDetails['stock'];
    //                     // Inventory::where('vin', $inventoryDetails['vin'])->update(['status' => 0]);
    //                 }
    //             }


    //             $newInventoryItemFound = true;

    //             foreach ($inventory as $inventoryDetails) {
    //                 if ($inventoryDetails['stock'] == $row[43]) {
    //                     $newInventoryItemFound = false;
    //                     break;
    //                 }
    //             }

    //             if ($newInventoryItemFound) {

    //                 $inventory_added[] = $row[22];

    //                 $dealer = User::where('phone', $row[2])->first();
    //                 if ($dealer) {
    //                     $dealer_id = $dealer->dealer_id;
    //                     $user_id = $dealer->id;
    //                 } else {

    //                     if (!isset($row[1]) || empty(trim($row[1])) || !isset($row[2]) || empty(trim($row[2]))) {
    //                         return;
    //                     }
    //                     $max_dealer = User::where('dealer_id', 'LIKE', 'DDRI-%')
    //                         ->orderByDesc(DB::raw('CAST(SUBSTRING(dealer_id, 6) AS UNSIGNED)'))
    //                         ->first();

    //                     // Determine the numeric part of the max dealer_id
    //                     if ($max_dealer) {
    //                         $max_dealer_id_numeric = intval(substr($max_dealer->dealer_id, 5));  // Extract numeric part from the max dealer_id
    //                     } else {
    //                         $max_dealer_id_numeric = 0;      // If no DDRI prefixed dealer_id is found, start from 0
    //                     }
    //                     $new_dealer_id_numeric = $max_dealer_id_numeric + 1;    // Increment the numeric part for new dealer_id
    //                     $custom_dealer_id = "DDRI-" . $new_dealer_id_numeric;    // Construct the new dealer_id with "DDRI-" prefix
    //                     $dealer = User::where('phone', $row[2])->where('phone', '!=', 'null')->first(); // array_walk($file_rows, function($row) use (&$max_dealer_id) {

    //                     if (!$dealer && !empty($row[2])) {
    //                         $user = new User();
    //                         $user->dealer_id        = $custom_dealer_id;
    //                         $user->name             = $row[1];
    //                         $user->phone            = $row[2];
    //                         $user->address          = $row[4];
    //                         $user->city             = $row[5];
    //                         $user->state            = $row[6];
    //                         $user->dealer_iframe_map = $row[7];
    //                         $user->save();

    //                         $user_id = $user->id;
    //                         $dealer_id = $user->dealer_id;

    //                         $role = 'dealer';
    //                         $role =  Role::where('name', $role)->first();
    //                         if (!$role) {
    //                             return response()->json([
    //                                 'status' => 'error',
    //                                 'message' => "The specified role does not exist or is not for the 'web' guard.",
    //                             ], 422);
    //                         }
    //                         $user->assignRole($role);
    //                     }
    //                 }

    //                 $dat = $this->createCarguruTmpInventory($row, $userId, $dealer_id, $user_id);
    //             }
    //         // return $request->all();

    //     }
    // }


    // private function createCarguruTmpInventory(array $row, int $id, $dealer_id, int $user_id)
    // {
    //     $errors = [];

    //     $formattedDate = date('Y-m-d', strtotime($row[32]));
    //     $monthly_payment = 0;
    //     if (!($row[16] == 0) || !($row[16] == null)) {
    //         $payment_price_cus = $row[16];
    //         $price_clean = str_replace(['$', ','], '', $payment_price_cus);
    //         $payment_price = intval($price_clean);

    //         $interest_rate = (5.82 / 100);
    //         $down_payment_percentage = 10 / 100;

    //         $down_payment = $payment_price * $down_payment_percentage;
    //         $loan_amount = $payment_price - $down_payment;
    //         $calculateMonthValue = 72;
    //         $monthly_interest_rate = $interest_rate / 12;

    //         $monthly_payment = ceil(($loan_amount * $monthly_interest_rate) / (1 - pow(1 + $monthly_interest_rate, -$calculateMonthValue)));
    //     } else {
    //         $payment_price = 0;
    //     }

    //     $lowerString = strtolower($row[40]);
    //     // return $lowerString;

    //     if (strpos($lowerString, 'coupe') !== false || strpos($lowerString, '2dr') !== false) {
    //         $car_body = 'Coupe';
    //     } elseif (strpos($lowerString, 'hetchback') !== false || strpos($lowerString, '3dr') !== false) {
    //         $car_body = 'Hatchback';
    //     } elseif (strpos($lowerString, 'sedun') !== false || strpos($lowerString, '4dr') !== false || strpos($lowerString, 'Sedan') !== false) {
    //         $car_body = 'Sedan';
    //     } elseif (strpos($lowerString, 'pickup') !== false || strpos($lowerString, 'Crew Cab Pickup') !== false || strpos($lowerString, 'Regular Cab Pickup') !== false || strpos($lowerString, 'Extended Cab Pickup') !== false || strpos($lowerString, 'Pickup Truck') !== false) {
    //         $car_body = 'Truck';
    //     } elseif (strpos($lowerString, 'cargo') !== false || strpos($lowerString, 'Full-size Cargo Van') !== false || strpos($lowerString, 'Mini-van, Cargo') !== false) {
    //         $car_body = 'Cargo Van';
    //     } elseif (strpos($lowerString, 'cargo') !== false) {
    //         $car_body = 'Cargo Van';
    //     } elseif (strpos($lowerString, 'passenger') !== false || strpos($lowerString, 'Full-size Passenger Van') !== false) {
    //         $car_body = 'Passenger Van';
    //     } elseif (strpos($lowerString, 'Mini-van') !== false || strpos($lowerString, 'Mini-van, Passenger') !== false  || strpos($lowerString, 'Minivan') !== false) {
    //         $car_body = 'Minivan';
    //     } elseif (strpos($lowerString, 'sport') !== false || strpos($lowerString, 'Sport Utility') !== false || strpos($lowerString, 'Full Size SUV') !== false || strpos($lowerString, 'SUV / Crossover') !== false) {
    //         $car_body = 'SUV';
    //     } elseif (strpos($lowerString, 'Wagon') !== false ) {
    //         $car_body = 'Station Wagon';
    //     } else {
    //         $car_body = $row[40];
    //     }

    //     $cus_drivetain = $row[29];
    //     // if($cus_drivetain != null){
    //         if ($cus_drivetain == 'All-Wheel Drive')
    //         {
    //             $drivetrain_result = 'AWD';
    //         }else if($cus_drivetain == 'Front-Wheel Drive')
    //         {
    //             $drivetrain_result = 'FWD';
    //         }else if($cus_drivetain == 'Four-Wheel Drive')
    //         {
    //             $drivetrain_result = '4WD';
    //         }else if($cus_drivetain == 'Rear-Wheel Drive')
    //         {
    //             $drivetrain_result = 'RWD';
    //         }else{
    //             $drivetrain_result = $row[29];
    //         }
    //     // }

    //     $vehicleMakeData = VehicleMake::where('make_name', $row[14])->first();

    //     if (!$vehicleMakeData) {
    //         $inventory_make_data = VehicleMake::create([
    //             'make_name' => $row[14],
    //             // 'status' => 1,
    //         ]);
    //         $makeId = $inventory_make_data->id;
    //     } else {
    //         $makeId = $vehicleMakeData->id;
    //     }

    //     $cleanedStr = preg_replace('/[^0-9,]/', '', $row[30]);
    //     $cleanedStr = str_replace(',', '', $cleanedStr);
    //    $cus_miles = (int)$cleanedStr;

    //     // $output = $row[18];
    //     // preg_match('/\d+,\d+/', $output, $matches);
    //     // ($matches != []) ? $numericValue = (int)str_replace(',', '', $matches[0]) : $numericValue = 0;

    //     try {
    //         $inventory_table_data = Inventory::create([
    //             'dealer_id' => $dealer_id,
    //             'deal_id' => $user_id,
    //             'dealer_comment' => $row[3],
    //             'zip_code' => $row[8],
    //             'latitude' => $row[42],
    //             'longitude' => $row[43],
    //             'detail_url' => $row[9],
    //             'img_from_url' => $row[10],
    //             'local_img_url' => $row[11],
    //             'vehicle_make_id' => $makeId,
    //             'title' => $row[12],
    //             'year' => $row[13],
    //             'make' => $row[14],
    //             'model' => $row[15],
    //             'vin' => $row[42],
    //             'price' => $payment_price,
    //             'miles' => $cus_miles,
    //             'type' => $row[41],
    //             // 'price_without_discount' => $row[19],
    //             'trim' => $row[39],
    //             'stock' => $row[43],
    //             'engine_details' => $row[33],
    //             'transmission' => $row[35],
    //             'body_description' => $car_body,
    //             'fuel' => $row[34],
    //             'drive_info' => $drivetrain_result,
    //             'mpg_city' => $row[46],
    //             'mpg_highway' => $row[47],
    //             'exterior_color' => $row[32],
    //             'star' => 5,
    //             'created_date' => $row[22],
    //             // 'batch_no' => 15,
    //             'batch_no' => $row[23],
    //             'stock_date_formated' => $formattedDate,
    //             'user_id' =>  $id,
    //             'payment_price' => $monthly_payment,
    //             'body_formated' => $car_body,
    //             'is_feature' => 0,
    //             'status' => 1,
    //         ]);

    //     } catch (\Exception $e) {
    //         $errors[] = "Error inserting inventory data: " . $e->getMessage();
    //     }

    //     if (!empty($errors)) {
    //         echo "The following errors occurred:\n";
    //         foreach ($errors as $error) {
    //             echo $error . "\n";
    //         }
    //     }
    // }



}
