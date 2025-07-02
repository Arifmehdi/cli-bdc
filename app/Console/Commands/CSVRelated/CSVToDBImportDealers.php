<?php

namespace App\Console\Commands\CSVRelated;

use App\Models\CsvTmpDealer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use League\Csv\Reader;
use Exception;

class CSVToDBImportDealers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv-import-dealers {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dealers from a CSV file and insert them into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileName = $this->argument('file');
        $filePath = public_path('uploads/dealer');
        $directory = $filePath.'/'.$fileName;

        // Check if file exists in storage
        if (!File::exists($filePath)) {
            $this->error("File not found: $filePath");
            return;
        }
        $csv = Reader::createFromPath($directory, 'r');
        $csv->setHeaderOffset(0); // Set the first row as header
        
        $errorRows = []; // Array to collect rows with errors

        foreach ($csv as $index => $record) {
            try {
                // Attempt to update or create the record
                CsvTmpDealer::updateOrCreate(
                    ['name' => $record['Name']], // Unique constraint (Modify as needed)
                    [
                        'full_address' => $record['Full Address'] ?? '',
                        'address' => $record['Address'] ?? '',
                        'city' => $record['City'] ?? '',
                        'state' => $record['State'] ?? '',
                        'zip_code' => $record['Zip Code'] ?? '',
                        'phone' => $record['Phone'] ?? '',
                        'dealer_homepage' => $record['Dealer_Homepage'] ?? ''
                    ]
                );

                $this->info("Successfully processed row #$index: {$record['Name']}");

            } catch (Exception $e) {
                // Log the error and record the row index and the error message
                $errorRows[] = [
                    'row' => $record,        // The actual data that caused the error
                    'index' => $index + 1,   // Row index (for reference)
                    'error' => $e->getMessage() // The error message
                ];

                // Log the error in the console
                $this->error("Error processing row #$index: " . $e->getMessage());

                // Continue to the next iteration (bypass the current one)
                continue;
            }
        }
        // After processing all records, log the errors to a file or return them as needed
        if (!empty($errorRows)) {
            // Optionally log to a file or database
            file_put_contents(storage_path('logs/error_log.txt'), print_r($errorRows, true));

            // Or display a summary in the console
            $this->info('Some rows encountered errors during processing.');
            foreach ($errorRows as $error) {
                
                // Append error details to a text file for later inspection
                $errorMessage = "Row #{$error['index']}: " . implode(', ', $error['row']) . " - Error: {$error['error']}\n";
                
                // Save the error message to a log file in the storage folder
                file_put_contents(storage_path('logs/error_log.txt'), $errorMessage, FILE_APPEND);
                $this->line($errorMessage);
            }
        } else {
            $this->info('All rows processed successfully.');
        }

        return 0;
    }
}
