<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use phpseclib3\Net\SFTP;

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

        $ftp_server = env('FTP_SERVER', '64.209.142.168');
        $ftp_user = env('FTP_USER', 'localcar_homenet');
        $ftp_pass = env('FTP_PASS', '6n65AraH');
        $ftp_file = env('FTP_FILE', 'homenetauto.csv');
        Cache::put('ftp_progress', 'Connecting to FTP server...', now()->addMinutes(5));
    
        $conn_id = ftp_connect($ftp_server);
        if (!$conn_id) {
            Cache::put('ftp_progress', 'Failed to connect to FTP server.', now()->addMinutes(5));
            return;
        }
        Cache::put('ftp_progress', 'Connected to FTP server successfully.', now()->addMinutes(5));

        $login_result = ftp_login($conn_id, $ftp_user, $ftp_pass);
        if (!$login_result) {
            Cache::put('ftp_progress', 'Failed to log in to FTP server.', now()->addMinutes(5));
            ftp_close($conn_id);
            return;
        }
        Cache::put('ftp_progress', 'Logged into FTP server successfully.', now()->addMinutes(5));

        $root_contents = ftp_nlist($conn_id, ".");
        if ($root_contents === false) {
            Cache::put('ftp_progress', 'Failed to list FTP directory contents.', now()->addMinutes(5));
            ftp_close($conn_id);
            return;
        }
        Cache::put('ftp_progress', 'Root directory contents: ' . implode(', ', $root_contents), now()->addMinutes(5));

        if (!in_array($ftp_file, $root_contents)) {
            Cache::put('ftp_progress', 'File does not exist on FTP server.', now()->addMinutes(5));
            ftp_close($conn_id);
            return;
        }
        Cache::put('ftp_progress', 'File exists on FTP server.', now()->addMinutes(5));
    
        $local_directory = public_path('uploads/import');
        $date = date('mdY');
        $local_file = $local_directory . '/' . 'server_'.$date . '_homenetauto.csv';
    
        if (!ftp_get($conn_id, $local_file, $ftp_file, FTP_BINARY)) {
            Cache::put('ftp_progress', 'Failed to download the file.', now()->addMinutes(5));
            ftp_close($conn_id);
            return;
        }
        Cache::put('ftp_progress', 'File downloaded successfully.', now()->addMinutes(5));
        ftp_close($conn_id);

        return $local_file;
        // dd($root_contents,$login_result, $conn_id, $ftp_server, $ftp_user, $ftp_pass, $ftp_file);

        // dd('kokhonoe hote dea hobe na ');
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
                return response()->json([
                    'progress' => Cache::get('ftp_progress', 'No updates yet.')
                ]);
                
            } else {
                $this->error('Failed to open the CSV file.');
            }

        // Close the connection
        // ftp_close($conn_id);
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
