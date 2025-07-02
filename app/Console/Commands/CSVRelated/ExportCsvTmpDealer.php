<?php

namespace App\Console\Commands\CSVRelated;

use App\Models\CsvTmpDealer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportCsvTmpDealer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export-csv-tmp-dealer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export CsvTmpDealer data to a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch all CsvTmpDealer records
        $dealers = CsvTmpDealer::all();

        // Define the directory and file path where the CSV file will be saved
        $directory = storage_path('app/exports');
        $csvFilePath = $directory . '/dealers.csv';

        // Check if the directory exists, and create it if it doesn't
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    // Check if the directory exists, and create it if it doesn't
    if (!File::exists($directory)) {
        File::makeDirectory($directory, 0755, true);
    }

    // Open the file for writing
    $file = fopen($csvFilePath, 'w');

    // Define the CSV headers
    $headers = ['Name', 'Full Address', 'Address', 'City', 'State', 'Zip Code', 'Phone', 'Dealer Homepage'];

    // Write the headers to the CSV
    fputcsv($file, $headers);

    // Loop through each dealer and write their data to the CSV
    foreach ($dealers as $dealer) {
        $row = [
            $dealer->name,
            $dealer->full_address,
            $dealer->address,
            $dealer->city,
            $dealer->state,
            $dealer->zip_code,
            $dealer->phone,
            $dealer->dealer_homepage,
        ];

        // Write the row to the CSV file
        fputcsv($file, $row);
    }

    // Close the CSV file
    fclose($file);

    // Output success message
    $this->info('CSV export completed successfully!');
    }
}
