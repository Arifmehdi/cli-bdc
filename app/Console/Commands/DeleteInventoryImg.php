<?php

namespace App\Console\Commands;

use App\Models\SoldInventories;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteInventoryImg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-inventory-img';

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
         $destination = public_path('listing');
     
         // Process in chunks to optimize memory usage
         SoldInventories::where('img_exist', 1)
             ->chunk(500, function ($soldInventories) use ($destination) {
                 $vinsToUpdate = [];
     
                 foreach ($soldInventories as $soldInventory) {
                     $folderPath = $destination . '/' . $soldInventory->vin;
     
                     if (File::exists($folderPath) && File::isDirectory($folderPath)) {
                         File::deleteDirectory($folderPath); // Delete the folder
                         $vinsToUpdate[] = $soldInventory->vin; // Collect VINs to update
                         dump("Deleted folder: " . $folderPath);
                     } else {
                         dump("Folder not found: " . $folderPath);
                     }
                 }
     
                 // Bulk update for img_exist = 0
                 if (!empty($vinsToUpdate)) {
                     SoldInventories::whereIn('vin', $vinsToUpdate)->update(['img_exist' => 0]);
                 }
             });
     
         dd('Folder deletion process completed.');
     }

    //  it is ok but noy use chunk 
    //  public function handle()
    // {
    //     $sold_inventories_vins = SoldInventories::where('img_exist',1)->pluck('vin');
    //     $destination = public_path('listing');

    //     foreach ($sold_inventories_vins as $vin) {
    //         $folderPath = $destination . '/' . $vin;

    //         if (File::exists($folderPath) && File::isDirectory($folderPath)) {
    //             File::deleteDirectory($folderPath); // Delete the folder and its contents
    //             // update img exist 
    //         // Find the inventory and update img_exist if found
    //             $sold_inventory = SoldInventories::where('vin', $vin)->first();
    //             if ($sold_inventory) {
    //                 $sold_inventory->update(['img_exist' => 0]);
    //             }
    //                 dump("Deleted folder: " . $folderPath);
    //         } else {
    //             dump("Folder not found: " . $folderPath);
    //         }
    //     }

    //     dd('Folder deletion process completed.');
    // }


//     public function handle()
//     {
//         $sold_inventories_vins = SoldInventories::pluck('vin');
//         $destination = public_path('listing');

//         foreach($sold_inventories_vins as $vin){
//             $destination_with_vin_file = $destination.'/'.$vin;
//             if(File::exists($destination_with_vin_file)){
//                 // $files = File::files($destination_with_vin_file);
//                 // foreach ($files as $file) {
//                 //     dump($file->getPathname()); // Full file path
//                 //     // dd($file->getPathname());
//                 // }

//                 $file_delete = File::delete($destination_with_vin_file);
//                 dump("Deleted file: " . $file_delete);
//             }
//             // else{
//             //     dd('file not found ');
//             // }
//         }
//     }
}
