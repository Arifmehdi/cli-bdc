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
use League\Csv\Reader;
use League\Csv\Writer;

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

    // public function storeInventory(Request $request)
    // {
    //     //// abort_if(! auth()->user()->can('hrm_bulk_attendance_import_store'), 403, 'Access forbidden');
    //     $request->validate([
    //         'user' => 'required',
    //         'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
    //     ], [
    //         'user.required' => 'User field is required',
    //         'import_file.required' => 'Import field is required',
    //         'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
    //     ]);

    //     // ***********data store start here
    //     $originalName = $request->file('import_file')->getClientOriginalName();


    //     $userId = $request->user;
    //     $directory = public_path('uploads/import');

    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     $originalFileName = $request->file('import_file')->getClientOriginalName();
    //     $modifiedFileName = $userId.'_'.date('mdY') . '_' . $originalFileName;
    //     $request->file('import_file')->move($directory, $modifiedFileName);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => "Non-Server CSV Stored Successfully",
    //     ], 200);
    // }


    public function storeInventory(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
        ], [
            'user.required' => 'User field is required',
            'import_file.required' => 'Import field is required',
            'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
        ]);

        $userId = $request->user;
        $directory = public_path('uploads/import');
    
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $originalName = $request->file('import_file')->getClientOriginalName();
        $file = $request->file('import_file');
        $filePath = $file->getPathname();

        // Read the CSV file
        $data = array_map('str_getcsv', file($filePath));
        $headers = array_shift($data); // Extract headers from the first row
    
        $batchSize = $request->splitNumber; // Number of rows per batch
        $batchNumber = 101; // Starting batch number
        $totalRows = count($data);
        $totalBatches = ceil($totalRows / $batchSize); // Calculate total batches needed

        Log::info("Total Rows: " . $totalRows); // Log total rows
        Log::info("Total Batches: " . $totalBatches); // Log total batches
    
        // Iterate over data and create batches
        for ($i = 0; $i < $totalRows; $i += $batchSize) {
            $batchData = array_slice($data, $i, $batchSize);

            // If the batch data is empty, skip this iteration
            if (empty($batchData)) {
                continue;
            }
    
            // Add batch_no to each row
            foreach ($batchData as &$row) {
                $row[] = $batchNumber;
            }
    
            // Add the `batch_no` column to headers if it's not already present
            if (!in_array('batch_no', $headers)) {
                $headers[] = 'batch_no';
            }
    
            // // Check if the batch number exceeds total batches, and break out if necessary
            // if ($batchNumber > $totalBatches) {
            //     break;
            // }
    
            // Create a new CSV file for the batch
            $batchFileName = $directory . "/{$userId}_{$originalName}_{$batchNumber}.csv";
            // Check if the file already exists to avoid overwriting or duplication
            if (file_exists($batchFileName)) {
                Log::warning("File already exists: " . $batchFileName);
                continue; // Skip this iteration to avoid overwriting
            }
    
            $fileHandle = fopen($batchFileName, 'w');
            fputcsv($fileHandle, $headers);
    
            foreach ($batchData as $row) {
                fputcsv($fileHandle, $row);
            }
    
            fclose($fileHandle);
    
            Log::info("Batch {$batchNumber} created with " . count($batchData) . " rows.");
    
            // Increment batch number for next batch
            $batchNumber++;
    
            // Check if the last batch has fewer rows and log that
            if (count($batchData) < $batchSize) {
                Log::info("Last batch has fewer rows: " . count($batchData));
            }
        }
    
        return response()->json([
            'status' => 'success',
            'message' => "CSV processed and stored in batches successfully.",
        ], 200);
    }
    
    
    
    

    // // this work but not saved batch_no 
    // public function storeInventory(Request $request)
    // {
    //     $request->validate([
    //         'user' => 'required',
    //         'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
    //     ], [
    //         'user.required' => 'User field is required',
    //         'import_file.required' => 'Import field is required',
    //         'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
    //     ]);

    //     // Store the file in the uploads directory
    //     $userId = $request->user;
    //     $directory = public_path('uploads/import');
    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     $originalFileName = $request->file('import_file')->getClientOriginalName();
    //     $modifiedFileName = $userId.'_'.date('mdY') . '_' . $originalFileName;
    //     $filePath = $request->file('import_file')->move($directory, $modifiedFileName);

    //     // Read the CSV file using League\Csv
    //     $csv = Reader::createFromPath($filePath, 'r');
    //     $csv->setHeaderOffset(0); // Assuming the first row contains headers

    //     $rows = $csv->getRecords(); // Get records as associative arrays
    //     $batchSize = 10; // Define the batch size
    //     $batchNumber = 1;
    //     $batches = [];
    //     $batchData = [];

    //     foreach ($rows as $row) {
    //         $batchData[] = $row;
    //         // Process the batch when it reaches the defined size
    //         if (count($batchData) === $batchSize) {
    //             $this->processBatch($batchData, $userId, $batchNumber);
    //             $batches[] = $batchData;
    //             $batchData = [];
    //             $batchNumber++;
    //         }
    //     }

    //     // Process any remaining rows
    //     if (!empty($batchData)) {
    //         $this->processBatch($batchData, $userId, $batchNumber);
    //         $batches[] = $batchData;
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => "CSV file processed in batches and saved successfully.",
    //     ], 200);
    // }

    // private function processBatch(array $batchData, $userId, $batchNumber)
    // {
    //     // // Save to database
    //     // foreach ($batchData as $row) {
    //     //     DB::table('your_table_name')->insert([
    //     //         'user_id' => $userId,
    //     //         'data_column_1' => $row['Column1'], // Replace 'Column1' with your actual column name
    //     //         'data_column_2' => $row['Column2'], // Add more columns as needed
    //     //         'created_at' => now(),
    //     //         'updated_at' => now(),
    //     //     ]);
    //     // }

    //     // Save batch as a CSV file
    //     $directory = public_path("uploads/import");
    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     $batchFilePath = $directory . "/batch_{$batchNumber}_user_{$userId}.csv";
    //     $csvWriter = Writer::createFromPath($batchFilePath, 'w+');
    //     $csvWriter->insertOne(array_keys($batchData[0])); // Insert header
    //     $csvWriter->insertAll($batchData); // Insert rows
    // }

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

                $index = $index + 1;
                // Example: Access data by header names
                $dealer_id_data = $rowData['dealer id'] ?? $rowData['customer id'] ?? null ;
                $dealer_type_data = $rowData['dealer type'] ?? null;
                $dealer_name_data = $rowData['dealer name'] ?? null;
                $dealer_full_address_data = $rowData['dealer address'] ?? null;
                $dealer_address_data = $rowData['dealer street'] ?? null;

                // if ($dealer_address_data) {
                //     // Split the address into parts by commas
                //     $dealer_address_explode_data = explode(',', $dealer_address_data);

                //     // Ensure the second part exists before accessing it
                //     if (isset($dealer_address_explode_data[1])) {
                //         // Extract the last word (city) from the second part
                //         $lastSpacePosition = strrpos($dealer_address_explode_data[0], ' '); // Find the last space
                //         $city_info = trim(substr($dealer_address_explode_data[0], $lastSpacePosition + 1));
                //         // Split the second part by spaces for state and zip
                //         $dealer_city_explode_data = explode(' ', trim($dealer_address_explode_data[1]));

                //         $state_info = $dealer_city_explode_data[0] ?? null; // State
                //         $zip_info = $dealer_city_explode_data[1] ?? null;   // Zip
                //     } else {
                //         $city_info = $state_info = $zip_info = null; // Default if second part doesn't exist
                //     }
                // } else {
                //     $city_info = $state_info = $zip_info = null; // Default if address is null
                // }

                // // // Debugging Output
                // // dd([
                // //     'city' => $city_info,
                // //     'state' => $state_info,
                // //     'zip' => $zip_info,
                // // ]);

                // dd($city_info,$state_info,$zip_info,$dealer_city_explode_data[0],$dealer_city_explode_data[1], $dealer_address_explode_data);
                $city_info = $rowData['dealer city'] ??  null;
                $state_info = $rowData['dealer region'] ?? null;
                $zip_info = $rowData['dealer zip code'] ?? null;

                $phone_digitsOnly = preg_replace('/\D/', '', $rowData['dealer sales phone']);
                $dealerPhone = $phone_digitsOnly ??  null;

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
                $price_digitsOnly = preg_replace('/\D/', '', $price_data);

                // Mileage
                $mileage_data = $rowData['mileage'] ?? 0;
                $cus_mileage = preg_replace('/\D/', '', $mileage_data);
                $fuel_data = $rowData['fuel'] ?? null;
                
                $city_mpg_data = $rowData['city mpg'] ?? null;
                $hwy_mpg_data = $rowData['hwy mpg'] ?? null;
                $engine_data = $rowData['engine'] ?? null;
                $transmission_data = $rowData['transmission'] ?? null;
                $year_data = $rowData['year'] ?? null;
                $type_data = $rowData['type'] ?? null;
                $stock_num_data = $rowData['stock number'] ?? null;
                $vin = $rowData['vin'] ?? null;
                $body_type_data = $rowData['body type'] ?? null;
                $dealer_option_info = $rowData['option'] ?? null;
                $drive_train_data = $rowData['drive train'] ?? null;
                $primary_image_data = $rowData['primary image'] ?? null;
                $all_images_data = $rowData['all images'] ?? $rowData['all image'] ?? null;
                $batch_no_data = $rowData['batch_no'] ?? null;

                //no need last csv
                $avg_mpg_data = $rowData['avg_mpg'] ?? null;
                $salesstatus_data = $rowData['salesstatus'] ?? null;
                $days_on_market_data = $rowData['days on market'] ?? null;               
                
                $dataCollection = collect([
                    'dealer_id' => $dealer_id_data ?? null,
                    'dealer_name' => $dealer_name_data ?? null,
                    'dealer_full_address' => $dealer_full_address_data ?? null,
                    'dealer_address' => $dealer_address_data ?? null,
                    'dealer_city' => $city_info ?? null,
                    'dealer_region' => $state_info ?? null,
                    'dealer_zip' => $zip_info ?? null,
                    'dealer_phone' => $dealerPhone ?? null,
                    'dealer_rating' => $dealer_rating ?? null,
                    'dealer_review' => $dealer_review ?? null,
                    'source_url' => $source_url ?? null,
                    'dealer_option' => $dealer_option_info ?? null,
                    'titles' => $titles_data ?? null,
                    'trim' => $trim_data ?? null,
                    'make' => $make_data ?? null,
                    'model' => $model_data ?? null,
                    'exterior_color' => $exterior_color_data ?? null,
                    'interior_color' => $interior_color_data ?? null,
                    'price' => isset($price_digitsOnly) ? (int)$price_digitsOnly : 0,
                    'milage' => isset($cus_mileage) ? (int)$cus_mileage : 0,
                    'fuel' => $fuel_data ?? null,
                    'city_mpg_data' => $city_mpg_data ?? null,
                    'hwy_mpg_data' => $hwy_mpg_data ?? null,
                    'engine' => $engine_data ?? null,
                    'transmission' => $transmission_data ?? null,
                    'year' => $year_data ?? null,
                    'type' => $type_data ?? null,
                    'stock_number' => $stock_num_data ?? null,
                    'vin' => $vin ?? null,
                    'body_type' => $body_type_data ?? null,
                    'drive_train' => $drive_train_data ?? null,
                    'primary_image' => $primary_image_data ?? null,
                    'all_images' => $all_images_data ?? null,
                    
                    // 'dealer_no' => $rowData['dealer no'] ?? null,
                    'avg_mpg' => $avg_mpg_data ?? null,
                    'sales_status' => $rowData['salesstatus'] ?? null,
                    'days_on_market' => $rowData['days on market'] ?? null,
                ]);

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
                            $user->dealer_id           = $custom_dealer_id;
                            $user->name                = $dealer_name_data;
                            $user->phone               = $dealerPhone;
                            $user->dealer_full_address = $dealer_full_address_data;
                            $user->address             = $dealer_address_data;
                            $user->city                = $city_info;
                            $user->state               = $state_info;
                            $user->zip                 = $zip_info;
                            $user->dealer_iframe_map   = $row[7] ?? null;
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
                    $dato = $this->createTmpInventory($row, $userId, $dealer_id, $user_id, $dataCollection, $filePath);
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

    private function createTmpInventory(array $row, int $id, $dealer_id, int $user_id, $dataCollection , $filePath)
    {

        // dd($dataCollection['price']);
        //dd($dataCollection['price'], $dataCollection);

        // // Proxy list
        // $proxies = [
        //     'http://rxlarecz:icx36fesnh6x@198.23.239.134:6540',
        //     'http://rxlarecz:icx36fesnh6x@107.172.163.27:6543',
        //     'http://rxlarecz:icx36fesnh6x@173.211.0.148:6641',
        //     'http://rxlarecz:icx36fesnh6x@167.160.180.203:6754',
        //     'http://rxlarecz:icx36fesnh6x@173.0.9.70:5653',
        //     'http://rxlarecz:icx36fesnh6x@173.0.9.209:5792',
        // ];
        // Proxy list
  
        $proxies = [
            'http://104.207.37.254:3128',
            'http://156.228.115.91:3128',
            'http://104.207.34.142:3128',
            'http://104.207.42.154:3128',
            'http://156.228.93.54:3128',
            'http://104.207.57.252:3128',
            'http://104.207.52.29:3128',
            'http://156.228.87.247:3128',
            'http://156.228.94.174:3128',
            'http://156.228.84.22:3128',
        ];

        // foreach ($proxies as $proxy) {
        //     try {
        //         $response = Http::withOptions(['proxy' => $proxy, 'timeout' => 5])
        //                         ->get('https://httpbin.org/ip'); // Test request
        //         echo "Proxy $proxy works: " . $response->body() . PHP_EOL;
        //         dd('i love you ', $response->successful(),$response->body());
        //     } catch (\Exception $e) {
        //         echo "Proxy $proxy failed: " . $e->getMessage() . PHP_EOL;
        //     }
        // }


        // if (empty($dataCollection['all_images'])) {
        //     throw new \Exception('No images found in dataCollection[\'all_images\']');
        // }

        // $localImagePaths = [];
        // $saveDir = public_path('frontend/uploads/autotrader/' . $dataCollection['vin']);

        // // Ensure the directory exists
        // if (!File::exists($saveDir)) {
        //     File::makeDirectory($saveDir, 0777, true, true);
        // }

        // // Process image URLs
        // $imageString = $dataCollection['all_images'];
        // $imageUrls = explode(',', $imageString);
        // $imageUrls = array_slice($imageUrls, 0, 5);

        // foreach ($imageUrls as $url) {
        //     $fileName = basename($url);
        //     $localPath = $saveDir . '/' . $fileName;

        //     // Skip download if the file already exists
        //     if (File::exists($localPath)) {
        //         $localImagePaths[] = asset('frontend/uploads/autotrader/' . $dataCollection['vin'] . '/' . $fileName);
        //         continue;
        //     }
        //       // Select a random proxy
        //       $proxy = $proxies[array_rand($proxies)];

        //     try {
        //         // Select a random proxy
        //         $proxy = $proxies[array_rand($proxies)];

        //         // Download the image using the selected proxy
        //         $response = Http::withOptions([
        //             'proxy' => $proxy, // Add proxy here
        //             'timeout' => 10    // Optional timeout
        //         ])->get(trim($url)); // Trim to avoid extra spaces
        //         // dd($response->body());
        //         if ($response->successful()) {
        //             File::put($localPath, $response->body());
        //             $localImagePaths[] = asset('frontend/uploads/autotrader/' . $dataCollection['vin'] . '/' . $fileName);
        //         } else {
        //             Log::warning("Failed to download image: $url using proxy $proxy");
        //         }
        //     } catch (\Exception $e) {
        //         Log::error("Error downloading image $url using proxy $proxy: " . $e->getMessage());
        //     }

        //     sleep(rand(4, 6)); // Random sleep between requests
        // }

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
                $localImagePaths[] = asset('frontend/uploads/autotrader/' .$dataCollection['vin'].'/'. $fileName);
                continue;
            }

            try {
                // Download the image
                $response = Http::get(trim($url)); // Trim to avoid extra spaces
                if ($response->successful()) {
                    File::put($localPath, $response->body());
                    $localImagePaths[] = asset('frontend/uploads/autotrader/' .$dataCollection['vin'].'/'. $fileName);
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
        $price = $dataCollection['price']; // Example price
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
            // dd($dealer_id, $user_id);
            $isSuccessful = false;
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
                    'img_from_url' => $dataCollection['all_images'],
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
                    'mpg_city' => $dataCollection['city_mpg_data'],
                    'mpg_highway' => $dataCollection['hwy_mpg_data'],
                    'exterior_color' => $dataCollection['exterior_color'],
                    'interior_color' => $dataCollection['interior_color'],
                    // 'star' => $dataCollection['star'],
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
                $isSuccessful = true;
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

            // Remove the file if processing was successful
            $newFilePath = public_path('uploads/taken/');

            // Ensure the folder exists
            if (!File::isDirectory($newFilePath)) {
                try {
                    File::makeDirectory($newFilePath, 0755, true); // Create directory with permissions and recursive option
                    echo "Folder created successfully: $newFilePath\n";
                } catch (\Exception $e) {
                    echo "Error creating folder: " . $e->getMessage() . "\n";
                }
            }

            if ($isSuccessful) {
                try {
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                        // File::move($filePath, $newFilePath);
                        echo "File moved successfully in : $newFilePath\n";
                    } else {
                        echo "File not moved in : $newFilePath\n";
                    }
                } catch (\Exception $e) {
                    echo "Error deleting file: " . $e->getMessage() . "\n";
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
