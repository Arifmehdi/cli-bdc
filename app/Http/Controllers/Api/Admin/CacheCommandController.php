<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CacheCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CacheCommandController extends Controller
{
    public function runAll()
    {
        $commands = CacheCommand::all();
        foreach ($commands as $command) {
            Artisan::call($command->command);

            // After successful deletion, update status to 0
            $command->status = 1;
            $command->save();
        }

        return response()->json(['success' => true, 'message' => 'All cache commands executed successfully']);
    }

    public function runSingle($id)
    {
        $command = CacheCommand::findOrFail($id);
        Artisan::call('cache:generate-county ' . $id);
        // After successful deletion, update status to 0
        $command->status = 1;
        $command->save();

        return response()->json(['success' => true, 'message' => 'Command executed successfully']);
    }

    public function deleteAll()
    {
        $commands = CacheCommand::all();
        foreach ($commands as $command) {
            // // 1. Run the Artisan command
            // Artisan::call($command->command);

            // 2. Delete the cache file if it exists
            if ($command->cache_file && file_exists($command->cache_file)) {
                try {
                    unlink($command->cache_file);
                    // After successful deletion, update status to 0
                    $command->status = 0;
                    $command->save();
                } catch (\Exception $e) {
                    // \Log::error("Failed to delete cache file: {$command->cache_file}. Error: {$e->getMessage()}");
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All cache files deleted successfully.'
        ]);
    }

    public function deleteCache($id)
    {
        $command = CacheCommand::findOrFail($id);

        try {
            // Step 1: Remove counties from main_inventories for these zip codes
            $zipCodes = json_decode($command->zip_codes, true) ?: [];

            if (!empty($zipCodes)) {
                DB::table('main_inventories')
                    ->whereIn('zip_code', $zipCodes)
                    ->update(['county' => null]);
            }

            // Step 2: Delete the cache file if it exists
            if ($command->cache_file) {
                // // Use Storage facade instead of file_exists for better consistency
                // if (Storage::exists($command->cache_file)) {
                //     Storage::delete($command->cache_file);
                // }

                // Alternative if you need to handle absolute paths:
                if (file_exists(storage_path('app/' . $command->county.'_county.json'))) {
                    unlink(storage_path('app/' . $command->county.'_county.json'));
                }
            }

            // Step 3: Update the command status
            $command->status = 0;
            $command->delete();

            return response()->json([
                'success' => true,
                'message' => 'County data removed and cache deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete cache: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cache: ' . $e->getMessage()
            ], 500);
        }
    }

}
