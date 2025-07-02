<?php

namespace App\Console\Commands;

use App\Models\TmpInventories;
use App\Models\User;
use App\Models\VehicleMake;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Net\SFTP;
use Spatie\Permission\Models\Role;

class DownloadCSVFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download a file on the server';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        // ****** start ********
        // // FTP connection details
        // $ftp_server = '64.209.142.168';
        // $ftp_user = 'localcar_homenet';
        // $ftp_pass = '6n65AraH';
        // $ftp_file = 'homenetauto.csv';

        // // Establish the connection
        // $conn_id = ftp_connect($ftp_server);

        // if (!$conn_id) {
        //     $this->error('Could not connect to FTP server');
        //     return;
        // }

        // // Login
        // $login_result = ftp_login($conn_id, $ftp_user, $ftp_pass);

        // if (!$login_result) {
        //     $this->error('Could not log in to FTP server');
        //     ftp_close($conn_id);
        //     return;
        // }

        // $this->info('Connected to FTP server successfully');

        // // List the contents of the root directory
        // $root_contents = ftp_nlist($conn_id, ".");
        
        // if ($root_contents === false) {
        //     $this->error('Could not list root directory contents');
        //     ftp_close($conn_id);
        //     return;
        // }

        // $this->info('Root directory contents:');
        // foreach ($root_contents as $content) {
        //     $this->info($content);
        // }

        // // Check if the file exists
        // if (in_array($ftp_file, $root_contents)) {
        //     $this->info('File exists on FTP server');
        // } else {
        //     $this->error('File does not exist on FTP server');
        //     ftp_close($conn_id);
        //     return;
        // }

        // // // Check if the local directory exists and is writable
        // // $local_directory = storage_path('app');
        $local_directory = public_path('uploads/import');
        // if (!is_dir($local_directory) || !is_writable($local_directory)) {
        //     $this->error('Local directory does not exist or is not writable');
        //     ftp_close($conn_id);
        //     return;
        // }
        // ****** end ********
        // Download the file
        $date = date('mdY');
        // $local_file = $local_directory . '/'.$date.'_homenetauto.csv';

        $local_file = $local_directory . '/'.'110002255.csv';
        // if (ftp_get($conn_id, $local_file, $ftp_file, FTP_ASCII)) {
        //     $this->info('File downloaded successfully');

        //     // Process the CSV file
        //    //  $this->processCSV($local_file);
        // } else {
        //     $this->error('There was an error downloading the file');
        // }

        // if (ftp_get($conn_id, $local_file, $ftp_file, FTP_ASCII)) {
        //     $this->info('File downloaded successfully');
        
            // Open and read the CSV file
            if (($handle = fopen($local_file, 'r')) !== false) {
                $header = fgetcsv($handle, 500000, ','); // Get the header row
                $header = array_map('strtolower', $header);

                // Now perform a case-insensitive search by using lowercase search terms
                $yearIndex = array_search('year', $header);
                $makeIndex = array_search('make', $header);
                $modelIndex = array_search('model', $header);
                $trimIndex = array_search('trim', $header);
                $stockIndex = array_search('stock', $header);
                $vinIndex = array_search('vin', $header);
                $typeIndex = array_search('type', $header);
                $milesIndex = array_search('miles', $header);
                $transmissionIndex = array_search('transmission', $header);
                $dealerNameIndex = array_search('dealer name', $header);
                $dealerAddressIndex = array_search('dealer address', $header);
                $dealerCityIndex = array_search('dealer city', $header);
                $dealerStateIndex = array_search('dealer state', $header);
                $dealerDetailUrlIndex = array_search('detail url', $header);
                $LocalImageUrlIndex = array_search('local image url', $header);
                $localTitleIndex = array_search('title', $header);

                $dealerLinkIndex = array_search('dealer link', $header);
                $monthlyPayIndex = array_search('monthly pay', $header);
                $dealerPhoneIndex = false;
                $dealerSearchTerms = ['dealer phone', 'dealer number'];

                foreach ($dealerSearchTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $dealerPhoneIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $dealerdescriptionIndex = false;
                $dealerDescriptionSearchTerms = ['dealer comment', 'description'];
                foreach ($dealerDescriptionSearchTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $dealerdescriptionIndex = $index  ?? ''; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }


                $dealerZipIndex = false;
                $dealerdealerZipIndexTerms = ['dealer zip', 'zip code'];
                foreach ($dealerdealerZipIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $dealerZipIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $priceIndex = false;
                $priceTerms = ['sellingprice', 'price'];
                foreach ($priceTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $priceIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $dealerImageUrlIndex = false;
                $dealerImageUrlIndexTerms = ['image from url', 'imagelist'];
                foreach ($dealerImageUrlIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $dealerImageUrlIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $engineDescriptionIndex = false;
                $engineDescriptionIndexTerms = ['engine_description', 'engine details'];
                foreach ($engineDescriptionIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $engineDescriptionIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $bodyDescriptionIndex = false;
                $bodyDescriptionIndexTerms = ['body', 'body description'];
                foreach ($bodyDescriptionIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $bodyDescriptionIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $fuelDescriptionIndex = false;
                $fuelDescriptionIndexTerms = ['fuel', 'fuel_type'];
                foreach ($fuelDescriptionIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $fuelDescriptionIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $driveTrainIndex = false;
                $driveTrainIndexTerms = ['drive info', 'drivetrain'];
                foreach ($driveTrainIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $driveTrainIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $mpgCityIndex = false;
                $mpgCityIndexTerms = ['mpg city', 'citympg'];
                foreach ($mpgCityIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $mpgCityIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $mpgHighwayIndex = false;
                $mpgHighwayIndexTerms = ['mpg highway', 'highwaympg'];
                foreach ($mpgHighwayIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $mpgHighwayIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $extColorIndex = false;
                $extColorIndexTerms = ['exterior color', 'ext_color_generic'];
                foreach ($extColorIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $extColorIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $stockDateIndex = false;
                $stockDateIndexTerms = ['created date', 'dateinstock'];

                $userId = Auth::user()->id;
                foreach ($stockDateIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $stockDateIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $batchNoIndex = false;
                $batchNoIndexTerms = ['batch no', 'batch'];
                foreach ($batchNoIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $batchNoIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                $interiroColorIndex = false;
                $interiroColorIndexTerms = ['interior color', 'interiorcolor'];
                foreach ($interiroColorIndexTerms as $term) {
                    $index = array_search(strtolower($term), array_map('strtolower', $header)); // case-insensitive search
                    if ($index !== false) {
                        $interiroColorIndex = $index; // Store the index if found
                        break; // Exit loop once a match is found
                    }
                }

                if ($dealerPhoneIndex !== false) {
                    // Column found, you can now access the dealer phone data using $dealerPhoneIndex
                    echo "Dealer phone index found at position: " . $dealerPhoneIndex;
                } else {
                    // Handle case where neither column is found
                    echo "Neither 'dealer phone' nor 'dealer number' column found in the header.";
                }
                // if ($dealerdescriptionIndex !== false) {
                //     // Column found, you can now access the dealer phone data using $dealerPhoneIndex
                //     echo "Dealer description index found at position: " . $dealerdescriptionIndex;
                // } else {
                //     // Handle case where neither column is found
                //     echo "Neither 'dealer description' nor 'dsecription' column found in the header.";
                // }
                if ($dealerZipIndex !== false) {
                    // Column found, you can now access the dealer phone data using $dealerPhoneIndex
                    echo "Dealer description index found at position: " . $dealerZipIndex;
                } else {
                    // Handle case where neither column is found
                    echo "Neither 'dealer description' nor 'dsecription' column found in the header.";
                }

                // $dealerPhoneIndex = array_search('dealer phone', $header);
        
                if ($makeIndex === false || $modelIndex === false || $vinIndex === false || $dealerNameIndex===false || $dealerAddressIndex===false || 
                $dealerCityIndex===false || $dealerStateIndex===false  || $dealerPhoneIndex===false) {
                    $this->error('Required columns not found.');
                    fclose($handle);
                    return;
                }
        
                // Create an array to hold car data for display
                $cars = [];
        
                // Loop through the data rows
                while (($data = fgetcsv($handle, 500000, ',')) !== false) {
                    $make = $data[$makeIndex];
                    $model = $data[$modelIndex];
                    $year = $data[$yearIndex];
                    $trim = $data[$trimIndex];
                    $stock = $data[$stockIndex];
                    $vin = $data[$vinIndex];
                    $miles = $data[$milesIndex] !== false ? $data[$milesIndex] : '';
                    $price = $data[$priceIndex] !== false ? $data[$priceIndex] : '';
                    $type = $data[$typeIndex] !== false ? $data[$typeIndex] : '';
                    $dealer_name = $data[$dealerNameIndex];
                    $dealer_address = $data[$dealerAddressIndex];
                    $dealer_city = $data[$dealerCityIndex];
                    $dealer_state = $data[$dealerStateIndex];
                    $dealer_description = $data[$dealerdescriptionIndex];
                    $dealer_zip = $data[$dealerZipIndex];
                    $dealer_phone = $data[$dealerPhoneIndex];
                    $dealer_detail_url = $dealerDetailUrlIndex !== false ? $data[$dealerDetailUrlIndex] : ''; 
                    $dealer_image_url = $dealerImageUrlIndex !== false ? $data[$dealerImageUrlIndex] : ''; 
                    $local_image_url = $LocalImageUrlIndex !== false ? $data[$LocalImageUrlIndex] : ''; 
                    $title = $localTitleIndex !== false ? $data[$localTitleIndex] : ''; 
                    $engine_description = $engineDescriptionIndex !== false ? $data[$engineDescriptionIndex] : ''; 
                    $transmission = $transmissionIndex !== false ? $data[$transmissionIndex] : ''; 
                    $body_description = $bodyDescriptionIndex !== false ? $data[$bodyDescriptionIndex] : ''; 
                    $fuel_description = $fuelDescriptionIndex !== false ? $data[$fuelDescriptionIndex] : ''; 
                    $driveTrain = $driveTrainIndex !== false ? $data[$driveTrainIndex] : ''; 
                    $mpgCity = $mpgCityIndex !== false ? $data[$mpgCityIndex] : ''; 
                    $mpgHighway = $mpgHighwayIndex !== false ? $data[$mpgHighwayIndex] : ''; 
                    $extColor = $extColorIndex !== false ? $data[$extColorIndex] : ''; 
                    $stockDate = $stockDateIndex !== false ? $data[$stockDateIndex] : ''; 
                    $batchNo = $batchNoIndex !== false ? $data[$batchNoIndex] : ''; 
                    $dealerLink = $dealerLinkIndex !== false ? $data[$dealerLinkIndex] : ''; 
                    $monthlyPay = $monthlyPayIndex !== false ? $data[$monthlyPayIndex] : ''; 
                    $interiroColor = $interiroColorIndex !== false ? $data[$interiroColorIndex] : ''; 
        
                    // php code mpping tools here 
                    $existingInventoryByDealer = TmpInventories::select('dealer_id', 'vin')
                                                            ->whereNotNull('vin')
                                                            ->get()
                                                            ->groupBy('dealer_id')
                                                            ->map(fn ($inventory) => $inventory->pluck('vin')->toArray());
                    
                    $inventory_sold = [];
                    $inventory_added = [];
            
                    $csvVINsByDealer = [];
            
                    $inventory = TmpInventories::with('dealer')->select('*')->get(); 

                                
                    // if (!empty($file_rows)) {
                    //     foreach (array_slice($file_rows, 1) as $row) {
                            $dealer_info = User::where('phone', $dealer_phone)->first();
                            if ($dealer_info) {
                                $dealerId = $dealer_info->dealer_id;
                                $csvVINsByDealer[$dealerId][] = $vin;
                            }


                            $inventoryItemFound = false;
                            $newInventoryItemFound = true;

                            foreach ($inventory as $inventoryDetails) {
                                // Check if VIN matches
                                if ($inventoryDetails['vin'] == $vin) {
                                    $inventoryItemFound = true;
                                }

                                // Check if stock matches
                                if ($inventoryDetails['vin'] == $vin) {
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
                                $inventory_added[] = $vin;

                                // $dealer = User::where('phone', $dealer_phone)->first();
                                if ($dealer_info) {
                                    $dealer_id = $dealer_info->dealer_id;
                                    $user_id = $dealer_info->id;
                                } else {
                                    $custom_dealer_id = 5556666;
                                    $dealer_iframe = '';
                                    if (!$dealer_info && !empty($row[2])) {
                                        $user = new User();
                                        $user->dealer_id        = $custom_dealer_id;
                                        $user->name             = $dealer_name;
                                        $user->phone            = $dealer_phone;
                                        $user->address          = $dealer_address;
                                        $user->city             = $dealer_city;
                                        $user->state            = $dealer_state;
                                        $user->dealer_iframe_map = $dealer_iframe;
                                        $user->save();


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

                                dd($dealer_phone);

                                $car_row[] = [
                                    'title' => $title,
                                    'make' => $make,
                                    'model' => $model,
                                    'year' => $year,
                                    'trim' => $trim,
                                    'vin' => $vin,
                                    'stock' => $stock,
                                    'price' => $price,
                                    'miles' => $miles,
                                    'type' => $type,
                                    'dealer_name' => $dealer_name,
                                    'dealer_address' => $dealer_address,
                                    'dealer_city' => $dealer_city,
                                    'dealer_state' => $dealer_state,
                                    'dealer_phone' => $dealer_phone,
                                    'dealer_description' => $dealer_description,
                                    'dealer_zip' => $dealer_zip,
                                    'dealer_detail_url' => $dealer_detail_url,
                                    'dealer_image_url' => $dealer_image_url,
                                    'local_image_url' => $local_image_url,
                                    'engine_description' => $engine_description,
                                    'transmission' => $transmission,
                                    'body_description' => $body_description,
                                    'fuel_description' => $fuel_description,
                                    'drivetrain' => $driveTrain,
                                    'mpg_city' => $mpgCity,
                                    'mpgHighway' => $mpgHighway,
                                    'exterior_color' => $extColor,
                                    'stockDate' => $stockDate,
                                    'batch_no' => $batchNo,
                                    'dealer_link' => $dealerLink,
                                    'monthly_pay' => $monthlyPay,
                                    'interiroColor' => $interiroColor,
                                ];
                            }
                            dd($car_row);
                                $this->createTmpInventory($car_row, $userId, $dealer_id, $user_id);

                            }
                    //     }
                    // }
                    // dd($existingInventoryByDealer, $inventory);
                    // $formattedDate = date('Y-m-d', strtotime($stockDate));

                    // $monthly_payment = 0;
                    // try {
                    //     if (!($row[17] == 'Contact for Price')) {
                    //         $payment_price =  $row[17];
                    //         $interest_rate = (5.82 / 100);
                    //         $down_payment_percentage = 10 / 100;
            
                    //         $down_payment = $payment_price * $down_payment_percentage;
                    //         $loan_amount = $payment_price - $down_payment;
                    //         $calculateMonthValue = 72;
                    //         $monthly_interest_rate = $interest_rate / 12;
            
                    //         $monthly_payment = ceil(($loan_amount * $monthly_interest_rate) / (1 - pow(1 + $monthly_interest_rate, -$calculateMonthValue)));
                    //     }
                    // } catch (\Throwable $th) {
                    //     $monthly_payment = 0;
                    // }

                    $lowerString = strtolower($body_description);
                    $car_body = $this->determineCarBody($lowerString);

                    $vehicleMakeData = VehicleMake::where('make_name', $make)->first();
                    $inventory = TmpInventories::where('vin',$vin)->first();

                    if(!$inventory){
                        if (!$vehicleMakeData) {
                            $inventory_make_data = VehicleMake::create([
                                'make_name' => $make,
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
            
                        // $latitude = $row[42];
                        // $longitude = $row[43];
            
                        if (!$dealer_id) {
                            $dealer_id = $row[0];  // Default value if dealer_id is not set
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
                                // 'created_date' => $row[32],
                                'created_date' => formattedDate,
                                'batch_no' => $row[33],
                                'stock_date_formated' => $formattedDate,
                                'user_id' => $id,
                                'payment_price' => $monthly_payment,
                                'body_formated' => $car_body,
                                'is_feature' => 0,
                                'status' => 1,
                            ];
            
                            // Insert into Inventory
                            // $inventory_table_data = TmpInventories::create($inventoryData);
            
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
                    // }

                    dd($formattedDate, $car_body, $vehicleMakeData, $inventory);
                    $cars[] = [
                        'title' => $title,
                        'make' => $make,
                        'model' => $model,
                        'year' => $year,
                        'trim' => $trim,
                        'vin' => $vin,
                        'stock' => $stock,
                        'price' => $price,
                        'miles' => $miles,
                        'type' => $type,
                        'dealer_name' => $dealer_name,
                        'dealer_address' => $dealer_address,
                        'dealer_city' => $dealer_city,
                        'dealer_state' => $dealer_state,
                        'dealer_phone' => $dealer_phone,
                        'dealer_description' => $dealer_description,
                        'dealer_zip' => $dealer_zip,
                        'dealer_detail_url' => $dealer_detail_url,
                        'dealer_image_url' => $dealer_image_url,
                        'local_image_url' => $local_image_url,
                        'engine_description' => $engine_description,
                        'transmission' => $transmission,
                        'body_description' => $body_description,
                        'fuel_description' => $fuel_description,
                        'drivetrain' => $driveTrain,
                        'mpg_city' => $mpgCity,
                        'mpgHighway' => $mpgHighway,
                        'exterior_color' => $extColor,
                        'stockDate' => $stockDate,
                        'batch_no' => $batchNo,
                        'dealer_link' => $dealerLink,
                        'monthly_pay' => $monthlyPay,
                        'interiroColor' => $interiroColor,
                    ];
                }
                dd($cars);
                dd($cars[380]);
                fclose($handle);
            } else {
                $this->error('Failed to open the CSV file.');
            }
        // } else {
        //     $this->error('There was an error downloading the file');
        // }

        // Close the connection
        // ftp_close($conn_id);
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

    private function processCSV($filePath)
    {
        $this->info('Processing CSV file: ' . $filePath);

        // Open the file
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Log or print the data for debugging
                $this->info('Row: ' . implode(', ', $data));
            }
            fclose($handle);
        } else {
            $this->error('Error opening the file');
        }
    }

    // public function handle()
    // {
    //     $ip_address = '64.209.142.168';

    //     $username = 'localcar_homenet';
    //     $password = '6n65AraH';

    //     // Create an SFTP connection to the remote server
    //     $sftp = new SFTP($ip_address);
    //     if (!$sftp->login($username, $password)) {
    //         dd('SFTP connection failed. Error: ' . $sftp->getLastSFTPError());
    //     } else {
    //         dd('SFTP connection succeeded!');
    //     }
        
    //     dd($sftp);
    //     // return 'ok aichi to beshi bujos ken';
    //     // Define the IP address of the remote server

    //     $remote_path = '/homenetauto.csv';

    //     $csv_url = "https://{$username}:{$password}@{$ip_address}{$remote_path}";

    //     // Download the CSV file
    //     if ($sftp->get($remote_path, $csv_url)) {
    //     $this->info('CSV file downloaded successfully.');
    //     } else {
    //     $this->error('Failed to download CSV file.');
    //     }

    //     // Define the local path to save the downloaded file
    //     // $localPath = storage_path('app/DownloadCsv/file.csv');
    //     $localPath = storage_path("public/uploads/import/{$username}_file.csv");

    //     // Download the file
    //     file_put_contents($localPath, file_get_contents($csv_url));

    //     $this->info('File downloaded successfully.');
    //     return Command::SUCCESS;
    // }
}
