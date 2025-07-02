<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyInventoriesToNewTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:copy-inventories-to-new-tables';
    protected $signature = 'inventories:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';
    protected $description = 'Copy data from inventories table to main_inventories and additional_inventories';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to copy data from inventories to main_inventories and additional_inventories...');
        

        DB::transaction(function () {
            // Fetch all inventories
            // $inventories = DB::table('inventories')->get();
            $inventories = Inventory::with('priceHistory')->get();

            // $priceHistories = $inventories->map(function ($inventory) {
            //     return $inventory->priceHistory; // Safely access the first element or return null if it doesn't exist
            // });


            foreach ($inventories as $inventory) {

                // dd($inventory->priceHistory);
                // Insert into main_inventories
                $mainInventoryId = DB::table('main_inventories')->insertGetId([
                    'deal_id' => $inventory->deal_id,
                    'zip_code' => $inventory->zip_code,
                    'latitude' => $inventory->latitude,
                    'longitude' => $inventory->longitude,
                    'vehicle_make_id' => $inventory->vehicle_make_id,
                    'title' => $inventory->title,
                    'year' => $inventory->year,
                    'make' => $inventory->make,
                    'model' => $inventory->model,
                    'vin' => $inventory->vin,
                    'price' => $inventory->price,
                    'price_rating' => $inventory->price_rating,
                    'miles' => $inventory->miles,
                    'type' => $inventory->type,
                    'trim' => $inventory->trim,
                    'stock' => $inventory->stock,
                    'transmission' => $inventory->transmission,
                    'engine_details' => $inventory->engine_details,
                    'fuel' => $inventory->fuel,
                    'drive_info' => $inventory->drive_info,
                    'mpg' => $inventory->mpg,
                    'mpg_city' => $inventory->mpg_city,
                    'mpg_highway' => $inventory->mpg_highway,
                    'exterior_color' => $inventory->exterior_color,
                    'interior_color' => $inventory->interior_color,
                    'created_date' => $inventory->created_date,
                    'stock_date_formated' => $inventory->stock_date_formated,
                    'user_id' => $inventory->user_id,
                    'payment_price' => $inventory->payment_price,
                    'body_formated' => $inventory->body_formated,
                    'is_feature' => $inventory->is_feature,
                    'is_lead_feature' => $inventory->is_lead_feature,
                    'package' => $inventory->package,
                    'payment_date' => $inventory->payment_date,
                    'active_till' => $inventory->active_till,
                    'featured_till' => $inventory->featured_till,
                    'is_visibility' => $inventory->is_visibility,
                    'batch_no' => $inventory->batch_no,
                    'status' => $inventory->status,
                    'inventory_status' => $inventory->inventory_status,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                    'deleted_at' => $inventory->deleted_at,
                ]);

                // Insert into additional_inventories
                DB::table('additional_inventories')->insert([
                    'main_inventory_id' => $mainInventoryId,
                    'detail_url' => $inventory->detail_url,
                    'img_from_url' => $inventory->img_from_url,
                    'local_img_url' => $inventory->local_img_url,
                    'vehicle_feature_description' => $inventory->vehicle_feature_description,
                    'vehicle_additional_description' => $inventory->vehicle_additional_description,
                    'seller_note' => $inventory->seller_note,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ]);

                $priceHistoryData = $inventory->priceHistory;

                if (!$priceHistoryData->isEmpty()) {
                    foreach ($priceHistoryData as $priceHistory) {
                        DB::table('main_price_history')->insert([
                            'main_inventory_id' => $mainInventoryId,
                            'change_date' => $priceHistory->change_date,
                            'change_amount' => $priceHistory->change_amount,
                            'amount' => $priceHistory->amount,
                            'status' => $priceHistory->status,
                            'created_at' => $inventory->created_at,
                            'updated_at' => $inventory->updated_at,
                        ]);
                    }
                }

            }
        });

        $this->info('Data successfully copied!');
    }
}
