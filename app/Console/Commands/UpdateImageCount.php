<?php

namespace App\Console\Commands;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class UpdateImageCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-image-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the image_count field for all inventories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image count update...');
        $in =  MainInventory::select('id', 'deal_id')
        ->with(['additionalInventory' => function ($query) {
            $query->select('main_inventory_id', 'local_img_url');
        }])
        // ->whereNull('image_count')
        // ->orWhere('image_count', 0)
        ->chunk(1000, function ($inventories) {
            foreach ($inventories as $inventory) {
                $imagePaths = explode(',', $inventory->additionalInventory->local_img_url ?? '');
                $folderPath = public_path('/');
                $validImages = array_filter($imagePaths, fn($img) => file_exists($folderPath . ltrim($img, '/')));
    
                // Count the images and update only if necessary
                $imageCount = count($validImages);
                if ($inventory->image_count !== $imageCount) {
                    MainInventory::where('id', $inventory->id)->update(['image_count' => $imageCount]);
                }
            }
        });
    }
}
