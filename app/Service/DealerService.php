<?php

namespace App\Service;

use Illuminate\Support\Collection;

class DealerService
{
    public function getCachedDealers(): Collection
    {
        $cacheFilePath = storage_path('app/cached_dealers.json');

        if (!file_exists($cacheFilePath)) {
            return collect(); // Return an empty collection if the file doesn't exist
        }

        // Read the file in chunks
        $file = fopen($cacheFilePath, 'r');
        $jsonData = '';

        while (!feof($file)) {
            $jsonData .= fread($file, 8192); // Read 8KB at a time
        }

        fclose($file);

        // Decode the JSON data
        $dealers = json_decode($jsonData, true);

        return collect($dealers);
    }
}
