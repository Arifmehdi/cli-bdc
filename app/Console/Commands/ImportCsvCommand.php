<?php

namespace App\Console\Commands;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\AdditionalInventory;
use App\Models\LocationZip;
use App\Models\MainInventory;
use App\Models\MainPriceHistory;
use App\Models\User;
use App\Models\VehicleMake;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class ImportCsvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV files from a folder and move them after successful import';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourcePath = public_path('uploads/ftp_csv_files');
        $destinationPath = public_path('uploads/processed_csv');
    
        // Ensure the directories exist
        $this->ensureDirectories([$sourcePath, $destinationPath]);
    
        $csvFiles = glob($sourcePath . '/*.csv');
    
        if (empty($csvFiles)) {
            $this->info("No CSV files found.");
            return;
        }
    
        foreach ($csvFiles as $file) {
            $this->info("Processing: " . basename($file));
    
            try {
                // Process file in chunks
                $this->processFileInChunks($file);
                rename($file, $destinationPath . '/' . basename($file));
                $this->info("File moved to processed folder: " . basename($file));
            } catch (\Exception $e) {
                $this->error("Error importing file: " . $e->getMessage());
            }
        }
    }
    
    private function ensureDirectories($directories)
    {
        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("Directory created: $dir");
            }
        }
    }
    
    private function processFileInChunks($filePath)
    {
        // Get the latest batch number
        $latestBatchNo = MainInventory::latest('batch_no')->value('batch_no');
        $batch_no = $latestBatchNo ? $latestBatchNo + 1 : 101;
    
        $imported = [];
        $updated = [];
        $skipped = [];
    
        // Use Laravel Excel's chunk reading
        Excel::import(new class($batch_no, $filePath, $imported, $updated, $skipped, $this) implements ToCollection, WithChunkReading {
            private $batch_no;
            private $filePath;
            private $imported;
            private $updated;
            private $skipped;
            private $handler;
    
            public function __construct($batch_no, $filePath, $imported, $updated, $skipped, $handler)
            {
                $this->batch_no = $batch_no;
                $this->filePath = $filePath;
                $this->imported = $imported; // Assign without reference
                $this->updated = $updated;   // Assign without reference
                $this->skipped = $skipped;   // Assign without reference
                $this->handler = $handler;
            }
    
            public function collection(Collection $rows)
            {
                if ($rows->isEmpty()) {
                    return;
                }
    
                // Get headers from the first row
                $headers = array_map('strtolower', array_map('trim', array_keys($rows->first()->toArray())));
    
                // Process each row in the chunk
                foreach ($rows as $index => $row) {
                    try {
                        $rowData = [];
                        foreach ($headers as $key) {
                            $rowData[$key] = $row[$key] ?? null;
                        }
    
                        $result = $this->handler->processRow($rowData, $this->batch_no, $this->filePath);
    
                        if ($result === 'imported') {
                            $this->imported[] = $row; // Append to the array
                        } elseif ($result === 'updated') {
                            $this->updated[] = $row; // Append to the array
                        } else {
                            $this->skipped[] = $row; // Append to the array
                        }
    
                        // Free up memory
                        unset($rowData);
                    } catch (\Exception $e) {
                        $this->handler->error("Error processing row $index: " . $e->getMessage());
                        $this->skipped[] = $row; // Append to the array
                    }
    
                    // Manually trigger garbage collection occasionally
                    if ($index % 50 === 0) {
                        gc_collect_cycles();
                    }
                }
            }
    
            public function chunkSize(): int
            {
                return 100; // Process 100 rows at a time
            }
        }, $filePath);
    
        // Save results to files
        $dateData = now()->format('Ymd_His');
        $this->saveToFile('main_command_insert_' . $dateData . '.txt', $imported);
        $this->saveToFile('main_command_update_' . $dateData . '.txt', $updated);
        $this->saveToFile('main_command_notimport_' . $dateData . '.txt', $skipped);
    
        // Return summary
        return [
            'imported' => count($imported),
            'updated' => count($updated),
            'skipped' => count($skipped),
        ];
    }
    
    private function processRow($rowData, $batch_no, $filePath)
    {
        // Extract user ID
        $userId = Auth::user()->id ?? '1';
    
        // Extract dealer data and check if dealer exists
        $dealerData = $this->extractDealerData($rowData);
        $dealer = $this->findOrCreateDealer($dealerData);
    
        $dealer_id = $dealer->dealer_id;
        $user_id = $dealer->id;
    
        // Extract vehicle data
        $vehicleData = $this->extractVehicleData($rowData, $batch_no);
    
        // Get lat/long for zip code (with caching)
        $latlongData = $this->getLatLong($dealerData['zip']);
    
        // Process vehicle make
        $vehicleMakeData = $this->processVehicleMake($vehicleData['make']);
    
        // Process images asynchronously or with a limit
        $localImagePaths = $this->processImagesOptimized($vehicleData['all_images'], $vehicleData['vin']);
    
        // Calculate additional fields
        $formattedDate = now()->format('Y-m-d');
        $monthlyPayment = $this->calculateMonthlyPayment($vehicleData['price']);
        $carBody = $this->determineCarBody(strtolower($vehicleData['body_type']));
    
        // Check if inventory exists and update or create
        $existingInventory = MainInventory::where('vin', $vehicleData['vin'])->exists();
    
        // Use transaction to ensure data consistency
        DB::beginTransaction();
        try {
            $this->insertOrUpdateInventoryOptimized(
                $userId, $dealer_id, $user_id, $vehicleData, $latlongData,
                $localImagePaths, $vehicleMakeData->id, $formattedDate,
                $monthlyPayment, $carBody
            );
            DB::commit();
    
            return $existingInventory ? 'updated' : 'imported';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing VIN {$vehicleData['vin']}: " . $e->getMessage());
            return 'skipped';
        }
    }
    

    
    private function extractDealerData($rowData)
    {
        return [
            'dealer_id' => $rowData['dealer id'] ?? $rowData['customer id'] ?? null,
            'name' => $rowData['dealer name'] ?? null,
            'phone' => preg_replace('/\D/', '', $rowData['dealer sales phone'] ?? ''),
            'full_address' => $rowData['dealer address'] ?? null,
            'address' => $rowData['dealer street'] ?? null,
            'city' => $rowData['dealer city'] ?? null,
            'region' => $rowData['dealer region'] ?? null,
            'zip' => $rowData['dealer zip code'] ?? null,
            'rating' => $rowData['dealer rating'] ?? null,
            'review' => $rowData['dealer review'] ?? null,
            'website' => $rowData['dealer website'] ?? null,
            'brand_website' => $rowData['brand website'] ?? null,
        ];
    }
    
    private function findOrCreateDealer($dealerData)
    {
        $dealer = User::where('name', $dealerData['name'])
                      ->where('zip', $dealerData['zip'])
                      ->first();
        
        if ($dealer) {
            // Update dealer rating/review if null
            if ($dealer->rating === null && $dealerData['rating']) {
                $dealer->rating = $dealerData['rating'];
                $dealer->review = $dealerData['review'];
                $dealer->save();
            }
            return $dealer;
        }
        
        // Create new dealer
        $user = new User();
        $user->dealer_id = $dealerData['dealer_id'];
        $user->name = $dealerData['name'];
        $user->phone = $dealerData['phone'];
        $user->dealer_full_address = $dealerData['full_address'];
        $user->rating = $dealerData['rating'];
        $user->review = $dealerData['review'];
        $user->address = $dealerData['address'];
        $user->dealer_website = $dealerData['website'];
        $user->brand_website = $dealerData['brand_website'];
        $user->city = $dealerData['city'];
        $user->state = $dealerData['region'];
        $user->zip = $dealerData['zip'];
        $user->status = 3;
        $user->dealer_iframe_map = null;
        $user->save();
        
        // Assign dealer role
        $role = Role::where('name', 'dealer')->first();
        if ($role) {
            $user->assignRole($role);
        }
        
        return $user;
    }
    
    private function processVehicleMake($make)
    {
        // Use firstOrCreate to avoid duplication
        return VehicleMake::firstOrCreate(
            ['make_name' => $make],
            ['status' => 1, 'is_read' => 0]
        );
    }
    
    // Optimized image processing
    private function processImagesOptimized($imageString, $vin)
    {
        $imageUrls = array_filter(explode(',', $imageString));
        $imageCount = min(count($imageUrls), 5); // Limit to 5 images
        $localImagePaths = [];
        
        $vinDir = public_path('listing/' . $vin);
        $this->ensureDirectoryExists($vinDir);
        
        // Just create the paths without downloading - defer actual downloads
        for ($index = 1; $index <= $imageCount; $index++) {
            $fileName = sprintf('%s_%02d.jpg', $vin, $index);
            $localPath = 'listing/' . $vin . '/' . $fileName;
            $localImagePaths[] = $localPath;
            
            // Schedule image download with queue if needed
            // ImageDownload::dispatch($imageUrls[$index-1], $vinDir.'/'.$fileName);
        }
        
        return implode(',', $localImagePaths);
    }
    
    // Optimized version of insertOrUpdateInventory
    private function insertOrUpdateInventoryOptimized($userId, $dealer_id, $user_id, $vehicleData, $latlongData, $localImagePaths, $vehicleMakeId, $formattedDate, $monthlyPayment, $carBody)
    {
        // First handle MainInventory
        $mainInventory = MainInventory::updateOrCreate(
            ['vin' => $vehicleData['vin']],
            [
                'deal_id' => $user_id,
                'zip_code' => $vehicleData['dealer_zip'],
                'latitude' => $latlongData['latitude'] ?? null,
                'longitude' => $latlongData['longitude'] ?? null,
                'vehicle_make_id' => $vehicleMakeId,
                'title' => $vehicleData['titles'],
                'year' => $vehicleData['year'],
                'make' => $vehicleData['make'],
                'model' => $vehicleData['model'],
                'price' => $vehicleData['price'],
                'price_rating' => $vehicleData['price_rating'],
                'miles' => $vehicleData['milage'],
                'type' => $vehicleData['type'],
                'trim' => $vehicleData['trim'],
                'stock' => $vehicleData['stock_number'],
                'transmission' => $vehicleData['transmission'],
                'engine_details' => $vehicleData['engine'],
                'fuel' => $vehicleData['fuel'],
                'drive_info' => $vehicleData['drive_train'],
                'mpg' => $vehicleData['avg_mpg'],
                'mpg_city' => $vehicleData['city_mpg_data'],
                'mpg_highway' => $vehicleData['hwy_mpg_data'],
                'exterior_color' => $vehicleData['exterior_color'],
                'interior_color' => $vehicleData['interior_color'],
                'created_date' => $formattedDate,
                'stock_date_formated' => $formattedDate,
                'user_id' => $userId,
                'payment_price' => $monthlyPayment,
                'body_formated' => $carBody,
                'is_feature' => 0,
                'batch_no' => $vehicleData['batch_no_data'],
                'status' => 1,
            ]
        );
        
        // Then handle AdditionalInventory
        AdditionalInventory::updateOrCreate(
            ['main_inventory_id' => $mainInventory->id],
            [
                'main_inventory_id' => $mainInventory->id,
                'detail_url' => $vehicleData['source_url'],
                'img_from_url' => $vehicleData['all_images'],
                'local_img_url' => $localImagePaths,
                'vehicle_feature_description' => $vehicleData['feature'],
                'vehicle_additional_description' => $vehicleData['dealer_option'],
                'seller_note' => $vehicleData['seller_note'],
            ]
        );
        
        // Process price history if exists
        $this->processPriceHistory($vehicleData['price_history_data'] ?? null, $mainInventory->id);
        
        return $mainInventory;
    }
    
    private function processPriceHistory($priceHistoryData, $mainInventoryId)
    {
        if (empty($priceHistoryData)) {
            return;
        }
        
        $entries = explode(',', $priceHistoryData);
        $historyRecords = [];
        
        foreach ($entries as $entry) {
            $parts = array_map('trim', explode(';', $entry));
            
            // Skip invalid entries
            if (count($parts) !== 3 || in_array('----', $parts)) {
                continue;
            }
            
            try {
                $date = Carbon::createFromFormat('m/d/y', $parts[0])->format('Y-m-d');
                $changeAmount = trim($parts[1]);
                
                if (!isset($parts[2])) {
                    continue;
                }
                
                $rawAmount = trim($parts[2]);
                $cleanAmount = str_replace([',', '$', '+', '-'], '', $rawAmount);
                
                if (!is_numeric($cleanAmount)) {
                    continue;
                }
                
                $amount = floatval($cleanAmount);
                
                $historyRecords[] = [
                    'main_inventory_id' => $mainInventoryId,
                    'change_date' => $date,
                    'change_amount' => $changeAmount,
                    'amount' => $amount,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } catch (\Exception $e) {
                // Skip this record on error
                continue;
            }
        }
        
        // Batch insert price history if we have records
        if (!empty($historyRecords)) {
            // Use chunks of 100 for better memory management
            foreach (array_chunk($historyRecords, 100) as $chunk) {
                MainPriceHistory::insert($chunk);
            }
        }
    }

    private function saveToFile($filename, $data)
    {
        $directory = public_path('uploads/command_import_report');
        
        // Ensure the directory exists before saving the file
        $this->ensureDirectoryExists($directory);

        $filePath = $directory . '/' . $filename;

        // Convert data to CSV format
        $content = '';
        foreach ($data as $row) {
            $content .= implode(',', $row) . "\n";
        }

        // Check if file exists before writing
        if (File::exists($filePath)) {
            File::delete($filePath); // Remove old file if it exists
        }

        // Save new file
        file_put_contents($filePath, $content);
    }
}
