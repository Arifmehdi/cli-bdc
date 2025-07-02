<?php

namespace App\Console\Commands;

use App\Models\SoldInventories;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteSoldInventories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:move-sold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move sold vehicles to sold_inventories and delete from main_inventories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = 1000; // Adjust batch size if needed
    
        DB::beginTransaction();
        try {
            // First, get all IDs for sold inventory
            $soldIds = DB::table('main_inventories')
                ->where('inventory_status', 'Sold')
                ->pluck('id')
                ->toArray();
            
            // Process in chunks to avoid memory issues
            foreach (array_chunk($soldIds, $batchSize) as $chunk) {
                // Get full records with join for this chunk
                $inventories = DB::table('main_inventories')
                    ->leftJoin('additional_inventories', 'main_inventories.id', '=', 'additional_inventories.main_inventory_id')
                    ->select([
                        'main_inventories.id',
                        'main_inventories.deal_id',
                        'main_inventories.zip_code',
                        'main_inventories.latitude',
                        'main_inventories.longitude',
                        'main_inventories.vehicle_make_id',
                        'main_inventories.title',
                        'main_inventories.year',
                        'main_inventories.make',
                        'main_inventories.model',
                        'main_inventories.vin',
                        'main_inventories.price',
                        'main_inventories.price_rating',
                        'main_inventories.miles',
                        'main_inventories.type',
                        'main_inventories.trim',
                        'main_inventories.stock',
                        'main_inventories.transmission',
                        'main_inventories.engine_details',
                        'main_inventories.fuel',
                        'main_inventories.drive_info',
                        'main_inventories.mpg',
                        'main_inventories.mpg_city',
                        'main_inventories.mpg_highway',
                        'main_inventories.exterior_color',
                        'main_inventories.interior_color',
                        'main_inventories.created_date',
                        'main_inventories.stock_date_formated',
                        'main_inventories.user_id',
                        'main_inventories.payment_price',
                        'main_inventories.body_formated',
                        'main_inventories.is_feature',
                        'main_inventories.is_lead_feature',
                        'main_inventories.package',
                        'main_inventories.payment_date',
                        'main_inventories.active_till',
                        'main_inventories.featured_till',
                        'main_inventories.is_visibility',
                        'main_inventories.batch_no',
                        'main_inventories.status',
                        'main_inventories.image_count',
                        'main_inventories.inventory_status',
                        'additional_inventories.detail_url'
                    ])
                    ->whereIn('main_inventories.id', $chunk)
                    ->get()
                    ->toArray();
                
                // Convert to array for insert
                $soldData = [];
                foreach ($inventories as $inventory) {
                    $sold_vin = SoldInventories::where('vin', $inventory->vin)->first();
                
                    // If VIN exists in the sold_inventories table, update the record
                    if ($sold_vin) {
                        // Update the existing record in sold_inventories
                        DB::table('sold_inventories')
                            ->where('vin', $inventory->vin)
                            ->update([
                                'deal_id' => $inventory->deal_id,
                                'zip_code' => $inventory->zip_code,
                                'latitude' => $inventory->latitude,
                                'longitude' => $inventory->longitude,
                                'vehicle_make_id' => $inventory->vehicle_make_id,
                                'title' => $inventory->title,
                                'year' => $inventory->year,
                                'make' => $inventory->make,
                                'model' => $inventory->model,
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
                                'image_count' => $inventory->image_count,
                                'inventory_status' => $inventory->inventory_status,                              
                                // Add other fields you need to update
                            ]);
                    } else {
                        // If VIN doesn't exist, prepare data for insertion
                        $soldData[] = (array) $inventory;
                    }
                }
                
                // Insert into sold_inventories
                if (!empty($soldData)) {
                    DB::table('sold_inventories')->insert($soldData);
                }
                
                // Delete processed records
                DB::table('main_inventories')->whereIn('id', $chunk)->delete();
            }
            
            DB::commit();
            $this->info('Sold inventories moved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
