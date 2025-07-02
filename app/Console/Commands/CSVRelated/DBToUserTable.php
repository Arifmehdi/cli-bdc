<?php

namespace App\Console\Commands\CSVRelated;

use App\Models\CsvTmpDealer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DBToUserTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db-to-dealer';

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
        // Get the dealers from CsvTmpDealer
        $dealers = CsvTmpDealer::get();
    
        // Initialize arrays for tracking results
        $updatedDealers = [];
        $notImportedDealers = [];
        $importedDealers = [];
        $bachNoData = User::max('batch_no') ?? 10;
        $batchNo = (int)$bachNoData + 1; // Example batch number, adjust accordingly

        // Loop through the dealers data
        foreach ($dealers as $dealer) {
    
            // Retrieve the user by name and zip code
            $userInfo = User::where('name', $dealer->name)
                            ->where('zip', $dealer->zip_code)
                            ->first();
    
            // Check if a user was found
            if ($userInfo) {
                // Compare fields for changes
                $changes = [];
                $updatableFields = [
                    'address', 'brand_website', 'dealer_full_address', 'phone',
                    'state', 'city', 'zip', 'email', 'dealer_website', 'rating', 'review'
                ];
    
                foreach ($updatableFields as $field) {
                    if (($dealer->$field ?? null) && $userInfo->$field !== $dealer->$field) {
                        $changes[$field] = [
                            'old' => $userInfo->$field,
                            'new' => $dealer->$field
                        ];
                        $userInfo->$field = $dealer->$field;
                    }
                }
    
                if (!empty($changes)) {
                    $userInfo->save();
                    $updatedDealers[] = "Dealer ID: {$userInfo->dealer_id} - Name: {$userInfo->name} - Changes: " . json_encode($changes);
                } else {
                    $notImportedDealers[] = "{$dealer->name}, {$dealer->zip_code} - No changes detected";
                }
    
                $this->info("Found user: " . $userInfo->name . " with zip code: " . $userInfo->zip);
                continue;  // Continue to the next dealer if user was found
            } else {
                // If user doesn't exist, create a new user
                $password = Str::random(8);
    
                $latestDealerData = User::max('dealer_id') ?? 1000; // Default to 1000 if no data exists
                $latestDealerId = is_numeric($latestDealerData) ? (int)$latestDealerData + 1 : 1001;
                $role = Role::where('name', 'dealer')->first();
    
                // Create a new dealer
                $newDealer = User::create([
                    'name' => $dealer->name,
                    'address' => $dealer->address ?? null,
                    'dealer_id' => $latestDealerId,
                    'brand_website' => $dealer->brand_website ?? $dealer->dealer_homepage ?? null,
                    'dealer_full_address' => $dealer->full_address ?? null,
                    'phone' => $dealer->phone ?? null,
                    'state' => $dealer->state ?? null,
                    'city' => $dealer->city ?? null,
                    'zip' => $dealer->zip_code,
                    'email' => $dealer->email ?? Str::random(12) . '@bestdreamcar.com',
                    'password' => Hash::make($password),
                    'dealer_website' => $dealer->dealer_website ?? null,
                    'role_id' => $role->id ?? null,
                    'rating' => $dealer->rating ?? null,
                    'review' => $dealer->review ?? null,
                    'import_type' => 1,
                    'batch_no' => $batchNo,
                ]);
    
                // Assign role
                $newDealer->assignRole($role);
    
                // Store imported dealer
                $importedDealers[] = $newDealer->dealer_id . ' - ' . $dealer->name;
    
                $this->info("No user found for name: {$dealer->name} and zip code: {$dealer->zip_code}");
            }
        }
    
        // You can log or handle the result arrays as needed
        // For example, you can log the arrays of updated or imported dealers
        $this->info("Updated Dealers: " . implode(', ', $updatedDealers));
        $this->info("Not Imported Dealers: " . implode(', ', $notImportedDealers));
        $this->info("Imported Dealers: " . implode(', ', $importedDealers));
    }
    
}
