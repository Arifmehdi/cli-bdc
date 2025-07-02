<?php

namespace App\Service;

use App\Models\Inventory;
use App\Models\LocationCity;
use App\Models\TmpInventories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Interface\InventoryServiceInterface;
use App\Models\MainInventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class InventoryService implements InventoryServiceInterface
{

    protected $priceRange;

    public function __construct()
    {
        $this->priceRange = app('priceRange');
    }



    // public function __construct(
    //     private FileUploaderServiceInterface $uploader,
    // ) {
    // }

    public function all()
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        // $leads = Inventory::with(['tmp_inventories_car', 'user'])->orderBy('id', 'desc')->get();

        // $inventory = Inventory::query();
        $inventory = MainInventory::query();
        return $inventory;
    }

    public function store(array $visits)
    {
        // abort_if(! auth()->user()->can('hrm_visit_create'), 403, 'Access Forbidden');
        // if (isset($visits['attachments'])) {
        //     $visits['attachments'] = $this->uploader->upload($visits['attachments'], 'uploads/visits/');
        // }
        // $item = Tmp_inventory::create($visits);

        // return $item;
    }

    public function getByUserId(int $userId)
    {
        // $item = Inventory::where('deal_id',$userId)->get();
        $item = MainInventory::where('deal_id', $userId)->get();
        return $item;
    }

    public function update(array $visit, int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_update'), 403, 'Access Forbidden');
        // $item = Tmp_inventory::find($id);
        // if (isset($visit['attachments'])) {
        //     if (isset($visit['attachments']) && ! empty($visit['attachments']) && file_exists('uploads/visits/'.$visit['old_photo']) && $visit['old_photo'] != null) {
        //         unlink(public_path('uploads/visits/'.$visit['old_photo']));
        //     }
        //     $visit['attachments'] = $this->uploader->upload($visit['attachments'], 'uploads/visits/');
        // } else {
        //     // unlink(public_path('uploads/visits/'.$visit['old_photo']));
        //     $visit['attachments'] = null;
        // }
        // $updatedItem = $item->update($visit);

        // return $updatedItem;
    }

    public function find(int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_view'), 403, 'Access Forbidden');
        // $item = Tmp_inventory::find($id);

        // $item = Inventory::find($id);
        $item = MainInventory::find($id);

        return $item;
    }

    //Move To Trash
    public function trash($data)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        // $item = Inventory::find($id);
        // $item->delete($item);

        // $inventory = Inventory::find($data->id);
        $inventory = MainInventory::find($data->id);
        $inventory->delete();
        return $inventory;
    }
    //Bulk Move To Trash
    public function bulkTrash(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            // $item = Inventory::find($id);
            $item = MainInventory::find($id);
            $item->delete($item);
        }

        return $item;
    }

    //Get Trashed Item list
    public function getTrashedItem($id = null)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        if ($id != null) {
            // $item = Inventory::where('deal_id',$id)->onlyTrashed()->orderBy('id', 'desc')->get();
            $item = MainInventory::where('deal_id', $id)->onlyTrashed()->orderBy('id', 'desc')->get();
        } else {

            // $item = Inventory::onlyTrashed()->orderBy('id', 'desc')->get();
            $item = MainInventory::onlyTrashed()->orderBy('id', 'desc')->get();
        }
        return $item;
    }


    //Get Trashed Item list
    public function getTrashedItemWithOptimized($id = null)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        if ($id != null) {
            $item = MainInventory::where('deal_id', $id)->onlyTrashed()->orderBy('id', 'desc')->get();
        } else {
            $item = MainInventory::onlyTrashed()->orderBy('id', 'desc')->get();
        }
        return $item;
    }

    //Permanent Delete
    public function permanentDelete(int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        // $item = Inventory::onlyTrashed()->find($id);
        $item = MainInventory::onlyTrashed()->find($id);
        $item->forceDelete();
        return $item;
    }


    //Bulk Permanent Delete
    public function bulkPermanentDelete(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            // $item = Inventory::onlyTrashed()->find($id);
            $item = MainInventory::onlyTrashed()->find($id);
            $item->forceDelete($item);
        }

        return $item;
    }


    //Bulk Active function

    public function bulkActive(array $ids)
    {
        foreach ($ids as $id) {
            // $inventory = Inventory::find($id);
            $inventory = MainInventory::find($id);
            $inventory->is_visibility = '1';
            $inventory->save();
        }
    }

    //Bulk Inactive function

    public function bulkInactive(array $ids)
    {
        foreach ($ids as $id) {
            // $inventory = Inventory::find($id);
            $inventory = MainInventory::find($id);
            $inventory->is_visibility = '0';
            $inventory->save();
        }
    }

    //Restore Trashed Item
    public function restore(int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        $item = Inventory::withTrashed()->find($id)->restore();
        return $item;
    }


    //Bulk Restore Trashed Item
    public function bulkRestore(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            // $item = Inventory::withTrashed()->find($id);
            $item = MainInventory::withTrashed()->find($id);
            $item->restore($item);
        }

        return $item;
    }


    //Get Row Count
    public function getRowCount($id = null)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        if ($id != null) {
            // $count = Inventory::where('deal_id',$id)->count();
            $count = MainInventory::where('deal_id', $id)->count();
        } else {
            // $count = Inventory::all()->count();
            $count = MainInventory::all()->count();
        }

        return $count;
    }

    public function getRowCountOptimized($request, $id = null)
    {
        // Use caching to avoid repeated database queries
        $cacheKey = 'inventory_row_count_' . ($id ?? 'all') . '_' . md5(json_encode($request->all())); // Unique cache key based on request parameters
        $cacheDuration = now()->addHours(1); // Cache for 1 hour

        // Initialize the base query
        $query = MainInventory::query();

        // Apply dealer ID filter if provided
        if ($id !== null) {
            $query->where('deal_id', $id);
        }

        // Filter by make
        if ($request->make_data != null) {
            $query->where('make', $request->make_data);
        }

        // Filter by dealer name
        if ($request->dealer_name != null) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->dealer_name}%");
            });
        }

        // Filter by dealer city
        if ($request->dealer_city != null) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('city', 'LIKE', "%{$request->dealer_city}%");
            });
        }

        // // Check if the inventory date range is provided
        // if ($request->inventory_date != null) {
        //     // Split the input into start and end dates
        //     $date_data = explode(':', $request->inventory_date);

        //     // Check if both start and end dates are present
        //     if (count($date_data) == 2) {
        //         $startDateData = trim($date_data[0]);  // Start date
        //         $endDateData = trim($date_data[1]);    // End date

        //         // Convert start date to Carbon instance
        //         $startDate = Carbon::createFromFormat('Y-m-d', $startDateData)->startOfDay();

        //         // Convert end date to Carbon instance, default to today if not provided
        //         $endDate = Carbon::createFromFormat('Y-m-d', $endDateData)->endOfDay();

        //         // Apply the date range filter
        //         $query->whereBetween('created_at', [$startDate, $endDate]);
        //     }
        //     else {
        //         // Handle case where the input format is incorrect or incomplete
        //         // Return an error or fallback logic
        //     }
        // }

        // Check if the start date is provided
        if ($request->inventory_date != null) {
            // Convert start date to Carbon instance
            $startDate = Carbon::parse($request->inventory_date)->startOfDay();

            // If end date is provided, use it; otherwise, default to today
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();


            // Apply the date range filter
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter by image count
        if ($request->img_count != null) {
            $query->where('image_count', $request->img_count);
        }

        // Filter by image count
        if ($request->inventory_status != null) {
            $query->where('inventory_status', $request->inventory_status);
        }

        // Filter by dealer state
        if ($request->dealer_state != null) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('state', 'LIKE', "%{$request->dealer_state}%");
            });
        }

        // Cache the result for 1 hour and return the count
        return Cache::remember($cacheKey, $cacheDuration, function () use ($query) {
            // Return the count of the filtered rows
            return $query->count();
        });
    }

    public function getSoldRowCountOptimized($request, $id = null)
    {
        // Use caching to avoid repeated database queries
        $cacheKey = 'inventory_row_count_' . ($id ?? 'all') . '_' . md5(json_encode($request->all())); // Unique cache key based on request parameters
        $cacheDuration = now()->addHours(1); // Cache for 1 hour

        // Initialize the base query
        $query = MainInventory::where('inventory_status', 'Sold');

        // Apply dealer ID filter if provided
        if ($id !== null) {
            $query->where('deal_id', $id);
        }

        // Filter by make
        if ($request->make_data != null) {
            $query->where('make', $request->make_data);
        }

        // Filter by dealer name
        if ($request->dealer_name != null) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->dealer_name}%");
            });
        }

        // Filter by dealer city
        if ($request->dealer_city != null) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('city', 'LIKE', "%{$request->dealer_city}%");
            });
        }

        // // Check if the inventory date range is provided
        // if ($request->inventory_date != null) {
        //     // Split the input into start and end dates
        //     $date_data = explode(':', $request->inventory_date);

        //     // Check if both start and end dates are present
        //     if (count($date_data) == 2) {
        //         $startDateData = trim($date_data[0]);  // Start date
        //         $endDateData = trim($date_data[1]);    // End date

        //         // Convert start date to Carbon instance
        //         $startDate = Carbon::createFromFormat('Y-m-d', $startDateData)->startOfDay();

        //         // Convert end date to Carbon instance, default to today if not provided
        //         $endDate = Carbon::createFromFormat('Y-m-d', $endDateData)->endOfDay();

        //         // Apply the date range filter
        //         $query->whereBetween('created_at', [$startDate, $endDate]);
        //     }
        //     else {
        //         // Handle case where the input format is incorrect or incomplete
        //         // Return an error or fallback logic
        //     }
        // }

        // Check if the start date is provided
        if ($request->inventory_date != null) {
            // Convert start date to Carbon instance
            $startDate = Carbon::parse($request->inventory_date)->startOfDay();

            // If end date is provided, use it; otherwise, default to today
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();


            // Apply the date range filter
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter by image count
        if ($request->img_count != null) {
            $query->where('image_count', $request->img_count);
        }

        // Filter by image count
        if ($request->inventory_status != null) {
            $query->where('inventory_status', $request->inventory_status);
        }

        // Filter by dealer state
        if ($request->dealer_state != null) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('state', 'LIKE', "%{$request->dealer_state}%");
            });
        }

        // Cache the result for 1 hour and return the count
        return Cache::remember($cacheKey, $cacheDuration, function () use ($query) {
            // Return the count of the filtered rows
            return $query->count();
        });
    }


    //Get Row Count
    public function getUserByRowCount($id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        // $count = Inventory::where('deal_id',$id)->count();
        $count = MainInventory::where('deal_id', $id)->count();
        return $count;
    }

    //Get Trashed Item Count
    public function getTrashedCount($id = null)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        if ($id != null) {
            // $count = Inventory::where('deal_id',$id)->onlyTrashed()->count();
            $count = MainInventory::where('deal_id', $id)->onlyTrashed()->count();
        } else {
            // $count = Inventory::onlyTrashed()->count();
            $count = MainInventory::onlyTrashed()->count();
        }

        return $count;
    }

    public function getTrashedItemsOptimized($id = null)
    {
        $query = MainInventory::onlyTrashed();

        if ($id !== null) {
            $query->where('deal_id', $id);
        }

        return $query->with(['additionalInventory', 'dealer'])->get();
    }

    public function getTrashedCountOptimizedCount($id = null)
    {
        // Use caching to avoid repeated database queries
        $cacheKey = 'inventory_trashed_count_' . ($id ?? 'all');
        $cacheDuration = now()->addHours(1); // Cache for 1 hour

        return Cache::remember($cacheKey, $cacheDuration, function () use ($id) {
            if ($id !== null) {
                // Use indexed columns for faster query
                return MainInventory::where('deal_id', $id)->onlyTrashed()->count();
            } else {
                // Directly count trashed rows without loading them into memory
                return MainInventory::onlyTrashed()->count();
            }
        });
    }


    public function getSoldTrashedCountOptimizedCount($id = null)
    {
        // Use caching to avoid repeated database queries
        $cacheKey = 'inventory_trashed_count_' . ($id ?? 'all');
        $cacheDuration = now()->addHours(1); // Cache for 1 hour

        return Cache::remember($cacheKey, $cacheDuration, function () use ($id) {
            if ($id !== null) {
                // Use indexed columns for faster query
                return MainInventory::where('inventory_status','Sold')->where('deal_id', $id)->onlyTrashed()->count();
            } else {
                // Directly count trashed rows without loading them into memory
                return MainInventory::where('inventory_status','Sold')->onlyTrashed()->count();
            }
        });
    }

    //Get Trashed Item Count
    public function getUserByTrashedCount($id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_index'), 403, 'Access Forbidden');
        // $count = Inventory::where('deal_id',$id)->onlyTrashed()->count();
        $count = MainInventory::where('deal_id', $id)->onlyTrashed()->count();
        return $count;
    }


    // public function getItemByFilterWithOptimized($request, $id = null)
    // {
    //     $mainInventory = MainInventory::select('id', 'deal_id', 'stock', 'year', 'make', 'model', 'vin', 'active_till', 'featured_till', 'payment_date', 'package', 'image_count', 'inventory_status')
    //         ->with([
    //             'additionalInventory:id,main_inventory_id,local_img_url,detail_url', // Specify required columns
    //             'dealer:id,name,city,state' // Specify required columns
    //         ]);

    //     // Apply filters
    //     if ($request->make_data != null) {
    //         $mainInventory->where('make', $request->make_data);
    //     }

    //     if ($request->dealer_name != null) {
    //         $mainInventory->whereHas('dealer', function ($query) use ($request) {
    //             $query->where('name', 'LIKE', "%{$request->dealer_name}%");
    //         });
    //     }

    //     if ($request->dealer_city != null) {
    //         $mainInventory->whereHas('dealer', function ($query) use ($request) {
    //             $query->where('city', 'LIKE', "%{$request->dealer_city}%");
    //         });
    //     }

    //     if ($request->img_count != null) {
    //         $mainInventory->where('image_count', $request->img_count);
    //     }

    //     if ($request->inventory_date != null) {
    //         $startDate = Carbon::parse($request->inventory_date)->startOfDay();
    //         $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
    //         $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
    //     }

    //     if ($request->dealer_state != null) {
    //         $mainInventory->whereHas('dealer', function ($query) use ($request) {
    //             $query->where('state', 'LIKE', "%{$request->dealer_state}%");
    //         });
    //     }

    //     // Use chunk to process the results in smaller batches
    //     $results = [];
    //     $mainInventory->chunk(500, function ($inventoryChunk) use (&$results) {
    //         foreach ($inventoryChunk as $inventory) {
    //             // Process each inventory item and add it to the results array
    //             $results[] = $inventory;
    //         }
    //     });

    //     return $results;
    // }


    // public function getItemByFilterWithOptimized($request, $id = null)
    // {
    //     // Generate a unique cache key based on filter parameters
    //     $cacheKey = 'filtered_inventory_' . md5(json_encode($request->all()));

    //     return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
    //         $mainInventory = MainInventory::select(
    //             'id', 'deal_id', 'stock', 'year', 'make', 'model', 'vin',
    //             'active_till', 'featured_till', 'payment_date', 'package',
    //             'image_count', 'inventory_status'
    //         )->with([
    //             'additionalInventory:id,main_inventory_id,local_img_url,detail_url',
    //             'dealer:id,name,city,state'
    //         ]);

    //         if (!empty($request->make_data)) {
    //             $mainInventory->where('make', $request->make_data);
    //         }

    //         if (!empty($request->dealer_name)) {
    //             $mainInventory->whereHas('dealer', function ($query) use ($request) {
    //                 $query->where('name', 'LIKE', "%{$request->dealer_name}%");
    //             });
    //         }

    //         if (!empty($request->dealer_city)) {
    //             $mainInventory->whereHas('dealer', function ($query) use ($request) {
    //                 $query->where('city', 'LIKE', "%{$request->dealer_city}%");
    //             });
    //         }

    //         if (!empty($request->img_count)) {
    //             $mainInventory->where('image_count', $request->img_count);
    //         }

    //         // Filter by image count
    //         if ($request->inventory_status != null) {
    //             $mainInventory->where('inventory_status', $request->inventory_status);
    //         }

    //         if (!empty($request->inventory_date)) {
    //             $startDate = Carbon::parse($request->inventory_date)->startOfDay();
    //             $endDate = !empty($request->end_date) ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
    //             $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
    //         }

    //         if (!empty($request->dealer_state)) {
    //             $mainInventory->whereHas('dealer', function ($query) use ($request) {
    //                 $query->where('state', 'LIKE', "%{$request->dealer_state}%");
    //             });
    //         }

    //         return $mainInventory->get(); // Fetch results and cache them
    //     });
    // }


    public function getSoldItemByFilterWithOptimized($request, $id = null)
    {
        // $mainInventory = MainInventory::select('id','deal_id','stock','year','make','model','vin','active_till','featured_till','payment_date','package');


        $mainInventory = MainInventory::select('id', 'deal_id', 'stock', 'year', 'make', 'model', 'vin', 'active_till', 'featured_till', 'payment_date', 'package','image_count','inventory_status')
            ->with([
                'additionalInventory:id,main_inventory_id,local_img_url,detail_url', // Specify required columns
                'dealer:id,name,city,state' // Specify required columns
            ])->where('inventory_status', 'Sold');

        if ($request->make_data != null) {
            $mainInventory->where('make', $request->make_data);
        }
        // Filter by dealer name
        if ($request->dealer_name != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->dealer_name}%");
            });
        }

        // Filter by dealer city
        if ($request->dealer_city != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('city', 'LIKE', "%{$request->dealer_city}%");
            });
        }

        // Filter by dealer city
        if ($request->img_count != null) {
            $mainInventory->where('image_count', $request->img_count);
        }

        // Filter by image count
        if ($request->inventory_status != null) {
            $mainInventory->where('inventory_status', $request->inventory_status);
        }

        // // Check if the inventory date range is provided
        // if ($request->inventory_date != null) {
        //     // Split the input into start and end dates
        //     $date_data = explode(':', $request->inventory_date);

        //     // Check if both start and end dates are present
        //     if (count($date_data) == 2) {
        //         $startDateData = trim($date_data[0]);  // Start date
        //         $endDateData = trim($date_data[1]);    // End date

        //         // Convert start date to Carbon instance
        //         $startDate = Carbon::createFromFormat('Y-m-d', $startDateData)->startOfDay();

        //         // Convert end date to Carbon instance, default to today if not provided
        //         $endDate = Carbon::createFromFormat('Y-m-d', $endDateData)->endOfDay();

        //         // Apply the date range filter
        //         $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
        //     }
        //     else {
        //         // Handle case where the input format is incorrect or incomplete
        //         // Return an error or fallback logic
        //     }
        // }

        // Check if the start date is provided
        if ($request->inventory_date != null) {
            // Convert start date to Carbon instance
            $startDate = Carbon::parse($request->inventory_date)->startOfDay();

            // If end date is provided, use it; otherwise, default to today
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();


            // Apply the date range filter
            $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter by dealer state
        if ($request->dealer_state != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('state', 'LIKE', "%{$request->dealer_state}%");
            });
        }

        return $mainInventory;
    }

    public function getUpdateItemByFilterWithOptimized($request, $id = null)
    {
        // $mainInventory = MainInventory::select('id','deal_id','stock','year','make','model','vin','active_till','featured_till','payment_date','package');


        $mainInventory = MainInventory::select('id', 'deal_id', 'stock', 'year', 'make', 'model', 'vin', 'active_till', 'featured_till', 'payment_date', 'package','image_count','inventory_status')
            ->with([
                'additionalInventory:id,main_inventory_id,local_img_url,detail_url', // Specify required columns
                'dealer:id,name,city,state' // Specify required columns
            ])->where('inventory_status', 'Updated');

        if ($request->make_data != null) {
            $mainInventory->where('make', $request->make_data);
        }
        // Filter by dealer name
        if ($request->dealer_name != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->dealer_name}%");
            });
        }

        // Filter by dealer city
        if ($request->dealer_city != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('city', 'LIKE', "%{$request->dealer_city}%");
            });
        }

        // Filter by dealer city
        if ($request->img_count != null) {
            $mainInventory->where('image_count', $request->img_count);
        }

        // Filter by image count
        if ($request->inventory_status != null) {
            $mainInventory->where('inventory_status', $request->inventory_status);
        }

        // // Check if the inventory date range is provided
        // if ($request->inventory_date != null) {
        //     // Split the input into start and end dates
        //     $date_data = explode(':', $request->inventory_date);

        //     // Check if both start and end dates are present
        //     if (count($date_data) == 2) {
        //         $startDateData = trim($date_data[0]);  // Start date
        //         $endDateData = trim($date_data[1]);    // End date

        //         // Convert start date to Carbon instance
        //         $startDate = Carbon::createFromFormat('Y-m-d', $startDateData)->startOfDay();

        //         // Convert end date to Carbon instance, default to today if not provided
        //         $endDate = Carbon::createFromFormat('Y-m-d', $endDateData)->endOfDay();

        //         // Apply the date range filter
        //         $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
        //     }
        //     else {
        //         // Handle case where the input format is incorrect or incomplete
        //         // Return an error or fallback logic
        //     }
        // }

        // Check if the start date is provided
        if ($request->inventory_date != null) {
            // Convert start date to Carbon instance
            $startDate = Carbon::parse($request->inventory_date)->startOfDay();

            // If end date is provided, use it; otherwise, default to today
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();


            // Apply the date range filter
            $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter by dealer state
        if ($request->dealer_state != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('state', 'LIKE', "%{$request->dealer_state}%");
            });
        }

        return $mainInventory;
    }

    public function getItemByFilterWithOptimized($request, $id = null)
    {
        // $mainInventory = MainInventory::select('id','deal_id','stock','year','make','model','vin','active_till','featured_till','payment_date','package');


        $mainInventory = MainInventory::select('id', 'deal_id', 'stock', 'year', 'make', 'model', 'vin', 'active_till', 'featured_till', 'payment_date', 'package','image_count','inventory_status')
            ->with([
                'additionalInventory:id,main_inventory_id,local_img_url,detail_url', // Specify required columns
                'dealer:id,name,city,state' // Specify required columns
            ]);

        if ($request->make_data != null) {
            $mainInventory->where('make', $request->make_data);
        }
        // Filter by dealer name
        if ($request->dealer_name != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->dealer_name}%");
            });
        }

        // Filter by dealer city
        if ($request->dealer_city != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('city', 'LIKE', "%{$request->dealer_city}%");
            });
        }

        // Filter by dealer city
        if ($request->img_count != null) {
            $mainInventory->where('image_count', $request->img_count);
        }

        // Filter by image count
        if ($request->inventory_status != null) {
            $mainInventory->where('inventory_status', $request->inventory_status);
        }

        // // Check if the inventory date range is provided
        // if ($request->inventory_date != null) {
        //     // Split the input into start and end dates
        //     $date_data = explode(':', $request->inventory_date);

        //     // Check if both start and end dates are present
        //     if (count($date_data) == 2) {
        //         $startDateData = trim($date_data[0]);  // Start date
        //         $endDateData = trim($date_data[1]);    // End date

        //         // Convert start date to Carbon instance
        //         $startDate = Carbon::createFromFormat('Y-m-d', $startDateData)->startOfDay();

        //         // Convert end date to Carbon instance, default to today if not provided
        //         $endDate = Carbon::createFromFormat('Y-m-d', $endDateData)->endOfDay();

        //         // Apply the date range filter
        //         $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
        //     }
        //     else {
        //         // Handle case where the input format is incorrect or incomplete
        //         // Return an error or fallback logic
        //     }
        // }

        // Check if the start date is provided
        if ($request->inventory_date != null) {
            // Convert start date to Carbon instance
            $startDate = Carbon::parse($request->inventory_date)->startOfDay();

            // If end date is provided, use it; otherwise, default to today
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();


            // Apply the date range filter
            $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter by dealer state
        if ($request->dealer_state != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('state', 'LIKE', "%{$request->dealer_state}%");
            });
        }

        return $mainInventory;
    }

    public function getItemByFilterNoImgWithOptimized($request, $id = null)
    {
        // $mainInventory = MainInventory::select('id','deal_id','stock','year','make','model','vin','active_till','featured_till','payment_date','package');


        $mainInventory = MainInventory::select('id', 'deal_id', 'title', 'vin', 'stock', 'year', 'make', 'model', 'active_till', 'featured_till', 'payment_date', 'package','image_count')
            ->with([
                'additionalInventory:id,main_inventory_id,img_from_url,detail_url,local_img_url', // Specify required columns
                'dealer:id,name,city,state' // Specify required columns
            ]);

        if ($request->make_data != null) {
            $mainInventory->where('make', $request->make_data);
        }
        // Filter by dealer name
        if ($request->dealer_name != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->dealer_name}%");
            });
        }

        // Filter by dealer city
        if ($request->dealer_city != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('city', 'LIKE', "%{$request->dealer_city}%");
            });
        }

        // Filter by dealer city
        if ($request->img_count != null) {
            $mainInventory->where('image_count', $request->img_count);
        }

        // // Check if the inventory date range is provided
        // if ($request->inventory_date != null) {
        //     // Split the input into start and end dates
        //     $date_data = explode(':', $request->inventory_date);

        //     // Check if both start and end dates are present
        //     if (count($date_data) == 2) {
        //         $startDateData = trim($date_data[0]);  // Start date
        //         $endDateData = trim($date_data[1]);    // End date

        //         // Convert start date to Carbon instance
        //         $startDate = Carbon::createFromFormat('Y-m-d', $startDateData)->startOfDay();

        //         // Convert end date to Carbon instance, default to today if not provided
        //         $endDate = Carbon::createFromFormat('Y-m-d', $endDateData)->endOfDay();

        //         // Apply the date range filter
        //         $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
        //     }
        //     else {
        //         // Handle case where the input format is incorrect or incomplete
        //         // Return an error or fallback logic
        //     }
        // }

        // Check if the start date is provided
        if ($request->inventory_date != null) {
            // Convert start date to Carbon instance
            $startDate = Carbon::parse($request->inventory_date)->startOfDay();

            // If end date is provided, use it; otherwise, default to today
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();


            // Apply the date range filter
            $mainInventory->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter by dealer state
        if ($request->dealer_state != null) {
            $mainInventory->whereHas('dealer', function ($query) use ($request) {
                $query->where('state', 'LIKE', "%{$request->dealer_state}%");
            });
        }

        return $mainInventory;
    }



    public function getItemByFilter($request, $id = null, $mainInventory)
    {

        $requestURL = $request->requestURL;
        $urlComponents = parse_url($requestURL);
        $queryString = $urlComponents['query'] ?? '';
        parse_str($queryString, $queryParams);
        $lowestValue = $queryParams['lowestPrice'] ?? null;
        $bestDealValue = $queryParams['bestDeal'] ?? null;
        $lowestMileageValue = $queryParams['lowestMileage'] ?? null;
        $ownedValue = $queryParams['owned'] ?? null;
        $makeTypeSearchValue = $queryParams['makeTypeSearch'] ?? null;
        $homeBodySearch = $queryParams['body'] ?? null;
        $homepage = $queryParams['home'] ?? null;
        $hometypeSearch = $queryParams['homeBodySearch'] ?? null;
        $homeMakeSearch = $queryParams['make'] ?? null;
        $homeModelSearch = $queryParams['model'] ?? null;
        $homePriceSearch = $queryParams['maximum_price'] ?? null;
        $homeDealerCitySearch = $queryParams['homeDealerCitySearch'] ?? null;
        $homeDealerStateSearch = $queryParams['homeDealerStateSearch'] ?? null;
        $homeLocationSearch = $queryParams['zip'] ?? null;
        $homeRadiusSearch = $queryParams['radius'] ?? null;
        // $homeLocationSearch2 = $queryParams['homeLocationSearch2'] ?? null;
        $homeMileageSearch = $queryParams['maximum_miles'] ?? null;
        $homeMinMileageSearch = $queryParams['min-miles'] ?? null;
        $homeMaxMileageSearch = $queryParams['max-miles'] ?? null;

        $homeMinPayment = $queryParams['min_payment'] ?? null;
        $homeMaxPayment = $queryParams['max_payment'] ?? null;
        $homeMinYear = $queryParams['min_year'] ?? null;
        $homeMaxYear = $queryParams['max_year'] ?? null;
        $minPriceBody = $queryParams['min_price'] ?? null;
        $maxPriceBody = $queryParams['max_price'] ?? null;
        $hfuel = $queryParams['hfuel'] ?? null;

        $zipCode  = $homeLocationSearch;
        $countryCode = 'US';

        //dd($homepage);

        //****************** */ saved local storage all search data **********************************************************

        if ($hometypeSearch == 'new') {
            // dd($request->all());
            $searchData = [
                'newfirstzipFilter' => $request->firstzipFilter,
                'newfirstMakeFilter' => $request->firstMakeFilter,
                'newfirstModelFilter' => $request->firstModelFilter,
                'newweb_search_any' => $request->web_search_any,
                'newmakeCheckdata' => $request->makeCheckdata,
                'newautoMaxBodyCheckbox' => $request->autoMaxBodyCheckbox,
                'newautoMinYearCheckbox' => $request->autoMinYearCheckbox,
                'newautoMaxYearCheckbox' => $request->autoMaxYearCheckbox,
                'newrangerMinPriceSlider' => $request->rangerMinPriceSlider,
                'newrangerMaxPriceSlider' => $request->rangerMaxPriceSlider,
                'newrangerMileageMinPriceSlider' => $request->rangerMileageMinPriceSlider,
                'newrangerMileageMaxPriceSlider' => $request->rangerMileageMaxPriceSlider,
                'newrangerYearMinPriceSlider' => $request->rangerYearMinPriceSlider,
                'newrangerYearMaxPriceSlider' => $request->rangerYearMaxPriceSlider,
                'newtotalLoanAmountCalculation' => $request->totalLoanAmountCalculation,
                'newautoWebTransmissionCheckbox' => $request->autoWebTransmissionCheckbox,
                'newautoWebFuelCheckbox' => $request->autoWebFuelCheckbox,
                'newautoWebDriveTrainCheckbox' => $request->autoWebDriveTrainCheckbox ??  $request->autoMobileDriveTrainCheckbox,
                'newwebColorFilter' => $request->webColorFilter,
                'newwebBodyFilter' => $homeBodySearch,
                // mobile version filter data
                'newmobileRangerMinPriceSlider' => $request->mobileRangerMinPriceSlider,
                'newmobileRangerMaxPriceSlider' => $request->mobileRangerMaxPriceSlider,
                'newmobileMileageRangerMinPriceSlider' => $request->mobileMileageRangerMinPriceSlider,
                'newmobileMileageRangerMaxPriceSlider' => $request->mobileMileageRangerMaxPriceSlider,
                'newmobileYearRangerMinPriceSlider' => $request->mobileYearRangerMinPriceSlider,
                'newmobileYearRangerMaxPriceSlider' => $request->mobileYearRangerMaxPriceSlider,
                'newautoMobileTypeCheckbox' => $request->autoMobileTypeCheckbox,
                'newsecondFilterMakeInputNew' => $request->secondFilterMakeInputNew,
                'newsecondFilterModelInputNew' => $request->secondFilterModelInputNew,
                'newautoMobileFuelCheckbox' => $request->autoMobileFuelCheckbox,
                'newautoMobileTransmissionCheckbox' => $request->autoMobileTransmissionCheckbox,
                'newmobileBody' => $homeBodySearch,
                'newmobileColorFilter' => $request->mobileColorFilter,
            ];



            Cookie::queue('searchData', json_encode($searchData), 120);
        } else {

            $searchData = [
                'firstzipFilter' => $request->firstzipFilter,
                'firstMakeFilter' => $request->firstMakeFilter,
                'firstModelFilter' => $request->firstModelFilter,
                'web_search_any' => $request->web_search_any,
                'makeCheckdata' => $request->makeCheckdata,
                'autoMaxBodyCheckbox' => $request->autoMaxBodyCheckbox,
                'autoMinYearCheckbox' => $request->autoMinYearCheckbox,
                'autoMaxYearCheckbox' => $request->autoMaxYearCheckbox,
                'rangerMinPriceSlider' => $request->rangerMinPriceSlider,
                'rangerMaxPriceSlider' => $request->rangerMaxPriceSlider,
                'rangerMileageMinPriceSlider' => $request->rangerMileageMinPriceSlider,
                'rangerMileageMaxPriceSlider' => $request->rangerMileageMaxPriceSlider,
                'rangerYearMinPriceSlider' => $request->rangerYearMinPriceSlider,
                'rangerYearMaxPriceSlider' => $request->rangerYearMaxPriceSlider,
                'totalLoanAmountCalculation' => $request->totalLoanAmountCalculation,
                'autoWebConditionCheckbox' => $request->autoWebConditionCheckbox,
                'autoWebTransmissionCheckbox' => $request->autoWebTransmissionCheckbox,
                'autoWebFuelCheckbox' => $request->autoWebFuelCheckbox,
                'autoWebDriveTrainCheckbox' => $request->autoWebDriveTrainCheckbox ??  $request->autoMobileDriveTrainCheckbox,
                'webColorFilter' => $request->webColorFilter,
                'webMakeFilterMakeInput' => $request->webMakeFilterMakeInput,
                'webBodyFilter' =>  $homeBodySearch,
                'mobileBody' => $homeBodySearch,
                // mobile version filter data
                'mobileRangerMinPriceSlider' => $request->mobileRangerMinPriceSlider,
                'mobileRangerMaxPriceSlider' => $request->mobileRangerMaxPriceSlider,
                'mobileMileageRangerMinPriceSlider' => $request->mobileMileageRangerMinPriceSlider,
                'mobileMileageRangerMaxPriceSlider' => $request->mobileMileageRangerMaxPriceSlider,
                'mobileYearRangerMinPriceSlider' => $request->mobileYearRangerMinPriceSlider,
                'mobileYearRangerMaxPriceSlider' => $request->mobileYearRangerMaxPriceSlider,
                'autoMobileTypeCheckbox' => $request->autoMobileTypeCheckbox,
                'secondFilterMakeInputNew' => $request->secondFilterMakeInputNew,
                'secondFilterModelInputNew' => $request->secondFilterModelInputNew,
                'autoMobileFuelCheckbox' => $request->autoMobileFuelCheckbox,
                'autoMobileTransmissionCheckbox' => $request->autoMobileTransmissionCheckbox,
                'mobileColorFilter' => $request->mobileColorFilter,

                'webExteriorColorFilter' => $request->webExteriorColorFilter,
                'webInteriorColorFilter' => $request->webInteriorColorFilter,
            ];

            Cookie::queue('searchData', json_encode($searchData), 120);
        }


        // dd($request->all());
        // $query = Inventory::with('dealer');
        // $query = MainInventory::with('dealer', 'mainPriceHistory', 'additionalInventory');
        // $query = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price')
        //     ->with([
        //         'dealer' => function ($query) {
        //             $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id')
        //                 ->addSelect('id'); // Add id explicitly to avoid conflict
        //         },
        //         'additionalInventory' => function ($query) {
        //             $query->select('main_inventory_id', 'local_img_url')  // Only necessary columns
        //                 ->addSelect('id'); // Add id explicitly to avoid conflict
        //         },
        //         'mainPriceHistory' => function ($query) {
        //             $query->select('main_inventory_id', 'change_amount') // Only necessary columns
        //                 ->addSelect('id'); // Add id explicitly to avoid conflict
        //         }
        //     ])->whereNot('inventory_status', 'Sold');


        $query = $mainInventory;

        //  query end here //  query end here //  query end here //  query end here //  query end here //  query end here //  query end here

        if ($request->web_search_any) {
            $searchWords = explode(' ', $request->web_search_any);

            $query->where(function ($subquery) use ($searchWords) {
                $subquery->where(function ($subquery2) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $subquery2->where(function ($subquery3) use ($word) {
                            $subquery3->where('make', 'like', '%' . $word . '%')
                                ->orWhere('model', 'like', '%' . $word . '%')
                                ->orWhere('stock', 'like', '%' . $word . '%')
                                ->orWhere('year', 'like', '%' . $word . '%')
                                ->orWhere('zip_code', 'like', '%' . $word . '%')
                                ->orWhere('vin', 'like', '%' . $word . '%');
                            // ->orWhere('body_formated ', 'like', '%' . $word . '%');
                        });
                    }
                })
                    ->orWhere(function ($subquery4) use ($searchWords) {
                        $subquery4->whereRaw("CONCAT_WS('', year, make, model,vin ) LIKE ?", ['%' . implode('%', $searchWords) . '%']);
                    });
            });
        }

        $sortMapping = [
            'datecreated|desc' => ['stock_date_formated', 'desc'],
            'datecreated|asc' => ['stock_date_formated', 'asc'],
            'searchprice|asc' => ['price', 'asc'],
            'searchprice|desc' => ['price', 'desc'],
            'mileage|asc' => ['miles', 'asc'],
            'mileage|desc' => ['miles', 'desc'],
            'modelyear|asc' => ['year', 'asc'],
            'modelyear|desc' => ['year', 'desc'],
            'payment|asc' => ['payment_price', 'asc'],
            'payment|desc' => ['payment_price', 'desc']
        ];



        //Cookie::queue('selected_sort_search',$request->selected_sort_search, 120);
        Session::put('selected_sort_search', $request->selected_sort_search);

        if (isset($sortMapping[$request->selected_sort_search])) {
            $query->orderBy($sortMapping[$request->selected_sort_search][0], $sortMapping[$request->selected_sort_search][1]);
        }

        if ($request->mobile_web_search_any) {
            $searchWords = explode(' ', $request->mobile_web_search_any);

            $query->where(function ($subquery) use ($searchWords) {
                $subquery->where(function ($subquery2) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $subquery2->where(function ($subquery3) use ($word) {
                            $subquery3->where('make', 'like', '%' . $word . '%')
                                ->orWhere('model', 'like', '%' . $word . '%')
                                ->orWhere('stock', 'like', '%' . $word . '%')
                                ->orWhere('year', 'like', '%' . $word . '%')
                                ->orWhere('zip_code', 'like', '%' . $word . '%')
                                ->orWhere('vin', 'like', '%' . $word . '%');
                            // ->orWhere('body_formated ', 'like', '%' . $word . '%');
                        });
                    }
                })
                    ->orWhere(function ($subquery4) use ($searchWords) {
                        $subquery4->whereRaw("CONCAT_WS('', year, make, model,vin ) LIKE ?", ['%' . implode('%', $searchWords) . '%']);
                    });
            });
        }


        // may be no need
        if ($homeDealerCitySearch != null) {
            $query->whereHas('dealer', function ($q) use ($homeDealerCitySearch) {
                $q->where('city', 'like', '%' . $homeDealerCitySearch . '%');
            });
        }

        if ($lowestValue == null && $lowestMileageValue == null && $ownedValue == null) {

            if ($makeTypeSearchValue != null) {
                $query->where('make', $makeTypeSearchValue);
            }

            if ($homeMakeSearch != null) {
                $query->where('make', $homeMakeSearch);
            }
            if ($homeModelSearch != null) {
                $query->where('model', $homeModelSearch);
            }



            if ($homePriceSearch != null) {
                switch ($homePriceSearch) {
                    case "0":
                        $query->where('price', '<=', 5000);
                        break;

                    case "1":
                        $query->where('price', '<=', 10000);
                        // $query->whereBetween('price', [5000, 10000]);
                        break;

                    case "2":
                        $query->where('price', '<=', 20000);
                        break;

                    case "3":
                        $query->where('price', '<=', 30000);
                        break;

                    case "4":
                        $query->where('price', '<=', 40000);
                        break;

                    case "5":
                        $query->where('price', '<=', 50000);
                        break;

                    case "6":
                        $query->where('price', '<=', 60000);
                        break;

                    case "7":
                        $query->where('price', '<=', 70000);
                        break;
                    case "8":
                        $query->where('price', '<=', 80000);
                        break;

                    default:
                        $query->where('price', '<=', 100000);
                        break;
                }
            }


            if ($homeMileageSearch != null) {
                $query->where('miles', '<=', $homeMileageSearch);
            }

            // if ($minPriceBody != null || $maxPriceBody != null) {
            //     $minValue = ($minPriceBody != null) ? $minPriceBody : 0;
            //     $maxValue = ($maxPriceBody != null) ? $maxPriceBody : 1000000;
            //     $query->whereBetween('price', [$minValue, $maxValue]);
            // }

            if ($minPriceBody  || $maxPriceBody) {
                $minValue = ($minPriceBody !== null) ? $minPriceBody : 0;
                $maxValue = ($maxPriceBody !== null) ? $maxPriceBody : 150000;

                // If the max value is 150000, it means we need to show all values
                if ($maxValue == 150000) {
                    // $minPrice_provide = $this->priceRange['used']['minPrice'];
                    // $maxPrice_provide = $this->priceRange['used']['maxPrice'];
                    // // $query->where('price', '>=', $minValue);
                    // $query->whereBetween('price', [$minPrice_provide, $maxPrice_provide]);
                } else {
                    $query->whereBetween('price', [$minValue, $maxValue]);
                }
            }

            if ($homeMinMileageSearch || $homeMaxMileageSearch) {
                $minMileage = ($homeMinMileageSearch !== null) ? $homeMinMileageSearch : 0;
                $maxMileage = ($homeMaxMileageSearch !== null) ? $homeMaxMileageSearch : 150000;

                if ($maxMileage == 150000) {
                    // // $query->where('miles', '>=', $minMileage);
                    // // $minPrice = $this->priceRange['new']['minMiles'];
                    // $minMiles_provide = $this->priceRange['used']['minMiles'];
                    // $maxMiles_provide = $this->priceRange['used']['maxMiles'];

                    // $query->whereBetween('miles', [$minMiles_provide, $maxMiles_provide]);

                } else {
                    $query->whereBetween('miles', [$minMileage, $maxMileage]);
                }
            }



            if (($request->rangerMinPriceSlider != null || $request->rangerMaxPriceSlider != null)) {
                $minValue = ($request->rangerMinPriceSlider != null) ? $request->rangerMinPriceSlider : 0;
                $maxValue = ($request->rangerMaxPriceSlider != null) ? $request->rangerMaxPriceSlider : 150000;
                // dd($request->rangerMileageMinPriceSlider, $request->rangerMileageMaxPriceSlider);
                if ($minValue > 150000) {
                    $query->whereNotNull('price');
                } else {
                    $query->whereBetween('price', [$minValue, $maxValue]);
                }
            }

            if ($request->rangerMileageMinPriceSlider != null || $request->rangerMileageMaxPriceSlider != null) {
                $minValue = ($request->rangerMileageMinPriceSlider != null) ? $request->rangerMileageMinPriceSlider : 0;
                $maxValue = ($request->rangerMileageMaxPriceSlider != null) ? $request->rangerMileageMaxPriceSlider : 150000;

                if ($maxValue < 150000) {
                    // If the max value is less than 150000, use a normal between range query
                    $query->whereBetween('miles', [$minValue, $maxValue]);
                } else {
                    // If the max value is 150000 or more, show all vehicles with miles >= minValue
                    $query->where('miles', '>=', $minValue);
                }
            }

            if ($request->rangerYearMinPriceSlider != null || $request->rangerYearMaxPriceSlider != null) {
                $minValue = ($request->rangerYearMinPriceSlider != null) ? $request->rangerYearMinPriceSlider : 1980;
                $maxValue = ($request->rangerYearMaxPriceSlider != null) ? $request->rangerYearMaxPriceSlider : 2024;

                $query->whereBetween('year', [$minValue, $maxValue]);
            }

            if ($request->mobileRangerMinPriceSlider != null || $request->mobileRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileRangerMinPriceSlider != null) ? $request->mobileRangerMinPriceSlider : 0;
                $maxValue = ($request->mobileRangerMaxPriceSlider != null) ? $request->mobileRangerMaxPriceSlider : 150000;

                if ($minValue > 150000) {
                    $query->whereNotNull('price');
                } else {
                    $query->whereBetween('price', [$minValue, $maxValue]);
                }

                // if ($maxValue < 150000) {
                //     // If the max value is less than 150000, use a normal between range query
                //     $query->whereBetween('price', [$minValue, $maxValue]);
                // } else {
                //     // If the max value is 150000 or more, show all vehicles with miles >= minValue
                //     $query->where('price', '>=', $minValue);
                // }
            }

            if ($request->mobileMileageRangerMinPriceSlider != null || $request->mobileMileageRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileMileageRangerMinPriceSlider != null) ? $request->mobileMileageRangerMinPriceSlider : 0;
                $maxValue = ($request->mobileMileageRangerMaxPriceSlider != null) ? $request->mobileMileageRangerMaxPriceSlider : 1000000;
                if ($maxValue < 150000) {
                    // If the max value is less than 150000, use a normal between range query
                    $query->whereBetween('miles', [$minValue, $maxValue]);
                } else {
                    // If the max value is 150000 or more, show all vehicles with miles >= minValue
                    $query->where('miles', '>=', $minValue);
                }
                // $query->whereBetween('miles', [$minValue, $maxValue]);
            }

            if ($request->mobileYearRangerMinPriceSlider != null || $request->mobileYearRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileYearRangerMinPriceSlider != null) ? $request->mobileYearRangerMinPriceSlider : 1985;
                $maxValue = ($request->mobileYearRangerMaxPriceSlider != null) ? $request->mobileYearRangerMaxPriceSlider : 2024;

                $query->whereBetween('year', [$minValue, $maxValue]);
            }
            if ($homeMinPayment != null || $homeMaxPayment != null) {
                $minPaymentValue = ($homeMinPayment != null) ? $homeMinPayment : 0;
                $maxPaymentValue = ($homeMaxPayment != null) ? $homeMaxPayment : 5000;

                $query->whereBetween('payment_price', [$minPaymentValue, $maxPaymentValue]);
            }

            // if ($homeMinYear != null || $homeMaxYear != null) {
            //     $minYearValue = ($homeMinYear != null) ? $homeMinYear : 1985;
            //     $maxYearValue = ($homeMaxYear != null) ? $homeMaxYear : date('yyyy');

            //     $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            // }

            if ($homeMinYear != null || $homeMaxYear != null) {
                $minYearValue = ($homeMinYear != null) ? $homeMinYear : 1985;
                $maxYearValue = ($homeMaxYear != null) ? $homeMaxYear : date('Y');

                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($hfuel != null) {
                if ($hfuel == 'electric') {
                    $query->where('fuel', 'Plug-in Gas/Electric Hybrid');
                }
                if ($hfuel == 'hybrid') {
                    $query->where('fuel', 'Hybrid');
                }
            }


            if ($request->autoMinYearCheckbox != null || $request->autoMaxYearCheckbox != null) {
                $minYearValue = ($request->autoMinYearCheckbox != null) ? $request->autoMinYearCheckbox : 1985;
                $maxYearValue = ($request->autoMaxYearCheckbox != null) ? $request->autoMaxYearCheckbox : date('yyyy');
                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($request->autoMobileMinYearCheckbox != null || $request->autoMobileMaxYearCheckbox != null) {
                $minYearValue = ($request->autoMobileMinYearCheckbox != null) ? $request->autoMobileMinYearCheckbox : 1985;
                $maxYearValue = ($request->autoMobileMaxYearCheckbox != null) ? $request->autoMobileMaxYearCheckbox : date('yyyy');
                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($request->firstzipFilter != null) {
                $query->where('zip_code', $request->firstzipFilter);
            }



            if ($request->webCity != null) {
                $query->whereHas('dealer', function ($q) use ($request) {
                    $q->where('city', $request->webCity);
                });
            }



            if ($request->firstMakeFilter != null) {
                $query->where('make', $request->firstMakeFilter);
            }
            if ($request->firstModelFilter != null) {
                $query->where('model', $request->firstModelFilter);
            }
            if ($request->makeCheckdata != null) {
                $query->whereIn('make', $request->makeCheckdata);
            }

            if ($request->has('autoMobileTypeCheckbox')) {
                $mobileSelectedTypes = $request->autoMobileTypeCheckbox;


                // Initialize an empty array to collect the types for filtering
                $types = [];

                if (in_array('Certified', $mobileSelectedTypes)) {
                    $types[] = 'preowned certified';
                }
                if (in_array('Preowned', $mobileSelectedTypes)) {
                    $types[] = 'Used';
                }
                if (in_array('New', $mobileSelectedTypes)) {
                    $types[] = 'New';
                }

                // Apply the query only if there are selected types
                if (!empty($types)) {
                    $query->whereIn('type', $types);
                }
            }


            if ($request->has('autoWebConditionCheckbox')) {
                $selectedTypes = $request->autoWebConditionCheckbox;
                // dd($selectedTypes);
                $types = [];

                if (in_array('Certified', $selectedTypes)) {
                    // $types[] = 'Certified Preowned';
                    $types[] = 'preowned certified';
                }
                if (in_array('Preowned', $selectedTypes)) {
                    $types[] = 'Used';
                }
                if (in_array('New', $selectedTypes)) {
                    $types[] = 'New';
                }

                // Apply the query only if there are selected types
                if (!empty($types)) {
                    $query->whereIn('type', $types);
                }
            }

            if ($request->autoMobileFuelCheckbox != null) {
                if ($request->has('allFuelName') && $request->allFuelName == 'allFuelValue') {
                    // "All" is selected, so no filter is applied
                } else if ($request->has('autoMobileFuelCheckbox')) {
                    $query->whereIn('fuel', $request->autoMobileFuelCheckbox);
                }
            }



            if ($request->autoMobileTransmissionCheckbox != null) {
                if ($request->has('allTransmissionlName') && $request->allTransmissionlName == 'allTransmissionValue') {
                    // "All" is selected, so no filter is applied
                } else if ($request->has('autoMobileTransmissionCheckbox')) {
                    $transmissions = $request->autoMobileTransmissionCheckbox;
                    $query->where(function ($subQuery) use ($transmissions) {
                        foreach ($transmissions as $transmission) {
                            if (trim($transmission) == 'automatic') {
                                $subQuery->orWhere('transmission', 'LIKE', '%automatic%')
                                    ->orWhere('transmission', 'LIKE', '%variable%');
                            } else {
                                $subQuery->orWhere('transmission', 'LIKE', '%' . trim($transmission) . '%');
                            }
                        }
                    });
                }
            }



            if ($request->autoWebTransmissionCheckbox != null) {
                if ($request->has('allWebTransmissionlName') && $request->allWebTransmissionlName == 'allWebTransmissionValue') {
                } else if ($request->has('autoWebTransmissionCheckbox')) {
                    $Web_transmissions = $request->autoWebTransmissionCheckbox;
                    $query->where(function ($subQuery) use ($Web_transmissions) {
                        foreach ($Web_transmissions as $transmission_info) {
                            if (trim($transmission_info) == 'automatic') {
                                $subQuery->orWhere('transmission', 'LIKE', '%automatic%')
                                    ->orWhere('transmission', 'LIKE', '%variable%');
                            } else {
                                $subQuery->orWhere('transmission', 'LIKE', '%' . trim($transmission_info) . '%');
                            }
                        }
                    });
                }
            }

            // if ($request->autoWebFuelCheckbox != null) {
            //     if ($request->has('allWebFuellName') && $request->allWebFuellName == 'allWebFuelValue') {
            //     } else if ($request->has('autoWebFuelCheckbox')) {
            //         $query->whereIn('fuel', $request->autoWebFuelCheckbox);
            //     }
            // }

            if ($request->autoWebFuelCheckbox != null) {
                // dd($request->autoWebFuelCheckbox);
                $fuelTypeMapping = [
                    'Diesel' => ['Diesel', 'Diesel (B20 capable)'],
                    'Electric' => ['Electric fuel type', 'BEV (battery electric vehicle)', 'MHEV (mild hybrid electric vehicle)', 'All-Electric'],
                    'Flex Fuel' => ['Race Fuel', 'flex_fuel', 'Flexible Fuel', 'E85 Flex Fuel'],
                    'Gasoline' => ['Plug-In/Gas', 'Gasoline Fuel', 'Gaseous', 'Gasoline fuel type', 'Gasolin Fuel', 'Gasoline', 'Regular Unleaded', 'Premium Unleaded', 'Gaseous Fuel Compatible', 'Ethanol'],
                    'Hybrid' => ['Full Hybrid Electric (FHEV)', 'Electric Performance Hybrid', 'Hybrid Fuel', 'Gasoline/Mild Electric Hybrid', 'Hybrid'],
                    'Hydrogen Fuel Cell' => ['Hydrogen Fuel Cell'],
                    'Plug In Hybrid' => ['Plug-in Gas/Electric Hybrid','PHEV (plug-in hybrid electric vehicle)', 'Phev', 'Plug-In Hybrid'],
                    'Compressed Natural Gas' => ['Natural Gas', 'Gas/CNG', 'Gasoline / Natural Gas', 'Compressed Natural Gas'],
                    'Other' => ['Other', '–', '----', 'Unspecified']
                ];

                $selectedValues = $request->autoWebFuelCheckbox;
                $mappedFuelValues = [];

                foreach ($selectedValues as $value) {
                    if (isset($fuelTypeMapping[$value])) {
                        $mappedFuelValues = array_merge($mappedFuelValues, $fuelTypeMapping[$value]);
                    }
                }

                $mappedFuelValues = array_unique($mappedFuelValues);

                if ($request->has('allWebFuelName') && $request->allWebFuellName == 'allWebFuelValue') {
                    // Logic for 'all' selection
                } else if ($request->has('autoWebFuelCheckbox')) {
                    $query->whereIn('fuel', $mappedFuelValues);
                }
            }

            // // // For Mobile Exterior Color
            // if ($request->autoMobileExteriorColorCheckbox != null) {
            //     // Map the selected colors with wildcards to search in database
            //     $colors = array_map(function($color) {
            //         return '%' . $color . '%'; // Add wildcard (%) for partial matching
            //     }, $request->autoMobileExteriorColorCheckbox);

            //     // Apply the query with OR conditions for matching any of the selected colors
            //     $query->where(function ($query) use ($colors) {
            //         $query->whereRaw(implode(' OR ', array_fill(0, count($colors), 'exterior_color LIKE ?')), $colors);
            //     });
            // }

            if ($request->autoMobileExteriorColorCheckbox != null) {
                $query->where(function ($query) use ($request) {
                    foreach ($request->autoMobileExteriorColorCheckbox as $color) {
                        $query->orWhere('exterior_color', 'LIKE', '%' . $color . '%');
                    }
                });
            }

            if ($request->autoMobileInteriorColorCheckbox != null) {
                $query->where(function ($query) use ($request) {
                    foreach ($request->autoMobileInteriorColorCheckbox as $color) {
                        $query->orWhere('interior_color', 'LIKE', '%' . $color . '%');
                    }
                });
            }

            // // For Mobile Interior Color
            // if ($request->autoMobileInteriorColorCheckbox != null) {
            //     // Map the selected colors with wildcards to search in database
            //     $colors = array_map(function($color) {
            //         return '%' . $color . '%'; // Add wildcard (%) for partial matching
            //     }, $request->autoMobileInteriorColorCheckbox);

            //     // Apply the query with OR conditions for matching any of the selected colors
            //     $query->where(function ($query) use ($colors) {
            //         $query->whereRaw(implode(' OR ', array_fill(0, count($colors), 'interior_color LIKE ?')), $colors);
            //     });
            // }



                // // // dd($request->autoWebExteriorColorCheckbox, $request->autoWebInteriorColorCheckbox);
                // $selectedExteriorColors = $request->autoWebExteriorColorCheckbox;  // Get selected exterior colors from request
                // $selectedInteriorColors = $request->autoWebInteriorColorCheckbox;  // Get selected interior colors from request

                // $allColors = [
                //     "Beige", "Black", "Blue", "Brown", "Gold", "Gray", "Green", "Orange", "Pink", "Purple",
                //     "Red", "Silver", "White", "Yellow", "Other"
                // ];

                // // Handle Exterior Color filtering
                // if ($selectedExteriorColors != null) {
                //     $exteriorColorQueries = [];

                //     // Loop through each selected exterior color and prepare the LIKE queries
                //     foreach ($selectedExteriorColors as $color) {
                //         if (in_array($color, $allColors)) {
                //             $exteriorColorQueries[] = ['exterior_color', 'like', '%' . $color . '%'];
                //         }
                //     }

                //     // If no exterior color matches, show "Other"
                //     if (empty($exteriorColorQueries)) {
                //         $query->where(function($q) {
                //             $q->where('exterior_color', 'like', '%Other%');
                //         });
                //     } else {
                //         // Apply the exterior color filtering
                //         foreach ($exteriorColorQueries as $colorQuery) {
                //             $query->orWhere($colorQuery[0], $colorQuery[1], $colorQuery[2]);
                //         }
                //     }
                // }

                // // Handle Interior Color filtering
                // if ($selectedInteriorColors != null) {
                //     $interiorColorQueries = [];

                //     // Loop through each selected interior color and prepare the LIKE queries
                //     foreach ($selectedInteriorColors as $color) {
                //         if (in_array($color, $allColors)) {
                //             $interiorColorQueries[] = ['interior_color', 'like', '%' . $color . '%'];
                //         }
                //     }

                //     // If no interior color matches, show "Other"
                //     if (empty($interiorColorQueries)) {
                //         $query->where(function($q) {
                //             $q->where('interior_color', 'like', '%Other%');
                //         });
                //     } else {
                //         // Apply the interior color filtering
                //         foreach ($interiorColorQueries as $colorQuery) {
                //             $query->orWhere($colorQuery[0], $colorQuery[1], $colorQuery[2]);
                //         }
                //     }
                // }


                if ($request->autoWebExteriorColorCheckbox != null) {
                    $query->where(function ($query) use ($request) {
                        foreach ($request->autoWebExteriorColorCheckbox as $color) {
                            $query->orWhere('exterior_color', 'LIKE', '%' . $color . '%');
                        }
                    });
                }

                // if ($request->autoWebInteriorColorCheckbox != null) {
                //     $query->where(function ($query) use ($request) {
                //         foreach ($request->autoWebInteriorColorCheckbox as $color) {
                //             $query->orWhere('interior_color', 'LIKE', '%' . $color . '%');
                //         }
                //     });
                // }

                if ($request->autoWebInteriorColorCheckbox != null) {
                    // dd($request->autoWebInteriorColorCheckbox);
                    $exteriorColorMapping = [
                        'Beigh' => ['Beige', 'Macchiato Beige/Black', 'Silk Beige/Black', 'Macchiato Beige/Space Gray', 'Teak/Light Shale', 'Silk Beige / Black', 'Chateau', 'Macchiato Beige', 'Amber Nappa', 'Macchiato Beige/Magma Gray', 'Atlas Beige', 'Chateau W/Linear Dark Mocha Wo', 'Jet/Tonal Stitch', 'Sand', 'Wicker Beige/Black', 'Pearl Beige', 'Sahara Beige w/Jet Black Accents', 'Cattle Tan/Black', 'Cardamom Beige', 'Glazed Caramel', 'Bisque', 'Cornsilk Beige', 'Dark Cashmere', 'Shetland Beige', 'Light Frost Beige/Black', 'Pistachio Beige', 'Ginger Beige/Espresso', 'Canberra Beige', 'Whisper Beige with Gideon accents', 'Siena Tan/Ebony/Siena Tan', 'Lt Oyster/Ebony/Lt Oyster', 'Ivory/Ebony/Ivory/Ivory', 'Pimento/Eb/Eb/Pim/Ebony', 'Shetland Beige & Black', 'Saiga Beige', 'Sand Beige', 'Stone Beige', 'Canberra Beige/Black', 'Wicker Beige/Global Black', 'Cornsilk Beige W/Brown', 'Parchment with Jet Black accents', 'Ginger Beige/Black', 'Silk Beige/Espresso Brown', 'Beige w/Nappa Leather Seati', 'Silk Beige', 'Light Oyster/Ebony', 'Pearl Beige W/Agate Gray', 'AMG Silk Beige', 'Whisper Beige w/Gideon Accents', 'Sonoma Beige Full Merino', 'Luxor Beige', 'Almond Beige/Mocha', 'Shale with Cocoa Accents', 'Light Oyster/Ebony/Ebony/Light Oyster', 'Silk Beige Black', 'Almond/Beige', 'Macchiato Beige/Grey', 'Tan / Red', 'Harvest Beige', 'Light Neutral w/Ebony Accents', 'Shara Beige', 'Black/Beige', 'Saddle/Black', 'Black/Limestone Beige', 'Cornsilk Beige / Brown', 'Beige/Black', 'Venetian Beige', 'Cornsilk Beige Leatherette Interior', 'Beige / Brown', 'Black/Lt Frost Beige', 'Macchiato Beige/Space Grey', 'Dark Beige', 'Beige/Black leatherette', 'Atlas Beige / Gray', 'Caraway/Ebony', 'Beige Leather Interior', 'Black/Luxor Beige', 'Beige / Black', 'Dark Beige/Titan Black', 'Beige Cloth Interior', 'Wicker Beige', 'Pimento / Ebony', 'Cattle Tan / Black', 'Cream Beige', 'Almond Beige', 'Atlas Beige/Light Carpet', 'Alpaca Beige Duragrain Interior', 'Sand Beige/Black', 'Savanna Beige', 'Cashmere and Ebony Leather Interior', 'Sahara Beige', 'Light Frost Beige/Mountain Brown', 'Canyon Brown/Lt Beige', 'Beige/Gray', 'Lt Frost Beige/Black', '2-Tone Black/Desert Beige', 'Shale With Ebony Interior Accents', 'Beige Tradizione', 'Atacama Beige/Basalt Black', 'Bei/Beige', 'Beige Two-tone', 'Black/Light Frost Beige Interior', 'Light Pebble Beige/Bark Brown Interior', 'Cirrus w/Dark Titanium Accents', 'Whisper Beige', 'Ginger Beige/Espresso Brown', 'Shale w/Cocoa Accents', 'Light Cashmere w/Medium Cashmere Accents', 'Velvet Beige', 'Ginger Beige', 'Light Pebble Beige/Bark Brown', 'Macchiato Beige / Black', 'designo Saddle Brown/Silk Beige', 'Veneto Beige', 'Mahogany/Silk Beige', 'Whisper Beige With Ebony Interior Accents', 'Havanna/Sand Beige', 'Soft Beige', 'Macchiato Beige/Espresso Brown', 'Cashmere Beige/Black', 'Pure Beige', 'Whisper Beige w/Ebony Accents', 'Dark Frost Beige/Medium Frost Beige', 'Whisper Beige seats with Ebony interior accents', 'Whisper Beige w/Jet Black Accents', 'Whisper Beige seats', 'Vintage Tan/Ebony', 'Desert Beige', 'Sahara Beige/Mocha', 'Light Beige', 'Cashmere/Ebony', 'Silk Beige/Black MB-Tex', 'Light Frost Beige/Canyon Brown', 'Almond/Ebony', 'Macchiato Beige / Bronze Brown Pearl', 'Whisper Beige W/ Ebony Accents', 'Velvet Beige / Brown', 'Glacier/Ebony/Glacier', 'Mojave Beige/Black', 'Caramel / Ebony', 'Beige/Tan', 'Light Frost Beige / Black', 'designo Macchiato Beige / Saddle Brown', 'Shetland Beige V-Tex Leatherette', 'designo Macchiato Beige', 'Calm Beige', 'Silk Beige/Espresso', 'Pebble Beige', 'Taupe/Pearl Beige', 'Glacier/Ebony', 'Mojave Beige', 'Macchiato Beige/Magma Grey', 'Macchiato Beige/Espresso', 'Coquina Beige', 'Macchiato Beige/Black MB-Tex', 'Macchiato Beige MB-Tex', 'ARTICO man-made leather macchiato beige / black', 'Lt Frost Beige/Mountain', 'Oxford Stone With Garnet Accents', 'Cashmere w/Cocoa Accents', 'Dark Frost Beige/Light Frost Beige', 'Macchiato Beige/Brown', 'Dune Beige', 'designo Silk Beige/Grey', 'Ceylon Beige', 'Beige leather cloth', 'Cornsilk Beige w/Brown Piping', 'Natural Beige/Black', 'Beige Cloth', 'Cornsilk Beige with Brown Piping', 'Beige cloth leather', 'Beige Velour', 'Sand Beige Leather', 'Light pebble beige Cloth', 'Light Beige - Leather', 'Light Oyster / Ebony', 'Lt Frost Beige/Brown', 'Sahara Beige Leather', 'Dark Beige/Black', 'Havanna/Sand Beige Leather', 'Soft Beige Leather', 'Parchment Beige', 'Choccachino/Ebony Accents', 'Luxor Beige/Saddle Brown Leath', 'Luxor Beige Leather', 'Frost Beige', 'Urban Ground/Beige', 'Canberra Beige/Black w/Contrast Stitching', 'Tan/Ebony/Tan/Ebony', 'Mountain Brown/Light Frost Beige', 'Zagora Beige', 'BEIGE LEATHER', 'Natural Beige / Black', 'Canyon Brown / Light Frost Beige', 'Macchiato Beige w/Diamond Stitching', 'Harvest Beige S', 'Leather Interior in Black/Luxor Beige', 'Leather Interior in Dark Night Blue/Limestone Beige', 'Standard Interior in Black/Luxor Beige', 'Two-Tone Exclusive Manufaktur Leather Interior in Graphite Blue and Choice of Color: Cream', 'Silk Beige / Espresso Brown', 'Latte/Ebony Stitch', 'Pimento/Eb/Pimento/Ebony', 'Whisper Beige with Gideon accent', 'Macchiato Beige / Space Grey Leather', 'Artico Almond Beige', 'Silk Beige/Expresso Brown Leather', 'Macchiato Beige Leather', 'Choccachino w/Cocoa Accents', 'Parchment Beige/Steel Gray Stitching', 'Beige Leatherette', 'Camel Beige', 'Pimento/Ebony/Ebony/Pimento/Cirrus', 'Mesa/Ebony', 'Sandstone Beige', 'Vanilla Beige', 'Light Frost Beige / Canyon Brown', 'Beige Connolly', 'Light Frost Beige', 'Rosewood/Ebony', 'Natural Beige', 'Dark Brown/Beige', 'Ivory/Ebony Stitch', 'Macchiato Beige / Black MB-Tex', 'Macchiato Beige / Black Leather', 'Khaki/Ebony', 'Macchiato Beige/Bronze Brown Pearl', 'Cashmere Beige', 'Beige/Brown', 'Polar Beige', 'Midrand Beige', 'Creme Beige', 'Shale with Brownston', 'Sahara Beige With Jet Black Accents', '2 Tone Beige AND Gray', 'Almond Beige/Black', 'Medium Pebble Beige / Cream', 'Dark Pebble Beige', 'Tuscan Beige'],
                        'Black' => ['Dark Galvanized/Sky Cool Gray','Charcoal Nappa','Jet Black','Charcoal','Black','Dark Slate Gray','Blond With Black','Noir with Santorini Blue accents','Obsidian Rush','Macchiato/Black','Black/Alloy','AMG Sienna Brown/Black','Espresso Brown/Black','Jet Black with Jet Black accents','Jet Black with Red Accents','Amg Black Nappa','White w/Black','Ebony','Ebony Black','Jet Black w/Red Accents','Jet Black/Victory Red','Lunar Shadow (Jet Black/Taupe)','Black/Space Gray','Oyster Black','AMG Black','Light Platinum / Jet Black','Titan Black w/Red Stitching','Black w/Orange Stitching','Jet Black/Medium Dark Pewter','Black w/Linear Espresso Wood Trim','Black Pearl','Slate Black','Jet Black w/Kalahari Accents','Jet Black/Gray w/Blue Accents','Black/Black','Titan Black','Nougat Brown/Black','Global Black','Charcoal Black','Midnight Edition','Black/Rock Gray Stitching','Medium Dark Slate','Titan Black w/Blue','Jet Black/Dark Anderson Silver Metallic','Sea Salt/Black','Black/Alloy/Black','Black Onyx','Ebony/Dark Titanium','Oyster/Black','Black/White','Carbon Black','Black w/Brown','Blk Cloudtex &Clth','Black w/Rock Gray','Blk Lthette &Clth','Jet Black/Red Accents','Red/Black','Jet Black/Chai','Alloy/Black','Charcoal Black/Ebony','Black/Ivory','Black/Graphite','Ebony Bucket Seats','Black Sport Cloth40/Con/4','Nero (Black)','Black Leather','Black Dakota leather',"Black/Scarlet w/Shimamoku", "Ebony/Light Oyster Stitch", "Black w/Blue Stitch", "Black/Cloud Gray Fine Nappa premium leather", "Design Black Leather Interior", "Black w/Blue Stitching", "Black Cloth Interior", "Black Leather Interior", "Ebony w/Lunar Grey Stich", "Oyster/Ebony/Oyster", "Jet Black w/Jet Black Accents", "Black/Red", "Sakhir Orange/Black", "Sky Gray Jet Black With Santorini Blue Accents", "Sedona Sauvage With Jet Black Accents", "Dark Gray W/Black Onyx", "Black w/Hadori Aluminum", "Standard Interior in Black", "Leather Interior in Black", "Leather Package in Black", "Standard Interior in Black/ Mojave Beige", "Leather/Race-Tex Interior in Black with Red Stitching", "Leather Interior in Black/Bordeaux Red", "Leather Interior in Black/Pebble Grey", "Standard Interior in Black/Mojave Beige", "Leather Interior in Black/Alcantara® with Stitching in Platinum Grey", "Leather Package in Black with Deviated Stitching in Gentian Blue", "Two-Tone Exclusive Manufaktur Leather Interior in Black and Choice of Color: Cognac Brown", "Leather Interior in Black with Chalk Stitching with Checkered Sport-Tex Seat Centers", "Leather Package in Black/Bordeaux Red", "Black / Ceramic", "Ebony/Ebony/Ebony", "Black/Brown", "Ebony/Ebony/Ebony/Ivory", "Black Cloth", "Black w/Suede-Effect Fabric Seating", "Ebony/Ebony", "Ebony/Ebony/Ebony/Ebony", "Deep Garnet/Ebony", "Black/Bordeaux Red", "Black/Mojave Beige", "EBONY BLK UNIQUE CLOTH SEATS", "Caraway/Ebony/Caraway", "Gradient Black", "Black/Gray", "Ebony With Ebony Interior Accents", "Black/Tartufo", "Jet Black/Gray w/Red Accents", "Black / Graphite", "Ruby Red/Black", "Java/Black", "Black w/Red Stitching", "Black/New Saddle", "Jet Black/Kalahari", "Noir w/Santorini Blue Accents", "Jet Black/Mocha", "Ebony/Ebony/Ivory/Ivory", "Onyx Black - Semi Aniline", "Ebony W Windsor Seats", "Eclp/Ebony/Eclip/Ebony/Eb", "Black w/Leather Seating Surfaces w", "EBONY ACTIVEX MATRL SEATS", "Black w/Rock Gray Stitch", "BLACK SPORT CLOTH40/CON/40", "Black w/Black Top", "EBNY PART VNYL/CLTH&RED STITCH", "Ebony w/Light Oyster Inserts", "Black w/Nappa Leather Seating Surf", "EBONY BLK PERF LTH-TRM SEAT", "Black w/Leather Trimme", "Jet Black/Ceramic White Accents", "Cloth Bucket Seats or Black Sued", "Black w/Rock Gray Contrast Stitching", "Dark Galvanized/Ebony Accents", "Jet Black/Gray", "Demonic Red/Black", "Jet Black/Artemis", "Black w/Stitching", "Slate Black Leather", "Titan Black/Scalepaper Plaid", "Black w/MB-Tex Upholstery", "Titan Black w/Red Piping", "EBONY LTHR SEAT SURFACES", "designo Black", "EBONY ACTIVEX POWER SEATS", "BLACK STX CLOTH 40/CON/40", "F Sport Black", "EBONY ACTIVEX SEAT MATERIAL", "Black W/ Gray", "Black W/ Brown", "LINCOLN SOFT TOUCH EBONY", "CHARCOAL BLACK CLOTH SEATS", "EBONY PREMIUM CLOTH SEATS", "EBONY LUXURY LEATHER TRIM", "Black/Ash 2-Tone", "Jet Black/Light Titanium", "Jet Black/Nightshift Blue", "Galaxy Black", "EBONY LEATHER TRIM SEATS", "Wheat/Black", "Black/Light Graystone", "Black/Blue", "EBONY BLACK UNIQUE CLOTH", "Black w/Oyster Stitching", "Black/Sable Brown", "Ebony/Ebony/Ebony/Cirrus", "Piano Black", "Carbon Black Checkered", "Black w/Contrast Stitching", "Titan Black/Quartz", "Ebon/Ebony", "Black/Silverstone", "Ebony/Lunar", "Black / Red", "Black Nappa Leather", "Black Graphite", "LTHR-TRIM/VINYL BLACK SEATS", "Titan Black w/Blue Accents", "Black/Space Grey", "Ebony with Dark Plum interior accents", "Black w/Striated Black Trim", "Black/Excl. Stitch", "Ebony/Ebony/Mars Red", "Black / Magma Red", "Black w/Black Open Pore", "Satin Black", "Black Dakota w/Dark Oyster Highlight leather", "Titan Black w/Clark Plaid", "Obsidian Black w/Red", "Black/Sevilla Red", "EBONY PREM LEATHER TRIMMED", "EBONY CLOTH BUCKET SEATS", "BLACK SPORT CLOTH40/20/40", "EBONY LEATHER-TRIMMED SEATS", "2-Tone Black/Ceramique", "Black w/Oyster Contrast Stitching", "EBONY ACTIVEX TRIM SEATS", "EBONY ACTIVEX SEAT MTRL", "BLACK LTHR TRIM BUCKET SEAT", "BLACK SPORT 40/CONSOLE/40", "EBONY UNIQUE CLOTH SEATS", "designo Black/Black", "Obsidian Black", "Ebony Cloth Interior", "BLACK INT W/CARMELO LEATHER", "Black Anthracite", "Black Leatherette", "VINYL GRAY/BLACK SEATS", "EBONY/LT SLATE ACTIVEX SEAT", "Dark Titanium/Jet Black", "Off Black", "Black/Gun Metal", "Black/Red Leather", "Ebony/Ebony Accents", "Cloud/Ebony", "Black W/Grey Accents", "BLACK ACTIVEX/COPPER STITCH", "EBONY LEATHER", "EBONY LEATHER SEATS", "Ebony Trimmed Seats", "Ebony/Cirrus premium leather", "Black Dakota w/Contrast Stitching/Piping leather", "EBONY CLOTH SEATS", "Dark Charcoal w/Orange Accents", "Jet Black with Kalahari accents", "Black Kansas Leather", "Jet Black/Dark Ash", "Jet Black/Medium Titanium", "Jet Black/Titanium", "Black / Crescendo Red", "Charcoal Black/Cashmere Leather Interior", "Black / Ivory", "Black/Black/Black", "Titan Black / Quarzit", "Ebony premium leather", "EBONY ACTIVEX SEAT MATRL", "Black/Alcantara Inserts", "Jet Black/Jet Black", "VINYL BLACK SEATS", "EBONY LEATHER-TRIM SEATS", "Vintage Tan/Ebony/Ebony/Vintage Tan/Ebony", "Off-Black", "Black/Chestnut Brown Contrast Stitching", "Ebony / Ebony", "Chalk/Titan Black", "designo Black/Black Exclusive Nappa premium leather", "Black Mountain", "EBONY ROAST LEA-TRIM", "EBONY LEATHER SEAT SURFACES", "Ebony/Silver", "Ebony Black w/Red Accent Stitching", "Ebony Oxford Leather", "MINI Yours Carbon Black Lounge leather", "Jet Black/Jet Black Accents", "Black w/Red Accent Stitching cloth", "PREMIUM LEATHER EBONY", "Black w/Red", "Black/Dark Sienna Brown", "Black/Lizard Green", "Lounge Carbon Black leather", "Ebony Suede Leather", "Titan Black Leatherette Interior", "BLACK ONYX CLOTH SEATS", "Light Oyster/Light Oyster/Ebony", "Black Leather Seats", "Charcoal Black Leather Interior", "Black/Bordeaux", "Black / Gray", "Individual Platinum Black Full Merino Leather", "Morello Red w/Jet Black Accents", "BLACK LTHR TRIMMED BUCKET", "EBONY BLK UNIQUE CLOTH SEAT", "EBONY BLACK LTHR-TRIM SEATS", "Black SensaTec", "Jet Black / Dark Titanium", "Light Titanium/Ebony", "Black/Lava Blue Pearl Leather Interior", "Ebony cloth", "Black/Saddle Leather Interior", "Jet Black/Graystone", "Leather Package in Black/Garnet Red", "Leather Interior in Black with Checkered Sport-Tex Seat Centers", "Almond / Ebony", "Black w/Medium Dark Slate", "Black w/ Silver Crust", "Ebony Seats", "Black w/Blue", "Ebony/Medium Slate", "Blk/Black", "Blk/Grey", "Black/TURCHESE", "Black/Phoenix red"],
                        'Blue' => ['Blue','Blue Stitching Leather', 'Navy/Beige', 'Blue w/StarTex Upholstery', 'Steel Blue', 'Night Blue/Black', 'Ultramarine Blue/Dune', 'Graphite Blue/Chalk', 'Indigo Blue', 'Ultramarine Blue', 'Fjord Blue', 'Night Blue/Dark Oyster', 'Raven Blue/Ebony', 'Admiral Blue/Light Slate', 'Sea Blue', 'Navy Blue', 'Admiral Blue', 'Coastal Blue', 'Blue/White', 'Yas Marina Blue', 'Navy w/Blue Stitching', 'Blue Gray', 'Blue Haze Metallic/Sandstorm', 'Rhapsody Blue', 'Deka Gray/Blue Highlight', 'Estoril Blue', 'Thunderbird Blue', 'Electric Blue', 'Meteor Blue', 'Deep Ocean Blue', 'Blue Bay / Sand', 'Nightshade Blue', 'Light Blue Gray', 'Navy/Gray', 'Gray/Blue', 'Graphite Blue', 'Marine Blue', 'Rhapsody Blue Recaro Seat', 'Yachting Blue', 'Blue / White', 'Medium Dark Flint Blue', 'Midnight Blue', 'Coastal Blue w/Alpine St', 'Raven Blue/Ebony Perforated Ultrafa', 'Dark Blue/Dune', 'Diamond Blue', 'ADMIRAL BLUE LEATHER SEATS', 'Leather Interior in Graphite Blue/Chalk', 'Leather Interior in Dark Night Blue/Chalk', 'Navy Pier W/Orange Stitch', 'ADMIRAL BLUE LT SLATE LEATH', 'Indigo Blue/Brown', 'Vivid Blue', 'Tension/Twilight Blue Dipped', 'Blue Agave', 'Midnight Blue With Grabber Blue Stitch', 'Navy/Harbour Grey', 'Charles Blue', 'Bugatti Light Blue Sport', 'Blue Accent', 'Neva Gray/Biscaya Blue', 'Aurora Blue', 'Light Blue', 'Indigo Blue / Brown', 'Deep Sea Blue/Silk Beige', 'Liberty Blue/Liberty Blue', 'Night Blue', 'Blue-Dark', 'Silver with Blue', 'Blue/Grey', 'Yacht Blue', 'Blue Leather', 'Blue Sterling', 'Deep Blue', 'Slate Blue', 'Tension Blue/Twilight Blue Dipped', 'Graphite Blue/Chalk Leather', 'Imperial Blue', 'Dark Pewter / Electric Blue', 'LAPIZ BLUE METALLIC BLUE', 'Dark Blue', 'Midnight Blue Grabber Blue Stitch', 'Brown/Indigo Blue', 'Neva Grey / Biscaya Blue MB-Tex', 'Blue & White', 'Metropol Blue', 'Aurora Blue / Alcantara', 'Royal Blue/Cream', 'Ocean Blue', 'Spectral Blue', 'Denim Blue', 'Klein Blue with Beluga', 'NAVY W/ORANGE', 'NAVY/ORANGE', 'Atlantic Blue', 'Beyond Blue', 'RHAPSODY BLUE RECARO', 'BLUE & TAN', 'TENSION/TWILIGHT BLUE DIPPED LEATHER', 'TWLIGHT BLUE', 'Tension Blue', 'Liberty Blue / Perlino', 'Saffron/Imperial Blue', 'PREM LTHR-TRMD BEYOND BLUE', 'Blue Haze', 'Aurora Blue/Electron Yellow', 'MANUFAKTUR Signature Yacht Blue', 'BLUE ACCENT / RECARO SEAT', 'Nightshift Blue', 'Dark Blue/Denim w/White Piping', 'Dk. Blue'],
                        'Brown' => ["Brown", "Chestnut", "Saddle Brown", "Cognac", "Alpine Umber", "Nougat Brown", "Maroon Brown", "Tartufo", "Saddle", "Coffee", "Bahia Brown/Black", "Mocha", "Java", "Dark Brown", "Okapi Brown", "Espresso", "Tan", "Santos Brown", "Terracotta", "Caturra Brown", "Atmosphere/Brownstone", "Tan Leather", "Bahia Brown", "Saddle Brown Dakota Leather", "Touring Brown", "Brandy With Very Dark Atmosphere Accents", "Amaro Brown", "Espresso Brown", "Java Brown", "Santos Brown/Steel Gray Stitching", "Aragon Brown", "Brown/Beige", "Nut Brown/Black", "Kona Brown/Jet Black", "Kona Brown with Jet Black Accents", "Brandy w/Very Dark Atmosphere Accents", "AMG Saddle Brown", "Dark Saddle/Black", "Chestnut Brown", "Kona Brown Sauvage", "Saddle Brown/Black", "Dakota Saddle Brown Leath", "Marrakesh Brown", "designo Saddle Brown/Black", "Tartufo/Black", "Kona Brown / Jet Black", "DESERT BROWN TRIM", "Malt Brown", "Maroon Brown Perforated", "New Saddle/Black", "Nougat Brown / Black", "Beechwood/Off-Black", "Hazel Brown/Off-Black", "Brownstone/Jet Black", "Sienna Brown/Black", "Mauro Brown", "Saddle Brown / Black", "Balao Brown", "Cinnamon Brown Nevada Leather Interior", "Brown/Tan interior", "Chestnut Brown/Black", "Castano Dark Brown", "Saddle Brown Dakota w/Exclusive Stitching leather", "designo Light Brown", "Java Brown w/Tan", "Cinnamon With Jet Black Accents", "Glazed Caramel w/Black", "Dark Saddle / Black", "Moccasin/Black Contrast", "Noble Brown", "Taruma Brown", "Tera Excl Dalbergia Brown", "AMG Sienna Brown", "Portland Brown", "Sienna Brown", "Mountain Brown", "Kona Brown", "Shale / Brownstone", "Maroon Brown W Upholstery", "Cinnamon Brown", "Espresso Brown/Magma Grey", "Giga Brown/Carum Spice Gray", "Saddle Brown/Excl. Stitch", "Canyon Brown/Light Frost Beige", "Truffle Brown", "Maroon Brown/Havana Brown", "Desert Brown", "Ski Gray/Bark Brown", "Copper Brown/Atlas Grey", "Light Frost Brown", "Volcano Brown", "Ebony/Brown", "Tan/Brown", "Urban Brown/Glacier White", "Golden Brown", "White / Brown", "Tera Exclusive Dalbergia Brown", "Dark Brown/Ivory", "Tartufo Brown", "Vintage Brown", "Mud Gray/Terra Brown", "Dark Brown w/Grey Stitching", "Brown/Light Frost", "Canyon Brown/Lt Frost Beige", "Bark Brown/Ski Grey", "Cognac Lthr W/dark Brown", "Sable Brown / Neva Grey Nappa Leather", "Tan / Brown", "Tuscan Brown", "Saddle Brown w/Exclusive Stitching", "Tera Dalbergia Brown", "Mountain Brown/Light Mountain Brown", "Brown Leather", "Norias (Brown)", "Sarder Brown", "Bronze (Brown)", "Marsala Brown/Espresso", "Havana Brown", "Lt Mountain Brown/Brown", "Light Mountain Brown", "Brownstone", "Light Mountain Brown/Mountain Brown", "Cedar Brown", "Golden Oak", "Club Leather Interior in Truffle Brown/Cohiba Brown", "Bison Brown", "Sienna Brown MB-Tex", "Saddle Brown MB-Tex", "Espresso Brown MB-Tex", "Caramel/Ebony Accents", "Chestnut With Ebony Interior Accents", "Chestnut Brown/Ebony Accents", "Cognac/Dark Brown", "Dark Brown / Ivory", "Saddle Brown/Brown/Brown", "Brandy/Ebony Accents", "Shale with Brownstone accents", "Cognac w/Dark Brown Highlight", "Parchment w/Open-Pore Brown Walnut Trim", "Cognac w/Dk Brown Highlight", "Marrakesch Brown", "Brown w/Grey Topstitching", "Saddle Brown/Cream", "Taupe/Brown", "Madras Brown", "Noisette Brown", "Auburn Brown", "SIENNE BROWN", "Mocha Brown Leather", "Chaparral Brown", "Charcoal/Light Brown", "Saddle Brown/Dark Brown", "Espresso Brown/Magma Gray", "Dark Brown/Green", "Camel/Dark Green", "Brown / Beige", "Blk Vern Leath W/ Brown Stitch", "Marsala Brown/Espresso Brown", "Olea Club Leather in Truffle Brown", "Dark Brown w/Gray Stitching", "Giga Brown/Carum Spice", "Saddle Brown Leather", "Hazel Brown/", "Saddle Brown/Luxor Beige", "Brown/Pearl", "Brown/Lt Frost Beige", "Brownstone premium leather", "Bahia Brown Leather", "Saddle Brown/Excl. Stitch Leat", "Bahia Brown w/Grey Topstitching", "Kona Brown Sauvage Leather seats with mini-perfor", "Nut Brown / Espresso", "Brown 2-Tone", "Shale w/Brownstone a", "Audi BROWN", "Brown/Ebony", "Florence Brown", "designo Light Brown/", "Dark Sienna Brown/Bl", "Saddle Brown Br", "Marsala Brown", "STYLE Saddle Brown/B", "Kona Brown with Jet", "Cohiba Brown", "Hazel Brown", "Arabica Brown", "Palomino Brown", "Dk Brown w/Gray Stit", "Club Leather Interior in Truffle Brown", "Brown / Light Frost", "Nougat Brown Leather", "Wheat/Brown", "Brn/Brown", "Indigo/Dark Brown", "Arabica Brown/Almond White", "Sable Brown/Neva Grey", "Canyon Brown", "Brown Nv", "Truffle Brown/Cohiba", "Nutmeg Brown", "Cognac Brown", "Macchiato/Bronze Brown", "Dark Atmosphere/Loft Brown", "Earth Brown/Smoky Green", "Gray/Brown", "Ebony / Brown", "Natural Brown", "Dk. Brown", "Impala Brown", "Palomino w/ Open-Pore Brown Walnut Trim", "Palomino w/Wood Brown Trim", "Canyon Brown / Light Beige", "Pecan Brown", "Amarone Brown", "Brown / Indigo Blue", "Leather Exclusive Brown", "Dk Brown w/Gray Stitching", "Saddle Brown Nappa Leather", "Birch Nuluxe® With Open Pore Brown Walnut Trim (Premium)", "Dark Brown / Light Pebble Beige", "Sable Brown Pearl/Espresso Brown", "Palomino semi-aniline leather and Open-Pore Brown", "Espresso Brown 114", "Mocha w/ Orange Stitching", "Brown Bw", "Caturra Brown Kf2", "Gray w/Brown Bolsters", "Noisette Brown Leather", "Castano Brown", "Maroon Brown - RA30", "Maroon Brown - RC30", "LIGHT BROWN", "Club Leather Interior in Truffle Brown/Cohiba Brow", "Exclusive Manufaktur Interior in Cohiba Brown and", "Criollo Brown", "Brown DINAMICA w/Grey", "Porcelain/Espresso Brown", "Saddle Brown Dakota", "designo Auburn Brown", "Mauro Brown Vienna Leather", "Bison Brown/Mountain Brown", "Ebony/Brown w/Premium Leather", "Vermont Brown", "designo Saddle Brown", "brown/tan", "Leather Brown", "Brown Nappa Leather", "Truffle Brown/Cohiba Brown", "Khkc/Brown", "AMG Saddle Brown MB-Tex", "MANUFAKTUR Mahogany Brown / Macchiato Beige Exclusive Nappa Leather", "Dark Brown and Tan", "Espr Brown Perforated Veganza", "Murillo Brown", "Brown / Saddle Leather", "Portland Brown Full Leather Interior", "Club Leather Interior in Cohiba Brown", "Leather Interior in Saddle Brown/Luxor Beige"],
                        'Gold' => ['Gold','Golden Oak & Black', 'Golden Oak/Black', 'Cream/Gold', 'Golden Oak & Black - CF', 'Agate Grey/Lime Gold'],
                        'Gray' => ["Gray", "Cirrus", "Medium Gray", "Graphite", "Ash", "Dark Gray", "AMG Neva Gray", "Gray w/Yellow Stitching", "Grey", "Neva Grey/Black", "Espresso/Gray", "Wilderness Startex", "Titanium Gray", "Rock Gray", "Storm Gray", "Macchiato/Magmagray", "Diesel Gray/Black", "Ski Gray/Black", "Light Gray", "Titan Blk Clth", "Cement", "Slate Grey", "Steel Gray w/Anthracite", "Rotor Gray w/Anthracite", "Steel Gray w/Anthracite Stitching", "Gray/Black", "Grey/Carbon Black", "Graystone", "Steel Gray", "Cocoa/Dune", "Ash/Black", "Gideon/Very Dark Atmosphere", "Dark Slate 40 20 40", "Dark Charcoal", "Shale", "Dark Gray Leather Interior", "Gray Cloth Interior", "Aviator Gray", "Dark Galvanized Gray", "Sky Cool Gray", "Gray w/Orange Stitching", "Sky Gray With Santorini Blue Accents", "Rotor Gray", "Leather Interior in Agate Grey/Pebble Grey", "Sandstorm Gray w/Nappa Le", "Gray Cloth", "Slate Gray", "Cement Gray", "Gray Flannel", "Dark Ash Gray Sky Gray", "Dark Atmosphere/Medium Ash Gray", "Steel Grey", "Cocoa/ Light Ash Gray", "Medium Ash Gray", "Gray w/Leatherette Seating Surface", "Macchiato/Magma Grey", "Dark Galvanized Sky Gray", "Titanium Grey/Black", "Adelaide Grey", "Medium Earth Gray", "DARK GRAY CLOTH 40CONSOLE40", "Grey Flannel", "Lunar Gray", "Silverstone/Black", "Agate Gray", "Titanium Grey Pearl", "Dark Ash with Jet Black Interior Accents", "Greige/Black", "DARK PALAZZO GRAY VINYL", "DARK PALAZZO GRAY CLOTH", "PRFM GRAY ACTIVEX SEAT MTRL", "Medium Ash Gray/Jet Black", "Gray 2-Tone", "Dark Earth Gray", "Neva Gray/Sable Brown", "Grey/Blue", "Agate Grey", "Neva Grey/Sable Brown", "Ash Gray/Glacier White", "Magma Grey/Black", "Dark Atmosphere/ Medium Ash Gray", "MED GRAY CLOTH 40CONSOLE40", "Rock Gray/Gray", "Rock Gray / Black", "Diesel Gray / Black", "Gray / Black", "Dk Khaki/Lt Graystone", "SPACE GRAY ACTIVEX TRIM SEA", "Stonegray", "Gray Cloudtex & Cloth", "Rotor Gray w/Anthracite Stitching", "Gray w/Blue Bolsters", "Dark Palazzo Gray", "Earth Gray", "DARK EARTH GRAY CLOTH SEATS", "Light Blue Gray/Black", "Gray Dakota Leather", "Dark Walnut/Dark Ash Grey", "Medium Slate Gray", "LTH-TRM/VINYL GRAY/NAVY STS", "Dark Slate Gray/Med Slate Gray", "Light Titanium/Dark Titanium Accents", "Dark Space Gray", "Medium Greystone/Dark Slate", "Dark Gray/Camel", "Gray/Dark Gray Leather Interior", "Fog Gray 2-Tone", "Satellite Grey", "Dark Slate Gray/Medium Slate Gray Cloth Interior", "Birch w/Black Open Pore", "Giga Cassia/Spice Gray", "Metro Gray", "French Roast/Black", "Diesel Grey/Black", "Palazzo Grey", "Medium Titanium/Jet Black", "Ebony seats with Slate interior accents", "Grayblack", "Dark Earth Gray cloth", "Gray MB Tex", "Marble Gray", "Stratus Gray", "Dark Slate Grey/Med Slate Grey", "Standard Interior in Slate Grey", "Light Space Gray", "Gray/Green 3-Tone", "Dark Ash Gray/Sky Cool Gray", "Neva Gray", "Ebony w/Red Accent Stitching", "Alpine Gray", "Satellite Gray", "Space Gray", "Cashmere grey/Phoenix Red", "Creme Light/Black Accent", "Ash/Gray", "Dark/Medium Slate Gray", "Light Blue Gray / Black", "Magma Grey", "Pastel Slate Gray", "Gray / Silver", "Mega Carum Spice Gray", "Dark Walnut/Dark Ash Gray", "Quartz Gray", "Cloud Gray", "Dark Slate/Medium Graystone", "Crystal White/Silver Gray Pearl", "Medium Flint Gray", "Dark Galvanized /Sky Cool Gray", "Dark Space Gray w/ Navy Pier", "Dark Walnut/Very Dark Ash Gray", "Monaco Gray", "DARK PALAZZO GREY VINYL", "Medium Slate Gray Leather Cloth D5", "Gray / Dark Gray", "Slate/Graystone", "BLK W/GRAY INSERTS", "Dark Gray/Vanilla", "Dark Slate Gray Interior", "Dark Ash Gray", "Gray/Metallic", "Jade Gray", "Gray w/Pure White", "Rock Gray / Granite Gray", "Gray / White", "Lt Stone W/Gray Piping", "Gray (yth)", "Dark Gray w/Navy Pier", "Crystal Gray", "Light Titanium W/Ebony Accents", "Misty Gray", "Dark Walnut/Dark Ash Grey Forge Perforated Leather Seating Surf", "Montana Gray", "Gray/Beige", "Charcoal Gray", "Dark Slate Gray/Medium Slate Gray", "Magma Gray", "Agate Grey / Pebble Grey", "GRAY CLOTH 40/20/40", "Gray leather", "Ebony w/Smoke Gray", "MEDIUM GRAY CLOTH 40/20/40", "Medium Earth Gray cloth", "Silverstone II Atlas Grey", "Diesel Gray", "Grey Fabric", "Gray Leatherette", "Charcoal/Misty Gray", "Dark Gray/Onyx", "Standard Interior in Agate Grey", "Leather Interior in Slate Gray/Chalk", "Hai Gray", "Melange/Light Gray", "Two-Tone Gray Cloth", "Cocoa/Light Ash Gray", "Palazzo Gray", "Platinum Grey", "Neva Grey", "Dark Grey", "Leather Interior in Slate Grey", "Phantom Gray", "Mega Carum Spice Grey/Carum Spice Grey", "Agate Grey/Pebble Grey", "Medium Slate Gray/Light Shale", "Gravity Gray", "Grey 40 Console 40", "Moonstone/Grey Flannel", "Natural Gray", "Medium Slate Gray cloth leather", "Light Ash Gray/Ceramic White", "Greystone", "Graphite Grey", "Pebble Grey", "Neva Grey / Sable Brown MB-Tex", "Neva Grey / Biscaya Blue Leather", "Light Ash Gray", "Dark Walnut / Dark Ash Grey", "Lt Gray", "Dove Grey", "Radar Red/Dark Slate Gray", "Graphite Gray", "Stone Gray/Raven", "Dark Gray/Med Gray", "Dk/Lt Slate Gray", "Platinum Gray", "Dark Slate Gray/Saddle Tan", "Dark Palazzo Grey", "Dark Slate Gray/Medium Slate Gray Cloth Bucket", "Medium Ash Gray Premium cloth seat trim", "Flint Gray", "GREY CLOTH 40/20/40", "Dark/Light Slate Gray", "Dk/Med Slate Gray", "Med Slate Gray", "Dark Khaki/Light Graystone", "Gray / Blue", "Gray cloth leather", "Giga Cassia/Carum Spice Grey", "Gray with Orange Stitching", "Light Grey", "PALAZZO GREY CLOTH SEATS", "Gray Partial Leather", "Coral and Gray", "Gray - GR", "Pando Gray", "Dark Slate Gray Cloth", "Md Slate Gray/Lt Shale", "Art Gray", "Storm Gray Leatherette", "GYT/GRAY", "Dark Slate Gray / Medium Slate Gray", "AMG Neva Grey", "Ash / Gray", "Metropolis Gray", "Two-Tone Gray", "Cinder Grey/Ebony", "Crystal Grey", "Scivaro Gray", "Dark Ash Gray / Light Ash Gray", "Dark Graystone/Medium Graystone", "Moonrock Gray", "Everest Gray", "Light Gray Leather", "Cognac w/Granite Gray", "Dark Graystone / Medium Graystone", "Neva Grey/Biscaya Blue"],
                        'Green' => ["Green","Light Argento Metallic/Sage Green","Green / Beige", "Forest Green/Beige", "Dark Green", "Sage Green", "Evergreen","Green Pearlcoat","GREEN / BLACK","Carbon Black/GREEN", "Urban Green","Rialto Green", "Agave Green", "Cactus Green", "Cumbrian Green", "Pine Green", "Nero Ade w/ Green and Orange", "Mori Green", "Sage Green w/Lime Accents" ,"Shadow Green", "Light green" , "Dark Green/Glacier White" , "Cumbrian Green Hide", "Forest Green", "Moss Green", "Dark Green 2-Tone"],
                        'Orange' => ["Orange", "Orange Stitching w/Cloth Upholst", "Orange Stitching Leather", "Kyalami Orange/Black", "Saffrano Orange & Black", "Kyalami Orange", "Inferno Orange", "Sakhir Orange", "Orange/White", "SAKHIR ORANGE/BLK", "Orange Accent", "Burnt Orange", "Orange Zest", "CODE ORANGE", "LT.ORANGE"],
                        'Pink' => ['Pink','Peony Pink', 'Club Pink Plaid/Black'],
                        'Purple' => ['Purple','Dark Auburn With Jet Black Accents', 'Garnet Seats With Ebony Interior Accents', 'Q Deep Purple', 'Purple Silk'],
                        'Red' => ["Red", "Bengal Red", "Tacora Red", "Bengal Red/Black", "Circuit Red", "Classic Red", "Red Amber", "Rioja Red", "Mars Red/Ebony/Mars Red", "Cockpit Red", "Red w/Ultra Suede Perforated SofTe", "Red Leather", "Red Stitch Leather", "Magma Red", "Burgundy Red", "Fiona Red", "Fiona Red/Black", "Flare Red", "Red & Zegna", "AMG Cranberry Red/Black", "Carmine Red/Black", "Exclusive Carmine Red/Black", "Tacora Red w/Contrast Stitching", "Classic Red/Black", "AMG Power Red/Black", "Garnet Red/Black", "Cranberry Red", "Arras Red", "Adrenaline Red", "Cranberry Red/Black", "Red/Black Bicolor", "Red Pepper/Black", "Mars Red/Flame Red Stitch", "Coral Red/Black", "Circuit Red w/Hadori Aluminum", "EBONY ACTIVEX/RED STITCHING", "Ruby Red", "Bordeaux Red", "Coral Red Dakota Leather", "Bordeaux Red/Black", "Red / Black", "Dream Red Leather Interior", "Charcoal w/Lava Red Stit", "Jet / Red", "Dark Ruby Red", "Porcelain/Titian Red", "Salsa Red", "Redwood", "Barcelona Red", "Circuit Red/Dark Graphite", "Charcoal w/Lava Red Stitch", "Red/tan", "Red/Tan/Yellow", "Rioja Red w/Dark Graphite Aluminum", "Crimson Red", "Monaco Red/Graphite", "Bk/Wred", "Red Merino", "Sevilla Red", "Spice Red", "Charcoal w/Lava Red", "designo Mystic Red", "Red / Tan", "Express Red", "Red Pepper", "Mahogany Red", "Ceramic White With Red Stitching", "Sporting Red", "Monaco Red", "Red Leather Interior", "Flamenco Red Metallic", "AMG Power Red", "AMG Cranberry Red", "Mugello Red", "RED NAPPA AND RED CARPET", "Mars Red / Ebony", "Red Oxide", "Jet / Jet / Redzone", "Ebony w/Red Stitch", "Red Recaro Nismo Leather", "Manufaktur Classic Red", "Circuit Red w/Scored Aluminum Trim", "Coral Red Boston Leather", "Showstop Red Lthr Trim Pw", "Garnet Red", "Ebony w/Red Stitching", "ADRENALINE RED FRONT LEATHER SEATING SURFACES", "Adrenaline Red Dipped", "Hotspur Red", "TACORA RED SENSATEC", "Charcoal / Red", "Charcoal w/Lava Red St", "BELUGA/RED", "Bordeaux Red w/ Chalk Stitching", "Commissioned Collection Phoenix Red", "Ebony/Red/Red Stitch", "MSO Bespoke Red", "Morello Red", "CARMINE RED LEATHER-TRIMMED", "Torch Red", "Custom White / Red Carbon Leathe", "Charcoal/Red Nappa Leather", "EBNY PART VNYL/CLTH&RED STI", "EBONY LTHR TRIM/RED STITCH", "Phoenix Red/arctic white", "Macchiato/Lounge Red", "Fox Red Novillo", "Berry Red", "Graphite with Red Stitch", "Consort Red", "Vermilion Red", "Red Accent", "Pimento Red/Ebony Inserts & Stitch", "Commissioned Collection Mugello Red", "Full Red", "Venom Red", "Circuit Red w/ Hadori Aluminum Trim", "Imola Red", "Phoenix Red", "Ebony With Red Stitching", "Ardent Red", "Red Leather Seating Surfaces", "White/Red", "Coral Red", "Red Rock", "Rioja Red w/Silver Performance Trim", "Red with Jet Stitching and Red Interior", "Chancellor Red", "Ebony with Mars Red Stitching", "Ebony/Mars Red", "Ebony/Pimento Red with Pimento Red Stitching", "Showstopper Red", "Brick Red/Cashmere Accents", "Signal Red", "Show Stopper Red", "Circuit Red w/Hadori Aluminum Trim", "Spicy Red", "Ebony/Eternal Red", "Carmine Red", "F-Sport Rioja Red", "Rioja Red w/Dark Graphite", "MAHGNY RED LTH SEAT SURFACE", "Circuit Red W/Hadori Alum", "Dark Knight/Spicy Red", "Flame Red Clearcoat", "Circut Red", "All Eclipse Red", "Mars Red/Ebony/Mars Red Leathe", "Cockpit Red leather", "Mars Red/Mars Red/Ebony", "Carrera Red", "Marsala Red", "Ebony/Red Stitch", "Checkered", "Red w/Amman Stitching", "Carbon and Martian Rock Red", "Pillar Box Red", "RED/BL", "Ebony w/Mars Red Stitch", "Pimento Red / Ebony / Ebony", "Anthracite/Red Leather", "Adrenaline Red/Jet B", "Red Line", "Circuit Red W/Satin Chrom", "Chill Red", "Fox Red", "Bengel Red", "Tacora Red Perforated SensaTec", "Burgundy Red perforated and quilted Veganza", "Chancellor Red/Ivory Leather", "Dk Charcoal/Red", "Two-Tone Exclusive Manufaktur Leather Interior in Bordeaux Red and Choice of Color: Cream", "Leather Interior in Bordeaux Red", "Redzone/Redzone Stitch", "Tacora Red w/Contrast Stitching/Piping", "Arras Red Design Selection", "Express Red Fine Nappa Leather", "NH-883PX/RED", "Vermillion Red", "Red/Red", "Circuit Red/F Aluminum", "Morello Red Dipped", "TORRED CLEARCOAT [RED]", "Ebony DuoLeather seats with Mars Red stitch", "Sevilla Red Two-Tone", "Red Nv", "F Sport Rioja Red", "Lords Red", "Claret Red", "Oxide Red", "Brick Red", "Red / Cream", "Flare Red With", "Circuit Red Nul", "Rioja Red Nulux", "Circut Red w/Satin Chrome Trim", "Ebony / Mars Red", "Ferrari Red", "Red Pepper Manufaktur Sin", "Charcoal & Red", "VELVET RED PEARLCOAT", "Ebony / Pimento Red / Ebony", "Circuit Red NuLuxe and Hadori Aluminum", "Amber Red", "Brick Red / Cashmere", "Circuit Red w/Naguri Aluminum Trim", "Merlot Red", "Red/White", "Red Mica", "BLK/RED", "Ceramic White w/Red Stitching", "RedL", "Sevilla Red 2-Tone", "KING RANCH RED BUCKET SEATS", "DESIGNO BENGEL RED", "Magma Red w/Anthracite Stitching", "Burgundy Red Perforated Veganz", "Pimento Red", "Cranberry Red Leather", "Cayenne Red", "EBONY W/ RED STITCH", "EBONY/ RED STITCHING", "EBONY W/RED STITCH ACCENTS", "Brick red & cashmere", "Deep Red", "Color to Sample Red & White", "REDWOOD VENETIAN LEATHER", "BENGAL RED NAPPA", "Red - RE", "Mars Red/Ebony/Mars Red - 301YG", "ADRENALIN RED", "Redwood Leather", "Sport Red", "Red Kj3", "SHOWSTOP RED LTHR-TRIM PWR", "Red Td1", "Red (td1)", "Adrenalin Red - 704", "Demonic Red Laguna Leather", "Red (d3l)", "Exclusive Carmine Red", "Rioja Red Eb33", "SHOWSTOPPER RED RECARO LTHR", "Rioja Red - EA21", "Lounge Championshipred", "Red Copper", "Charcoal with Lava Red Stitch with Front & Rear Leather Seat Trim (1st / 2nd Rows)", "Pure Red", "Mars Red with Flame Red Stitching", "Adrenaline Red Napa leather seating surfaces with", "Divine Red", "GTS Interior Package in Carmine Red", "MANUFAKTUR Signature Carmine Red", "AMG Classic Red / Black Exclusive Nappa Leather", "Boxster Red", "Onyx/Red", "Arras Red Valcona", "Dark Red", "Flare Red w/Ginsumi", "CHARCOAL VENTIRED NAPPA L", "Ebony/Flame Red Stitch", "Red & Zegna w/Amman", "designo Bengal Red", "White & Red", "Infrared"],
                        'Silver' => ['Silver','Steel','Platinum','Silverstone','Light Platinum/Jet Black','Light Platinum w/Jet Black Accents','Silver w/Silver Trim','SILVER','Lunar Silver','Silverstone/Coffee','Dark Pewter w/Silver Trim','Silverstone/Vintage Coffee','Silver w/Blue Trim','IGNOT SILVER','Dark Pewter / Silver','Symphony Silver','Pyrite Silver Metallic','Manufaktur Cry Wte/Silver','LIQUID SILVER METALLIC S','Ingot Silver Metallic','Silver Pearl','Silverstone Sensafin','Sil/silver','Rhodium Silver','BLADE SILVER METALLIC','Silver / Blue','Silver with Silver Trim','Silver Bison','Shimmering Silver Pearl','Pastel Silver'],
                        'White' => ['White', 'Blond','Ivory','Warm Ivory','Parchment','Medium Stone','Ivory/Ebony/Ivory/Ebony','Light Oyster','White/Black','Cashmere','Ivory Lth','Ceramic White','Almond','Ivory White','Gallery White','Smoke White','Designo Diamond White Metallic Vinyl','Tafeta White','Ivory White/Night Blue','Ivory White/Black','Off White','Deep White/Black','Opal White','Melange/Lt Gray','Glacier White','WHITE/BLK','Arctic White','Bespoke White','designo Platinum White','White Sands/Espresso','White Leather','Parchment White','Summit White','White/Ivory','White/Brown','Platinum White Pearl','Graphite/White Stitching','Ultra White','Giga Ivory White','Vianca White','Macchiato/Magmagrey','Grace White','GRACE WHITE / BLACK / PEONY PINK','Turchese/ARCTIC WHITE','TAILORED PURPLE/GRACE WHITE/BLACK','GRACE WHITE/COBALTO BLUE','Ivory White/Dark Oyster','Grace White/peony pink','Crystal White','AMG Neva Grey MB-Tex','White Sands','FUJI WHITE','F SPORT White','White Ivory Leather','Sea Salt/White','Platinum White','White / Tan','Grace White/Havana','White/Peppercorn','Pure White','Bright White Clearcoat','Ivory White/Atlas Grey','White Premium leath','Neva White/Magma Grey','Neva White/Magma Grey MB-Tex','White Frost Tricoat','Glacier White w/Copper','Opal White/Amaro Brown','Oxford White','Ivory White Nappa','Whi/White','designo Platinum White Pearl / Blac','Q Glacier White','Champagne/Blue','White w/Satin Chrome Trim','Ivory White Nappa Leather','Eminent White Pearl','Super White','Polar White','Ivory White w/Dark Oyster Highlight','White or Light Beige','Ivory White Ext Merino Leather','Veganza Perforated Qlt Smoke White','VCFU Smoke White Extended Merino L','Mandarin with Grace White','Grace White with Pine Green','White/white','MACCHIATO/ MAGMAGREY','Smoke White/Night Blue','White and blue','Macchiato/Magmagrey - 115','White D1s','offwhite leather','Ceramic White Pearl','White by Mulliner','Ivory White w/ Contrast Stitching','Nero-White Stitching','Graphite/White cloth','Ivory White Extended Merino Leather','Ivory White/Night Blue Extended Merino Leather','Cream (Off-White)','Steam (White)','Glacier White - GLW','Commissioned Collection Arctic White','Pearl White','White Diamond Pearl','Ivory White/Atlas Gray'],
                        'Yellow' => ['Yellow', 'Amber', 'Yellow Stitching w/StarTex Uphols', 'Yellow w/Yellow Trim', 'Forge Yellow'],
                        'Other' => ['Other', '–', '----', 'Unspecified']
                    ];

                    $selectedInteriorColorValues = $request->autoWebInteriorColorCheckbox;
                    $mappedwebInteriorColorValues = [];

                    foreach ($selectedInteriorColorValues as $value) {
                        if (isset($exteriorColorMapping[$value])) {
                            $mappedwebInteriorColorValues = array_merge($mappedwebInteriorColorValues, $exteriorColorMapping[$value]);
                        }
                    }

                    $mappedwebInteriorColorValues = array_unique($mappedwebInteriorColorValues);

                    if ($request->has('allWebInteriorColorName') && $request->allWebInteriorColorName == 'allWebInteriorColorValue') {
                        // Logic for 'all' selection
                    } else if ($request->has('autoWebInteriorColorCheckbox')) {
                        $query->whereIn('interior_color', $mappedwebInteriorColorValues);
                    }
                }

                // if ($request->autoWebExteriorColorCheckbox != null) {
                //     $colors = array_map(function($color) {
                //         return '%' . $color . '%';
                //     }, $request->autoWebExteriorColorCheckbox);

                //     $query->where(function ($query) use ($colors) {
                //         $query->whereRaw(implode(' OR ', array_fill(0, count($colors), 'exterior_color LIKE ?')), $colors);
                //     });
                // }

                // if ($request->autoWebInteriorColorCheckbox != null) {
                //     $colors = array_map(function($color) {
                //         return '%' . $color . '%';
                //     }, $request->autoWebInteriorColorCheckbox);

                //     $query->where(function ($query) use ($colors) {
                //         $query->whereRaw(implode(' OR ', array_fill(0, count($colors), 'interior_color LIKE ?')), $colors);
                //     });
                // }


                // if ($request->autoWebExteriorColorCheckbox != null) {
                //     $query->where(function ($query) use ($request) {
                //         foreach ($request->autoWebExteriorColorCheckbox as $color) {
                //             $query->orWhere('exterior_color', 'like', '%' . $color . '%');
                //         }
                //     });
                // }

                // if ($request->autoWebInteriorColorCheckbox != null) {
                //     $query->where(function ($query) use ($request) {
                //         foreach ($request->autoWebInteriorColorCheckbox as $color) {
                //             $query->orWhere('interior_color', 'like', '%' . $color . '%');
                //         }
                //     });
                // }




            // if ($request->autoWebDriveTrainCheckbox != null) {
            //     dd($request->autoWebDriveTrainCheckbox);
            //     if ($request->has('allWebDriveTrainlName') && $request->allWebFuellName == 'allWebDriveTrainValue') {
            //     } else if ($request->has('autoWebDriveTrainCheckbox')) {
            //         $query->whereIn('drive_info', $request->autoWebDriveTrainCheckbox);
            //     }
            // }

            if ($request->autoWebDriveTrainCheckbox != null) {
                // Mapping array
                $driveTypeMapping = [
                    '4WD' => ['Four-wheel Drive', '4WD'],
                    'AWD' => ['All-wheel Drive', 'AWD'],
                    'FWD' => ['Front-wheel Drive', 'FWD'],
                    'RWD' => ['Rear-wheel Drive', 'RWD'],
                    'Other' => ['Unknown', '–']
                ];

                $selectedValues = $request->autoWebDriveTrainCheckbox;
                $mappedValues = [];

                // Map selected checkboxes to database values
                foreach ($selectedValues as $value) {
                    if (isset($driveTypeMapping[$value])) {
                        $mappedValues = array_merge($mappedValues, $driveTypeMapping[$value]);
                    }
                }

                // Remove duplicate values
                $mappedValues = array_unique($mappedValues);

                // Debugging the mapped values
                // dd($mappedValues);

                if ($request->has('allWebDriveTrainlName') && $request->allWebFuellName == 'allWebDriveTrainValue') {
                    // Your existing logic for this condition
                } else if ($request->has('autoWebDriveTrainCheckbox')) {
                    // Apply the mapped values to your query
                    $query->whereIn('drive_info', $mappedValues);
                }
            }


            // if ($request->autoMobileDriveTrainCheckbox != null) {
            //     if ($request->has('allMobileDriveTrainlName') && $request->allWebFuellName == 'allMobileDriveTrainValue') {
            //     } else if ($request->has('autoMobileDriveTrainCheckbox')) {
            //         $query->whereIn('drive_info', $request->autoMobileDriveTrainCheckbox);
            //     }
            // }


            if ($request->autoMobileDriveTrainCheckbox != null) {
                // dd($request->autoMobileDriveTrainCheckbox);
                // Mapping array
                $mobileDriveTypeMapping = [
                    '4WD' => ['Four-wheel Drive', '4WD'],
                    'AWD' => ['All-wheel Drive', 'AWD'],
                    'FWD' => ['Front-wheel Drive', 'FWD'],
                    'RWD' => ['Rear-wheel Drive', 'RWD'],
                    'Other' => ['Unknown', '–']
                ];

                $mobileSelectedValues = $request->autoMobileDriveTrainCheckbox;
                $mobileMappedValues = [];

                // Map selected checkboxes to database values
                foreach ($mobileSelectedValues as $value) {
                    if (isset($mobileDriveTypeMapping[$value])) {
                        $mobileMappedValues = array_merge($mobileMappedValues, $mobileDriveTypeMapping[$value]);
                    }
                }

                // Remove duplicate values
                $mobileMappedValues = array_unique($mobileMappedValues);

                if ($request->has('allWebDriveTrainlName') && $request->allWebDriveTrainlName == 'allWebDriveTrainValue') {
                    // Your existing logic for this condition
                } else if ($request->has('autoMobileDriveTrainCheckbox')) {
                    // Apply the mapped values to your query
                    $query->whereIn('drive_info', $mobileMappedValues);
                }

            }


            if ($request->mobileBody != null) {
                $mobile_body = $request->mobileBody;
                $query->Where('body_formated', 'LIKE', '%' . $mobile_body . '%');
            }


            if ($request->webBodyFilter != null) {
                $web_body = $request->webBodyFilter;
                $query->Where('body_formated', 'LIKE', '%' . $web_body . '%');
            }


            if ($request->webColorFilter != null) {
                $web_body = $request->webColorFilter;

                $query->where(function ($subQuery) use ($web_body) {
                    foreach ($web_body as $body) {
                        $subQuery->orWhere('exterior_color', 'LIKE', '%' . $body . '%');
                    }
                });
            }

            if ($request->autoMaxBodyCheckbox != null) {
                if ($request->autoMaxBodyCheckbox[0] == null) {
                    $query->whereNull('body_formated')->orWhereIn('body_formated', $request->autoMaxBodyCheckbox);
                } else {

                    $query->whereIn('body_formated', $request->autoMaxBodyCheckbox);
                }
            }
            if ($request->autoMobileMakeCheckbox != null) {
                $query->whereIn('make', $request->autoMobileMakeCheckbox);
            }
            if ($request->secondFilterMakeInputNew != null) {
                $query->where('make', $request->secondFilterMakeInputNew);
            }
            if ($request->secondFilterModelInputNew != null) {
                $query->where('model', $request->secondFilterModelInputNew);
            }
            if ($request->autoMobileMaxBodyCheckbox != null) {
                if ($request->autoMobileMaxBodyCheckbox[0] == null) {
                    $query->whereNull('body_formated')->orWhereIn('body_formated', $request->autoMobileMaxBodyCheckbox);
                } else {

                    $query->whereIn('body_formated', $request->autoMobileMaxBodyCheckbox);
                }
            }

            if ($homeBodySearch != null) {

                if ($homeBodySearch == 'SUV') {
                    $query->where('body_formated', 'suv / crossover');
                } elseif ($homeBodySearch == 'Electric') {
                    $query->where('fuel', 'Electric');
                } elseif ($homeBodySearch == 'Hybrid') {
                    $query->where('fuel', 'Hybrid');
                } else {
                    $query->where('body_formated', $homeBodySearch);
                }
            }


            // mobile filter start here
            if ($request->secondFilterZipInput != null) {
                $query->where('zip_code', $request->secondFilterZipInput);
            }
            if ($request->secondFilterMakeInput != null) {
                $query->where('make', $request->secondFilterMakeInput);
            }
            if ($request->secondFilterModelInput != null) {
                $query->where('model', $request->secondFilterModelInput);
            }
            if ($request->webMakeFilterMakeInput != null) {
                Cookie::queue(Cookie::forget('searchData'));
                $searchData = ['webMakeFilterMakeInput' => $request->webMakeFilterMakeInput];
                Cookie::queue('searchData', json_encode($searchData), 120);
                $query->where('make', $request->webMakeFilterMakeInput);
            }
            if ($request->webModelFilterInput != null) {
                $query->where('model', $request->webModelFilterInput);
            }
            if ($request->totalLoanAmountCalculation != null) {
                $format_price  = intVal(str_replace(',', '', $request->totalLoanAmountCalculation));
                $query->whereBetween('payment_price', [0, $format_price]);
            }

            if ($request->mobileColorFilter != null) {
                $mobile_color = $request->mobileColorFilter;
                $query->where(function ($subQuery) use ($mobile_color) {
                    foreach ($mobile_color as $mobile_color) {
                        $subQuery->orWhere('exterior_color', 'LIKE', '%' . $mobile_color . '%');
                    }
                });
            }


            // mobile filter end here
        } else {
            if ($lowestValue != null) {
                $query->orderBy('price');
            }
            if ($lowestMileageValue != null) {
                $query->orderBy('miles');
            }
        }


        // Apply filters based on the request
        if ($request->has('make_data') && !empty($request->make_data)) {
            $query->where('make', $request->make_data);
        }

        if ($request->has('dealer_name') && !empty($request->dealer_name)) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('name', $request->dealer_name);
            });
        }

        if ($request->has('dealer_city') && !empty($request->dealer_city)) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('city', $request->dealer_city);
            });
        }

        if ($request->has('dealer_state') && !empty($request->dealer_state)) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('state', $request->dealer_state);
            });
        }

        if ($request->has('inventory_date') && !empty($request->inventory_date)) {
            $query->whereDate('created_at', $request->inventory_date);
        }


        // if ($id != null) {
        //     $query->where('deal_id', $id)->where(['status' => '1', 'is_visibility' => '1']);
        // } else {
        //     $query->where(['status' => '1', 'is_visibility' => '1']);
        // }

        return $query;
    }


    public function getItemByFilterOnly($request, $id = null)
    {

        $requestURL = $request->requestURL;
        $urlComponents = parse_url($requestURL);
        $queryString = $urlComponents['query'] ?? '';
        parse_str($queryString, $queryParams);
        $lowestValue = $queryParams['lowestPrice'] ?? null;
        $bestDealValue = $queryParams['bestDeal'] ?? null;
        $lowestMileageValue = $queryParams['lowestMileage'] ?? null;
        $ownedValue = $queryParams['owned'] ?? null;
        $makeTypeSearchValue = $queryParams['makeTypeSearch'] ?? null;
        $homeBodySearch = $queryParams['body'] ?? null;
        $homepage = $queryParams['home'] ?? null;
        $hometypeSearch = $queryParams['homeBodySearch'] ?? null;
        $homeMakeSearch = $queryParams['make'] ?? null;
        $homeModelSearch = $queryParams['model'] ?? null;
        $homePriceSearch = $queryParams['maximum_price'] ?? null;
        $homeDealerCitySearch = $queryParams['homeDealerCitySearch'] ?? null;
        $homeDealerStateSearch = $queryParams['homeDealerStateSearch'] ?? null;
        $homeLocationSearch = $queryParams['zip'] ?? null;
        $homeRadiusSearch = $queryParams['radius'] ?? null;
        // $homeLocationSearch2 = $queryParams['homeLocationSearch2'] ?? null;
        $homeMileageSearch = $queryParams['maximum_miles'] ?? null;
        $homeMinMileageSearch = $queryParams['min-miles'] ?? null;
        $homeMaxMileageSearch = $queryParams['max-miles'] ?? null;

        $homeMinPayment = $queryParams['min_payment'] ?? null;
        $homeMaxPayment = $queryParams['max_payment'] ?? null;
        $homeMinYear = $queryParams['min_year'] ?? null;
        $homeMaxYear = $queryParams['max_year'] ?? null;
        $minPriceBody = $queryParams['min_price'] ?? null;
        $maxPriceBody = $queryParams['max_price'] ?? null;
        $hfuel = $queryParams['hfuel'] ?? null;

        $zipCode  = $homeLocationSearch;
        $countryCode = 'US';

        //dd($homepage);

        //****************** */ saved local storage all search data **********************************************************

        if ($hometypeSearch == 'new') {
            // dd($request->all());
            $searchData = [
                'newfirstzipFilter' => $request->firstzipFilter,
                'newfirstMakeFilter' => $request->firstMakeFilter,
                'newfirstModelFilter' => $request->firstModelFilter,
                'newweb_search_any' => $request->web_search_any,
                'newmakeCheckdata' => $request->makeCheckdata,
                'newautoMaxBodyCheckbox' => $request->autoMaxBodyCheckbox,
                'newautoMinYearCheckbox' => $request->autoMinYearCheckbox,
                'newautoMaxYearCheckbox' => $request->autoMaxYearCheckbox,
                'newrangerMinPriceSlider' => $request->rangerMinPriceSlider,
                'newrangerMaxPriceSlider' => $request->rangerMaxPriceSlider,
                'newrangerMileageMinPriceSlider' => $request->rangerMileageMinPriceSlider,
                'newrangerMileageMaxPriceSlider' => $request->rangerMileageMaxPriceSlider,
                'newrangerYearMinPriceSlider' => $request->rangerYearMinPriceSlider,
                'newrangerYearMaxPriceSlider' => $request->rangerYearMaxPriceSlider,
                'newtotalLoanAmountCalculation' => $request->totalLoanAmountCalculation,
                'newautoWebTransmissionCheckbox' => $request->autoWebTransmissionCheckbox,
                'newautoWebFuelCheckbox' => $request->autoWebFuelCheckbox,
                'newautoWebDriveTrainCheckbox' => $request->autoWebDriveTrainCheckbox ??  $request->autoMobileDriveTrainCheckbox,
                'newwebColorFilter' => $request->webColorFilter,
                'newwebBodyFilter' => $homeBodySearch,
                // mobile version filter data
                'newmobileRangerMinPriceSlider' => $request->mobileRangerMinPriceSlider,
                'newmobileRangerMaxPriceSlider' => $request->mobileRangerMaxPriceSlider,
                'newmobileMileageRangerMinPriceSlider' => $request->mobileMileageRangerMinPriceSlider,
                'newmobileMileageRangerMaxPriceSlider' => $request->mobileMileageRangerMaxPriceSlider,
                'newmobileYearRangerMinPriceSlider' => $request->mobileYearRangerMinPriceSlider,
                'newmobileYearRangerMaxPriceSlider' => $request->mobileYearRangerMaxPriceSlider,
                'newautoMobileTypeCheckbox' => $request->autoMobileTypeCheckbox,
                'newsecondFilterMakeInputNew' => $request->secondFilterMakeInputNew,
                'newsecondFilterModelInputNew' => $request->secondFilterModelInputNew,
                'newautoMobileFuelCheckbox' => $request->autoMobileFuelCheckbox,
                'newautoMobileTransmissionCheckbox' => $request->autoMobileTransmissionCheckbox,
                'newmobileBody' => $homeBodySearch,
                'newmobileColorFilter' => $request->mobileColorFilter,
            ];



            Cookie::queue('searchData', json_encode($searchData), 120);
        } else {

            $searchData = [
                'firstzipFilter' => $request->firstzipFilter,
                'firstMakeFilter' => $request->firstMakeFilter,
                'firstModelFilter' => $request->firstModelFilter,
                'web_search_any' => $request->web_search_any,
                'makeCheckdata' => $request->makeCheckdata,
                'autoMaxBodyCheckbox' => $request->autoMaxBodyCheckbox,
                'autoMinYearCheckbox' => $request->autoMinYearCheckbox,
                'autoMaxYearCheckbox' => $request->autoMaxYearCheckbox,
                'rangerMinPriceSlider' => $request->rangerMinPriceSlider,
                'rangerMaxPriceSlider' => $request->rangerMaxPriceSlider,
                'rangerMileageMinPriceSlider' => $request->rangerMileageMinPriceSlider,
                'rangerMileageMaxPriceSlider' => $request->rangerMileageMaxPriceSlider,
                'rangerYearMinPriceSlider' => $request->rangerYearMinPriceSlider,
                'rangerYearMaxPriceSlider' => $request->rangerYearMaxPriceSlider,
                'totalLoanAmountCalculation' => $request->totalLoanAmountCalculation,
                'autoWebConditionCheckbox' => $request->autoWebConditionCheckbox,
                'autoWebTransmissionCheckbox' => $request->autoWebTransmissionCheckbox,
                'autoWebFuelCheckbox' => $request->autoWebFuelCheckbox,
                'autoWebDriveTrainCheckbox' => $request->autoWebDriveTrainCheckbox ??  $request->autoMobileDriveTrainCheckbox,
                'webColorFilter' => $request->webColorFilter,
                'webMakeFilterMakeInput' => $request->webMakeFilterMakeInput,
                'webBodyFilter' =>  $homeBodySearch,
                'mobileBody' => $homeBodySearch,
                // mobile version filter data
                'mobileRangerMinPriceSlider' => $request->mobileRangerMinPriceSlider,
                'mobileRangerMaxPriceSlider' => $request->mobileRangerMaxPriceSlider,
                'mobileMileageRangerMinPriceSlider' => $request->mobileMileageRangerMinPriceSlider,
                'mobileMileageRangerMaxPriceSlider' => $request->mobileMileageRangerMaxPriceSlider,
                'mobileYearRangerMinPriceSlider' => $request->mobileYearRangerMinPriceSlider,
                'mobileYearRangerMaxPriceSlider' => $request->mobileYearRangerMaxPriceSlider,
                'autoMobileTypeCheckbox' => $request->autoMobileTypeCheckbox,
                'secondFilterMakeInputNew' => $request->secondFilterMakeInputNew,
                'secondFilterModelInputNew' => $request->secondFilterModelInputNew,
                'autoMobileFuelCheckbox' => $request->autoMobileFuelCheckbox,
                'autoMobileTransmissionCheckbox' => $request->autoMobileTransmissionCheckbox,
                'mobileColorFilter' => $request->mobileColorFilter,

                'webExteriorColorFilter' => $request->webExteriorColorFilter,
                'webInteriorColorFilter' => $request->webInteriorColorFilter,
            ];

            Cookie::queue('searchData', json_encode($searchData), 120);
        }


        // dd($request->all());
        // $query = Inventory::with('dealer');
        // $query = Inventory::with('dealer');
        $query = MainInventory::with('dealer', 'mainPriceHistory', 'additionalInventory');

        //  query end here //  query end here //  query end here //  query end here //  query end here //  query end here //  query end here

        if ($request->web_search_any) {
            $searchWords = explode(' ', $request->web_search_any);

            $query->where(function ($subquery) use ($searchWords) {
                $subquery->where(function ($subquery2) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $subquery2->where(function ($subquery3) use ($word) {
                            $subquery3->where('make', 'like', '%' . $word . '%')
                                ->orWhere('model', 'like', '%' . $word . '%')
                                ->orWhere('stock', 'like', '%' . $word . '%')
                                ->orWhere('year', 'like', '%' . $word . '%')
                                ->orWhere('zip_code', 'like', '%' . $word . '%')
                                ->orWhere('vin', 'like', '%' . $word . '%');
                            // ->orWhere('body_formated ', 'like', '%' . $word . '%');
                        });
                    }
                })
                    ->orWhere(function ($subquery4) use ($searchWords) {
                        $subquery4->whereRaw("CONCAT_WS('', year, make, model,vin ) LIKE ?", ['%' . implode('%', $searchWords) . '%']);
                    });
            });
        }

        $sortMapping = [
            'datecreated|desc' => ['stock_date_formated', 'desc'],
            'datecreated|asc' => ['stock_date_formated', 'asc'],
            'searchprice|asc' => ['price', 'asc'],
            'searchprice|desc' => ['price', 'desc'],
            'mileage|asc' => ['miles', 'asc'],
            'mileage|desc' => ['miles', 'desc'],
            'modelyear|asc' => ['year', 'asc'],
            'modelyear|desc' => ['year', 'desc'],
            'payment|asc' => ['payment_price', 'asc'],
            'payment|desc' => ['payment_price', 'desc']
        ];



        //Cookie::queue('selected_sort_search',$request->selected_sort_search, 120);
        Session::put('selected_sort_search', $request->selected_sort_search);

        if (isset($sortMapping[$request->selected_sort_search])) {
            $query->orderBy($sortMapping[$request->selected_sort_search][0], $sortMapping[$request->selected_sort_search][1]);
        }

        if ($request->mobile_web_search_any) {
            $searchWords = explode(' ', $request->mobile_web_search_any);

            $query->where(function ($subquery) use ($searchWords) {
                $subquery->where(function ($subquery2) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $subquery2->where(function ($subquery3) use ($word) {
                            $subquery3->where('make', 'like', '%' . $word . '%')
                                ->orWhere('model', 'like', '%' . $word . '%')
                                ->orWhere('stock', 'like', '%' . $word . '%')
                                ->orWhere('year', 'like', '%' . $word . '%')
                                ->orWhere('zip_code', 'like', '%' . $word . '%')
                                ->orWhere('vin', 'like', '%' . $word . '%');
                            // ->orWhere('body_formated ', 'like', '%' . $word . '%');
                        });
                    }
                })
                    ->orWhere(function ($subquery4) use ($searchWords) {
                        $subquery4->whereRaw("CONCAT_WS('', year, make, model,vin ) LIKE ?", ['%' . implode('%', $searchWords) . '%']);
                    });
            });
        }


        // may be no need
        if ($homeDealerCitySearch != null) {
            $query->whereHas('dealer', function ($q) use ($homeDealerCitySearch) {
                $q->where('city', 'like', '%' . $homeDealerCitySearch . '%');
            });
        }

        if ($lowestValue == null && $lowestMileageValue == null && $ownedValue == null) {

            if ($makeTypeSearchValue != null) {
                $query->where('make', $makeTypeSearchValue);
            }

            if ($homeMakeSearch != null) {
                $query->where('make', $homeMakeSearch);
            }
            if ($homeModelSearch != null) {
                $query->where('model', $homeModelSearch);
            }


            if ($homePriceSearch != null) {
                switch ($homePriceSearch) {
                    case "0":
                        $query->where('price', '<=', 5000);
                        break;

                    case "1":
                        $query->where('price', '<=', 10000);
                        // $query->whereBetween('price', [5000, 10000]);
                        break;

                    case "2":
                        $query->where('price', '<=', 20000);
                        break;

                    case "3":
                        $query->where('price', '<=', 30000);
                        break;

                    case "4":
                        $query->where('price', '<=', 40000);
                        break;

                    case "5":
                        $query->where('price', '<=', 50000);
                        break;

                    case "6":
                        $query->where('price', '<=', 60000);
                        break;

                    case "7":
                        $query->where('price', '<=', 70000);
                        break;
                    case "8":
                        $query->where('price', '<=', 80000);
                        break;

                    default:
                        $query->where('price', '<=', 100000);
                        break;
                }
            }

            if ($homeMileageSearch != null) {
                $query->where('miles', '<=', $homeMileageSearch);
            }

            // if ($minPriceBody != null || $maxPriceBody != null) {
            //     $minValue = ($minPriceBody != null) ? $minPriceBody : 0;
            //     $maxValue = ($maxPriceBody != null) ? $maxPriceBody : 1000000;
            //     $query->whereBetween('price', [$minValue, $maxValue]);
            // }

            if ($minPriceBody  || $maxPriceBody) {
                $minValue = ($minPriceBody !== null) ? $minPriceBody : 0;
                $maxValue = ($maxPriceBody !== null) ? $maxPriceBody : 150000;

                // If the max value is 150000, it means we need to show all values
                if ($maxValue == 150000) {
                    // $minPrice_provide = $this->priceRange['used']['minPrice'];
                    // $maxPrice_provide = $this->priceRange['used']['maxPrice'];
                    // // $query->where('price', '>=', $minValue);
                    // $query->whereBetween('price', [$minPrice_provide, $maxPrice_provide]);
                } else {
                    $query->whereBetween('price', [$minValue, $maxValue]);
                }
            }

            if ($homeMinMileageSearch || $homeMaxMileageSearch) {
                $minMileage = ($homeMinMileageSearch !== null) ? $homeMinMileageSearch : 0;
                $maxMileage = ($homeMaxMileageSearch !== null) ? $homeMaxMileageSearch : 150000;

                if ($maxMileage == 150000) {
                    // // $query->where('miles', '>=', $minMileage);
                    // // $minPrice = $this->priceRange['new']['minMiles'];
                    // $minMiles_provide = $this->priceRange['used']['minMiles'];
                    // $maxMiles_provide = $this->priceRange['used']['maxMiles'];

                    // $query->whereBetween('miles', [$minMiles_provide, $maxMiles_provide]);

                } else {
                    $query->whereBetween('miles', [$minMileage, $maxMileage]);
                }
            }



            if (($request->rangerMinPriceSlider != null || $request->rangerMaxPriceSlider != null)) {
                $minValue = ($request->rangerMinPriceSlider != null) ? $request->rangerMinPriceSlider : 0;
                $maxValue = ($request->rangerMaxPriceSlider != null) ? $request->rangerMaxPriceSlider : 150000;
                // dd($request->rangerMileageMinPriceSlider, $request->rangerMileageMaxPriceSlider);
                if ($minValue > 150000) {
                    $query->whereNotNull('price');
                } else {
                    $query->whereBetween('price', [$minValue, $maxValue]);
                }
            }

            if ($request->rangerMileageMinPriceSlider != null || $request->rangerMileageMaxPriceSlider != null) {


                $minValue = ($request->rangerMileageMinPriceSlider != null) ? $request->rangerMileageMinPriceSlider : 0;
                $maxValue = ($request->rangerMileageMaxPriceSlider != null) ? $request->rangerMileageMaxPriceSlider : 150000;

                if ($maxValue < 150000) {
                    // If the max value is less than 150000, use a normal between range query
                    $query->whereBetween('miles', [$minValue, $maxValue]);
                } else {
                    // If the max value is 150000 or more, show all vehicles with miles >= minValue
                    $query->where('miles', '>=', $minValue);
                }
            }

            if ($request->rangerYearMinPriceSlider != null || $request->rangerYearMaxPriceSlider != null) {
                $minValue = ($request->rangerYearMinPriceSlider != null) ? $request->rangerYearMinPriceSlider : 1980;
                $maxValue = ($request->rangerYearMaxPriceSlider != null) ? $request->rangerYearMaxPriceSlider : 2024;

                $query->whereBetween('year', [$minValue, $maxValue]);
            }

            if ($request->mobileRangerMinPriceSlider != null || $request->mobileRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileRangerMinPriceSlider != null) ? $request->mobileRangerMinPriceSlider : 0;
                $maxValue = ($request->mobileRangerMaxPriceSlider != null) ? $request->mobileRangerMaxPriceSlider : 150000;

                if ($minValue > 150000) {
                    $query->whereNotNull('price');
                } else {
                    $query->whereBetween('price', [$minValue, $maxValue]);
                }

                // if ($maxValue < 150000) {
                //     // If the max value is less than 150000, use a normal between range query
                //     $query->whereBetween('price', [$minValue, $maxValue]);
                // } else {
                //     // If the max value is 150000 or more, show all vehicles with miles >= minValue
                //     $query->where('price', '>=', $minValue);
                // }
            }

            if ($request->mobileMileageRangerMinPriceSlider != null || $request->mobileMileageRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileMileageRangerMinPriceSlider != null) ? $request->mobileMileageRangerMinPriceSlider : 0;
                $maxValue = ($request->mobileMileageRangerMaxPriceSlider != null) ? $request->mobileMileageRangerMaxPriceSlider : 1000000;
                if ($maxValue < 150000) {
                    // If the max value is less than 150000, use a normal between range query
                    $query->whereBetween('miles', [$minValue, $maxValue]);
                } else {
                    // If the max value is 150000 or more, show all vehicles with miles >= minValue
                    $query->where('miles', '>=', $minValue);
                }
                // $query->whereBetween('miles', [$minValue, $maxValue]);
            }

            if ($request->mobileYearRangerMinPriceSlider != null || $request->mobileYearRangerMaxPriceSlider != null) {
                $minValue = ($request->mobileYearRangerMinPriceSlider != null) ? $request->mobileYearRangerMinPriceSlider : 1985;
                $maxValue = ($request->mobileYearRangerMaxPriceSlider != null) ? $request->mobileYearRangerMaxPriceSlider : 2024;

                $query->whereBetween('year', [$minValue, $maxValue]);
            }
            if ($homeMinPayment != null || $homeMaxPayment != null) {
                $minPaymentValue = ($homeMinPayment != null) ? $homeMinPayment : 0;
                $maxPaymentValue = ($homeMaxPayment != null) ? $homeMaxPayment : 5000;

                $query->whereBetween('payment_price', [$minPaymentValue, $maxPaymentValue]);
            }

            // if ($homeMinYear != null || $homeMaxYear != null) {
            //     $minYearValue = ($homeMinYear != null) ? $homeMinYear : 1985;
            //     $maxYearValue = ($homeMaxYear != null) ? $homeMaxYear : date('yyyy');

            //     $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            // }

            if ($homeMinYear != null || $homeMaxYear != null) {
                $minYearValue = ($homeMinYear != null) ? $homeMinYear : 1985;
                $maxYearValue = ($homeMaxYear != null) ? $homeMaxYear : date('Y');

                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($hfuel != null) {
                if ($hfuel == 'electric') {
                    $query->where('fuel', 'Plug-in Gas/Electric Hybrid');
                }
                if ($hfuel == 'hybrid') {
                    $query->where('fuel', 'Hybrid');
                }
            }


            if ($request->autoMinYearCheckbox != null || $request->autoMaxYearCheckbox != null) {
                $minYearValue = ($request->autoMinYearCheckbox != null) ? $request->autoMinYearCheckbox : 1985;
                $maxYearValue = ($request->autoMaxYearCheckbox != null) ? $request->autoMaxYearCheckbox : date('yyyy');
                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($request->autoMobileMinYearCheckbox != null || $request->autoMobileMaxYearCheckbox != null) {
                $minYearValue = ($request->autoMobileMinYearCheckbox != null) ? $request->autoMobileMinYearCheckbox : 1985;
                $maxYearValue = ($request->autoMobileMaxYearCheckbox != null) ? $request->autoMobileMaxYearCheckbox : date('yyyy');
                $query->whereBetween('year', [$minYearValue, $maxYearValue]);
            }

            if ($request->firstzipFilter != null) {
                $query->where('zip_code', $request->firstzipFilter);
            }



            if ($request->webCity != null) {
                $query->whereHas('dealer', function ($q) use ($request) {
                    $q->where('city', $request->webCity);
                });
            }



            if ($request->firstMakeFilter != null) {
                $query->where('make', $request->firstMakeFilter);
            }
            if ($request->firstModelFilter != null) {
                $query->where('model', $request->firstModelFilter);
            }
            if ($request->makeCheckdata != null) {
                $query->whereIn('make', $request->makeCheckdata);
            }

            if ($request->has('autoMobileTypeCheckbox')) {
                $mobileSelectedTypes = $request->autoMobileTypeCheckbox;


                // Initialize an empty array to collect the types for filtering
                $types = [];

                if (in_array('Certified', $mobileSelectedTypes)) {
                    $types[] = 'preowned certified';
                }
                if (in_array('Preowned', $mobileSelectedTypes)) {
                    $types[] = 'Used';
                }
                if (in_array('New', $mobileSelectedTypes)) {
                    $types[] = 'New';
                }

                // Apply the query only if there are selected types
                if (!empty($types)) {
                    $query->whereIn('type', $types);
                }
            }


            if ($request->has('autoWebConditionCheckbox')) {
                $selectedTypes = $request->autoWebConditionCheckbox;
                // dd($selectedTypes);
                $types = [];

                if (in_array('Certified', $selectedTypes)) {
                    // $types[] = 'Certified Preowned';
                    $types[] = 'preowned certified';
                }
                if (in_array('Preowned', $selectedTypes)) {
                    $types[] = 'Used';
                }
                if (in_array('New', $selectedTypes)) {
                    $types[] = 'New';
                }

                // Apply the query only if there are selected types
                if (!empty($types)) {
                    $query->whereIn('type', $types);
                }
            }

            if ($request->autoMobileFuelCheckbox != null) {
                if ($request->has('allFuelName') && $request->allFuelName == 'allFuelValue') {
                    // "All" is selected, so no filter is applied
                } else if ($request->has('autoMobileFuelCheckbox')) {
                    $query->whereIn('fuel', $request->autoMobileFuelCheckbox);
                }
            }



            if ($request->autoMobileTransmissionCheckbox != null) {
                if ($request->has('allTransmissionlName') && $request->allTransmissionlName == 'allTransmissionValue') {
                    // "All" is selected, so no filter is applied
                } else if ($request->has('autoMobileTransmissionCheckbox')) {
                    $transmissions = $request->autoMobileTransmissionCheckbox;
                    $query->where(function ($subQuery) use ($transmissions) {
                        foreach ($transmissions as $transmission) {
                            if (trim($transmission) == 'automatic') {
                                $subQuery->orWhere('transmission', 'LIKE', '%automatic%')
                                    ->orWhere('transmission', 'LIKE', '%variable%');
                            } else {
                                $subQuery->orWhere('transmission', 'LIKE', '%' . trim($transmission) . '%');
                            }
                        }
                    });
                }
            }



            if ($request->autoWebTransmissionCheckbox != null) {
                if ($request->has('allWebTransmissionlName') && $request->allWebTransmissionlName == 'allWebTransmissionValue') {
                } else if ($request->has('autoWebTransmissionCheckbox')) {
                    $Web_transmissions = $request->autoWebTransmissionCheckbox;
                    $query->where(function ($subQuery) use ($Web_transmissions) {
                        foreach ($Web_transmissions as $transmission_info) {
                            if (trim($transmission_info) == 'automatic') {
                                $subQuery->orWhere('transmission', 'LIKE', '%automatic%')
                                    ->orWhere('transmission', 'LIKE', '%variable%');
                            } else {
                                $subQuery->orWhere('transmission', 'LIKE', '%' . trim($transmission_info) . '%');
                            }
                        }
                    });
                }
            }

            // if ($request->autoWebFuelCheckbox != null) {
            //     if ($request->has('allWebFuellName') && $request->allWebFuellName == 'allWebFuelValue') {
            //     } else if ($request->has('autoWebFuelCheckbox')) {
            //         $query->whereIn('fuel', $request->autoWebFuelCheckbox);
            //     }
            // }

            if ($request->autoWebFuelCheckbox != null) {
                // dd($request->autoWebFuelCheckbox);
                $fuelTypeMapping = [
                    'Diesel' => ['Diesel', 'Diesel (B20 capable)'],
                    'Electric' => ['Electric fuel type', 'BEV (battery electric vehicle)', 'MHEV (mild hybrid electric vehicle)', 'All-Electric'],
                    'Flex Fuel' => ['Race Fuel', 'flex_fuel', 'Flexible Fuel', 'E85 Flex Fuel'],
                    'Gasoline' => ['Plug-In/Gas', 'Gasoline Fuel', 'Gaseous', 'Gasoline fuel type', 'Gasolin Fuel', 'Gasoline', 'Regular Unleaded', 'Premium Unleaded', 'Gaseous Fuel Compatible', 'Ethanol'],
                    'Hybrid' => ['Full Hybrid Electric (FHEV)', 'Electric Performance Hybrid', 'Hybrid Fuel', 'Gasoline/Mild Electric Hybrid', 'Hybrid'],
                    'Hydrogen Fuel Cell' => ['Hydrogen Fuel Cell'],
                    'Plug In Hybrid' => ['Plug-in Gas/Electric Hybrid','PHEV (plug-in hybrid electric vehicle)', 'Phev', 'Plug-In Hybrid'],
                    'Compressed Natural Gas' => ['Natural Gas', 'Gas/CNG', 'Gasoline / Natural Gas', 'Compressed Natural Gas'],
                    'Other' => ['Other', '–', '----', 'Unspecified']
                ];

                $selectedValues = $request->autoWebFuelCheckbox;
                $mappedFuelValues = [];

                foreach ($selectedValues as $value) {
                    if (isset($fuelTypeMapping[$value])) {
                        $mappedFuelValues = array_merge($mappedFuelValues, $fuelTypeMapping[$value]);
                    }
                }

                $mappedFuelValues = array_unique($mappedFuelValues);

                if ($request->has('allWebFuelName') && $request->allWebFuellName == 'allWebFuelValue') {
                    // Logic for 'all' selection
                } else if ($request->has('autoWebFuelCheckbox')) {
                    $query->whereIn('fuel', $mappedFuelValues);
                }
            }

            // // // For Mobile Exterior Color
            // if ($request->autoMobileExteriorColorCheckbox != null) {
            //     // Map the selected colors with wildcards to search in database
            //     $colors = array_map(function($color) {
            //         return '%' . $color . '%'; // Add wildcard (%) for partial matching
            //     }, $request->autoMobileExteriorColorCheckbox);

            //     // Apply the query with OR conditions for matching any of the selected colors
            //     $query->where(function ($query) use ($colors) {
            //         $query->whereRaw(implode(' OR ', array_fill(0, count($colors), 'exterior_color LIKE ?')), $colors);
            //     });
            // }

            if ($request->autoMobileExteriorColorCheckbox != null) {
                $query->where(function ($query) use ($request) {
                    foreach ($request->autoMobileExteriorColorCheckbox as $color) {
                        $query->orWhere('exterior_color', 'LIKE', '%' . $color . '%');
                    }
                });
            }

            if ($request->autoMobileInteriorColorCheckbox != null) {
                $query->where(function ($query) use ($request) {
                    foreach ($request->autoMobileInteriorColorCheckbox as $color) {
                        $query->orWhere('interior_color', 'LIKE', '%' . $color . '%');
                    }
                });
            }

            // // For Mobile Interior Color
            // if ($request->autoMobileInteriorColorCheckbox != null) {
            //     // Map the selected colors with wildcards to search in database
            //     $colors = array_map(function($color) {
            //         return '%' . $color . '%'; // Add wildcard (%) for partial matching
            //     }, $request->autoMobileInteriorColorCheckbox);

            //     // Apply the query with OR conditions for matching any of the selected colors
            //     $query->where(function ($query) use ($colors) {
            //         $query->whereRaw(implode(' OR ', array_fill(0, count($colors), 'interior_color LIKE ?')), $colors);
            //     });
            // }



                // // // dd($request->autoWebExteriorColorCheckbox, $request->autoWebInteriorColorCheckbox);
                // $selectedExteriorColors = $request->autoWebExteriorColorCheckbox;  // Get selected exterior colors from request
                // $selectedInteriorColors = $request->autoWebInteriorColorCheckbox;  // Get selected interior colors from request

                // $allColors = [
                //     "Beige", "Black", "Blue", "Brown", "Gold", "Gray", "Green", "Orange", "Pink", "Purple",
                //     "Red", "Silver", "White", "Yellow", "Other"
                // ];

                // // Handle Exterior Color filtering
                // if ($selectedExteriorColors != null) {
                //     $exteriorColorQueries = [];

                //     // Loop through each selected exterior color and prepare the LIKE queries
                //     foreach ($selectedExteriorColors as $color) {
                //         if (in_array($color, $allColors)) {
                //             $exteriorColorQueries[] = ['exterior_color', 'like', '%' . $color . '%'];
                //         }
                //     }

                //     // If no exterior color matches, show "Other"
                //     if (empty($exteriorColorQueries)) {
                //         $query->where(function($q) {
                //             $q->where('exterior_color', 'like', '%Other%');
                //         });
                //     } else {
                //         // Apply the exterior color filtering
                //         foreach ($exteriorColorQueries as $colorQuery) {
                //             $query->orWhere($colorQuery[0], $colorQuery[1], $colorQuery[2]);
                //         }
                //     }
                // }

                // // Handle Interior Color filtering
                // if ($selectedInteriorColors != null) {
                //     $interiorColorQueries = [];

                //     // Loop through each selected interior color and prepare the LIKE queries
                //     foreach ($selectedInteriorColors as $color) {
                //         if (in_array($color, $allColors)) {
                //             $interiorColorQueries[] = ['interior_color', 'like', '%' . $color . '%'];
                //         }
                //     }

                //     // If no interior color matches, show "Other"
                //     if (empty($interiorColorQueries)) {
                //         $query->where(function($q) {
                //             $q->where('interior_color', 'like', '%Other%');
                //         });
                //     } else {
                //         // Apply the interior color filtering
                //         foreach ($interiorColorQueries as $colorQuery) {
                //             $query->orWhere($colorQuery[0], $colorQuery[1], $colorQuery[2]);
                //         }
                //     }
                // }


                if ($request->autoWebExteriorColorCheckbox != null) {
                    $query->where(function ($query) use ($request) {
                        foreach ($request->autoWebExteriorColorCheckbox as $color) {
                            $query->orWhere('exterior_color', 'LIKE', '%' . $color . '%');
                        }
                    });
                }

                // if ($request->autoWebInteriorColorCheckbox != null) {
                //     $query->where(function ($query) use ($request) {
                //         foreach ($request->autoWebInteriorColorCheckbox as $color) {
                //             $query->orWhere('interior_color', 'LIKE', '%' . $color . '%');
                //         }
                //     });
                // }

                if ($request->autoWebInteriorColorCheckbox != null) {
                    // dd($request->autoWebInteriorColorCheckbox);
                    $exteriorColorMapping = [
                        'Beigh' => ['Beige', 'Macchiato Beige/Black', 'Silk Beige/Black', 'Macchiato Beige/Space Gray', 'Teak/Light Shale', 'Silk Beige / Black', 'Chateau', 'Macchiato Beige', 'Amber Nappa', 'Macchiato Beige/Magma Gray', 'Atlas Beige', 'Chateau W/Linear Dark Mocha Wo', 'Jet/Tonal Stitch', 'Sand', 'Wicker Beige/Black', 'Pearl Beige', 'Sahara Beige w/Jet Black Accents', 'Cattle Tan/Black', 'Cardamom Beige', 'Glazed Caramel', 'Bisque', 'Cornsilk Beige', 'Dark Cashmere', 'Shetland Beige', 'Light Frost Beige/Black', 'Pistachio Beige', 'Ginger Beige/Espresso', 'Canberra Beige', 'Whisper Beige with Gideon accents', 'Siena Tan/Ebony/Siena Tan', 'Lt Oyster/Ebony/Lt Oyster', 'Ivory/Ebony/Ivory/Ivory', 'Pimento/Eb/Eb/Pim/Ebony', 'Shetland Beige & Black', 'Saiga Beige', 'Sand Beige', 'Stone Beige', 'Canberra Beige/Black', 'Wicker Beige/Global Black', 'Cornsilk Beige W/Brown', 'Parchment with Jet Black accents', 'Ginger Beige/Black', 'Silk Beige/Espresso Brown', 'Beige w/Nappa Leather Seati', 'Silk Beige', 'Light Oyster/Ebony', 'Pearl Beige W/Agate Gray', 'AMG Silk Beige', 'Whisper Beige w/Gideon Accents', 'Sonoma Beige Full Merino', 'Luxor Beige', 'Almond Beige/Mocha', 'Shale with Cocoa Accents', 'Light Oyster/Ebony/Ebony/Light Oyster', 'Silk Beige Black', 'Almond/Beige', 'Macchiato Beige/Grey', 'Tan / Red', 'Harvest Beige', 'Light Neutral w/Ebony Accents', 'Shara Beige', 'Black/Beige', 'Saddle/Black', 'Black/Limestone Beige', 'Cornsilk Beige / Brown', 'Beige/Black', 'Venetian Beige', 'Cornsilk Beige Leatherette Interior', 'Beige / Brown', 'Black/Lt Frost Beige', 'Macchiato Beige/Space Grey', 'Dark Beige', 'Beige/Black leatherette', 'Atlas Beige / Gray', 'Caraway/Ebony', 'Beige Leather Interior', 'Black/Luxor Beige', 'Beige / Black', 'Dark Beige/Titan Black', 'Beige Cloth Interior', 'Wicker Beige', 'Pimento / Ebony', 'Cattle Tan / Black', 'Cream Beige', 'Almond Beige', 'Atlas Beige/Light Carpet', 'Alpaca Beige Duragrain Interior', 'Sand Beige/Black', 'Savanna Beige', 'Cashmere and Ebony Leather Interior', 'Sahara Beige', 'Light Frost Beige/Mountain Brown', 'Canyon Brown/Lt Beige', 'Beige/Gray', 'Lt Frost Beige/Black', '2-Tone Black/Desert Beige', 'Shale With Ebony Interior Accents', 'Beige Tradizione', 'Atacama Beige/Basalt Black', 'Bei/Beige', 'Beige Two-tone', 'Black/Light Frost Beige Interior', 'Light Pebble Beige/Bark Brown Interior', 'Cirrus w/Dark Titanium Accents', 'Whisper Beige', 'Ginger Beige/Espresso Brown', 'Shale w/Cocoa Accents', 'Light Cashmere w/Medium Cashmere Accents', 'Velvet Beige', 'Ginger Beige', 'Light Pebble Beige/Bark Brown', 'Macchiato Beige / Black', 'designo Saddle Brown/Silk Beige', 'Veneto Beige', 'Mahogany/Silk Beige', 'Whisper Beige With Ebony Interior Accents', 'Havanna/Sand Beige', 'Soft Beige', 'Macchiato Beige/Espresso Brown', 'Cashmere Beige/Black', 'Pure Beige', 'Whisper Beige w/Ebony Accents', 'Dark Frost Beige/Medium Frost Beige', 'Whisper Beige seats with Ebony interior accents', 'Whisper Beige w/Jet Black Accents', 'Whisper Beige seats', 'Vintage Tan/Ebony', 'Desert Beige', 'Sahara Beige/Mocha', 'Light Beige', 'Cashmere/Ebony', 'Silk Beige/Black MB-Tex', 'Light Frost Beige/Canyon Brown', 'Almond/Ebony', 'Macchiato Beige / Bronze Brown Pearl', 'Whisper Beige W/ Ebony Accents', 'Velvet Beige / Brown', 'Glacier/Ebony/Glacier', 'Mojave Beige/Black', 'Caramel / Ebony', 'Beige/Tan', 'Light Frost Beige / Black', 'designo Macchiato Beige / Saddle Brown', 'Shetland Beige V-Tex Leatherette', 'designo Macchiato Beige', 'Calm Beige', 'Silk Beige/Espresso', 'Pebble Beige', 'Taupe/Pearl Beige', 'Glacier/Ebony', 'Mojave Beige', 'Macchiato Beige/Magma Grey', 'Macchiato Beige/Espresso', 'Coquina Beige', 'Macchiato Beige/Black MB-Tex', 'Macchiato Beige MB-Tex', 'ARTICO man-made leather macchiato beige / black', 'Lt Frost Beige/Mountain', 'Oxford Stone With Garnet Accents', 'Cashmere w/Cocoa Accents', 'Dark Frost Beige/Light Frost Beige', 'Macchiato Beige/Brown', 'Dune Beige', 'designo Silk Beige/Grey', 'Ceylon Beige', 'Beige leather cloth', 'Cornsilk Beige w/Brown Piping', 'Natural Beige/Black', 'Beige Cloth', 'Cornsilk Beige with Brown Piping', 'Beige cloth leather', 'Beige Velour', 'Sand Beige Leather', 'Light pebble beige Cloth', 'Light Beige - Leather', 'Light Oyster / Ebony', 'Lt Frost Beige/Brown', 'Sahara Beige Leather', 'Dark Beige/Black', 'Havanna/Sand Beige Leather', 'Soft Beige Leather', 'Parchment Beige', 'Choccachino/Ebony Accents', 'Luxor Beige/Saddle Brown Leath', 'Luxor Beige Leather', 'Frost Beige', 'Urban Ground/Beige', 'Canberra Beige/Black w/Contrast Stitching', 'Tan/Ebony/Tan/Ebony', 'Mountain Brown/Light Frost Beige', 'Zagora Beige', 'BEIGE LEATHER', 'Natural Beige / Black', 'Canyon Brown / Light Frost Beige', 'Macchiato Beige w/Diamond Stitching', 'Harvest Beige S', 'Leather Interior in Black/Luxor Beige', 'Leather Interior in Dark Night Blue/Limestone Beige', 'Standard Interior in Black/Luxor Beige', 'Two-Tone Exclusive Manufaktur Leather Interior in Graphite Blue and Choice of Color: Cream', 'Silk Beige / Espresso Brown', 'Latte/Ebony Stitch', 'Pimento/Eb/Pimento/Ebony', 'Whisper Beige with Gideon accent', 'Macchiato Beige / Space Grey Leather', 'Artico Almond Beige', 'Silk Beige/Expresso Brown Leather', 'Macchiato Beige Leather', 'Choccachino w/Cocoa Accents', 'Parchment Beige/Steel Gray Stitching', 'Beige Leatherette', 'Camel Beige', 'Pimento/Ebony/Ebony/Pimento/Cirrus', 'Mesa/Ebony', 'Sandstone Beige', 'Vanilla Beige', 'Light Frost Beige / Canyon Brown', 'Beige Connolly', 'Light Frost Beige', 'Rosewood/Ebony', 'Natural Beige', 'Dark Brown/Beige', 'Ivory/Ebony Stitch', 'Macchiato Beige / Black MB-Tex', 'Macchiato Beige / Black Leather', 'Khaki/Ebony', 'Macchiato Beige/Bronze Brown Pearl', 'Cashmere Beige', 'Beige/Brown', 'Polar Beige', 'Midrand Beige', 'Creme Beige', 'Shale with Brownston', 'Sahara Beige With Jet Black Accents', '2 Tone Beige AND Gray', 'Almond Beige/Black', 'Medium Pebble Beige / Cream', 'Dark Pebble Beige', 'Tuscan Beige'],
                        'Black' => ['Dark Galvanized/Sky Cool Gray','Charcoal Nappa','Jet Black','Charcoal','Black','Dark Slate Gray','Blond With Black','Noir with Santorini Blue accents','Obsidian Rush','Macchiato/Black','Black/Alloy','AMG Sienna Brown/Black','Espresso Brown/Black','Jet Black with Jet Black accents','Jet Black with Red Accents','Amg Black Nappa','White w/Black','Ebony','Ebony Black','Jet Black w/Red Accents','Jet Black/Victory Red','Lunar Shadow (Jet Black/Taupe)','Black/Space Gray','Oyster Black','AMG Black','Light Platinum / Jet Black','Titan Black w/Red Stitching','Black w/Orange Stitching','Jet Black/Medium Dark Pewter','Black w/Linear Espresso Wood Trim','Black Pearl','Slate Black','Jet Black w/Kalahari Accents','Jet Black/Gray w/Blue Accents','Black/Black','Titan Black','Nougat Brown/Black','Global Black','Charcoal Black','Midnight Edition','Black/Rock Gray Stitching','Medium Dark Slate','Titan Black w/Blue','Jet Black/Dark Anderson Silver Metallic','Sea Salt/Black','Black/Alloy/Black','Black Onyx','Ebony/Dark Titanium','Oyster/Black','Black/White','Carbon Black','Black w/Brown','Blk Cloudtex &Clth','Black w/Rock Gray','Blk Lthette &Clth','Jet Black/Red Accents','Red/Black','Jet Black/Chai','Alloy/Black','Charcoal Black/Ebony','Black/Ivory','Black/Graphite','Ebony Bucket Seats','Black Sport Cloth40/Con/4','Nero (Black)','Black Leather','Black Dakota leather',"Black/Scarlet w/Shimamoku", "Ebony/Light Oyster Stitch", "Black w/Blue Stitch", "Black/Cloud Gray Fine Nappa premium leather", "Design Black Leather Interior", "Black w/Blue Stitching", "Black Cloth Interior", "Black Leather Interior", "Ebony w/Lunar Grey Stich", "Oyster/Ebony/Oyster", "Jet Black w/Jet Black Accents", "Black/Red", "Sakhir Orange/Black", "Sky Gray Jet Black With Santorini Blue Accents", "Sedona Sauvage With Jet Black Accents", "Dark Gray W/Black Onyx", "Black w/Hadori Aluminum", "Standard Interior in Black", "Leather Interior in Black", "Leather Package in Black", "Standard Interior in Black/ Mojave Beige", "Leather/Race-Tex Interior in Black with Red Stitching", "Leather Interior in Black/Bordeaux Red", "Leather Interior in Black/Pebble Grey", "Standard Interior in Black/Mojave Beige", "Leather Interior in Black/Alcantara® with Stitching in Platinum Grey", "Leather Package in Black with Deviated Stitching in Gentian Blue", "Two-Tone Exclusive Manufaktur Leather Interior in Black and Choice of Color: Cognac Brown", "Leather Interior in Black with Chalk Stitching with Checkered Sport-Tex Seat Centers", "Leather Package in Black/Bordeaux Red", "Black / Ceramic", "Ebony/Ebony/Ebony", "Black/Brown", "Ebony/Ebony/Ebony/Ivory", "Black Cloth", "Black w/Suede-Effect Fabric Seating", "Ebony/Ebony", "Ebony/Ebony/Ebony/Ebony", "Deep Garnet/Ebony", "Black/Bordeaux Red", "Black/Mojave Beige", "EBONY BLK UNIQUE CLOTH SEATS", "Caraway/Ebony/Caraway", "Gradient Black", "Black/Gray", "Ebony With Ebony Interior Accents", "Black/Tartufo", "Jet Black/Gray w/Red Accents", "Black / Graphite", "Ruby Red/Black", "Java/Black", "Black w/Red Stitching", "Black/New Saddle", "Jet Black/Kalahari", "Noir w/Santorini Blue Accents", "Jet Black/Mocha", "Ebony/Ebony/Ivory/Ivory", "Onyx Black - Semi Aniline", "Ebony W Windsor Seats", "Eclp/Ebony/Eclip/Ebony/Eb", "Black w/Leather Seating Surfaces w", "EBONY ACTIVEX MATRL SEATS", "Black w/Rock Gray Stitch", "BLACK SPORT CLOTH40/CON/40", "Black w/Black Top", "EBNY PART VNYL/CLTH&RED STITCH", "Ebony w/Light Oyster Inserts", "Black w/Nappa Leather Seating Surf", "EBONY BLK PERF LTH-TRM SEAT", "Black w/Leather Trimme", "Jet Black/Ceramic White Accents", "Cloth Bucket Seats or Black Sued", "Black w/Rock Gray Contrast Stitching", "Dark Galvanized/Ebony Accents", "Jet Black/Gray", "Demonic Red/Black", "Jet Black/Artemis", "Black w/Stitching", "Slate Black Leather", "Titan Black/Scalepaper Plaid", "Black w/MB-Tex Upholstery", "Titan Black w/Red Piping", "EBONY LTHR SEAT SURFACES", "designo Black", "EBONY ACTIVEX POWER SEATS", "BLACK STX CLOTH 40/CON/40", "F Sport Black", "EBONY ACTIVEX SEAT MATERIAL", "Black W/ Gray", "Black W/ Brown", "LINCOLN SOFT TOUCH EBONY", "CHARCOAL BLACK CLOTH SEATS", "EBONY PREMIUM CLOTH SEATS", "EBONY LUXURY LEATHER TRIM", "Black/Ash 2-Tone", "Jet Black/Light Titanium", "Jet Black/Nightshift Blue", "Galaxy Black", "EBONY LEATHER TRIM SEATS", "Wheat/Black", "Black/Light Graystone", "Black/Blue", "EBONY BLACK UNIQUE CLOTH", "Black w/Oyster Stitching", "Black/Sable Brown", "Ebony/Ebony/Ebony/Cirrus", "Piano Black", "Carbon Black Checkered", "Black w/Contrast Stitching", "Titan Black/Quartz", "Ebon/Ebony", "Black/Silverstone", "Ebony/Lunar", "Black / Red", "Black Nappa Leather", "Black Graphite", "LTHR-TRIM/VINYL BLACK SEATS", "Titan Black w/Blue Accents", "Black/Space Grey", "Ebony with Dark Plum interior accents", "Black w/Striated Black Trim", "Black/Excl. Stitch", "Ebony/Ebony/Mars Red", "Black / Magma Red", "Black w/Black Open Pore", "Satin Black", "Black Dakota w/Dark Oyster Highlight leather", "Titan Black w/Clark Plaid", "Obsidian Black w/Red", "Black/Sevilla Red", "EBONY PREM LEATHER TRIMMED", "EBONY CLOTH BUCKET SEATS", "BLACK SPORT CLOTH40/20/40", "EBONY LEATHER-TRIMMED SEATS", "2-Tone Black/Ceramique", "Black w/Oyster Contrast Stitching", "EBONY ACTIVEX TRIM SEATS", "EBONY ACTIVEX SEAT MTRL", "BLACK LTHR TRIM BUCKET SEAT", "BLACK SPORT 40/CONSOLE/40", "EBONY UNIQUE CLOTH SEATS", "designo Black/Black", "Obsidian Black", "Ebony Cloth Interior", "BLACK INT W/CARMELO LEATHER", "Black Anthracite", "Black Leatherette", "VINYL GRAY/BLACK SEATS", "EBONY/LT SLATE ACTIVEX SEAT", "Dark Titanium/Jet Black", "Off Black", "Black/Gun Metal", "Black/Red Leather", "Ebony/Ebony Accents", "Cloud/Ebony", "Black W/Grey Accents", "BLACK ACTIVEX/COPPER STITCH", "EBONY LEATHER", "EBONY LEATHER SEATS", "Ebony Trimmed Seats", "Ebony/Cirrus premium leather", "Black Dakota w/Contrast Stitching/Piping leather", "EBONY CLOTH SEATS", "Dark Charcoal w/Orange Accents", "Jet Black with Kalahari accents", "Black Kansas Leather", "Jet Black/Dark Ash", "Jet Black/Medium Titanium", "Jet Black/Titanium", "Black / Crescendo Red", "Charcoal Black/Cashmere Leather Interior", "Black / Ivory", "Black/Black/Black", "Titan Black / Quarzit", "Ebony premium leather", "EBONY ACTIVEX SEAT MATRL", "Black/Alcantara Inserts", "Jet Black/Jet Black", "VINYL BLACK SEATS", "EBONY LEATHER-TRIM SEATS", "Vintage Tan/Ebony/Ebony/Vintage Tan/Ebony", "Off-Black", "Black/Chestnut Brown Contrast Stitching", "Ebony / Ebony", "Chalk/Titan Black", "designo Black/Black Exclusive Nappa premium leather", "Black Mountain", "EBONY ROAST LEA-TRIM", "EBONY LEATHER SEAT SURFACES", "Ebony/Silver", "Ebony Black w/Red Accent Stitching", "Ebony Oxford Leather", "MINI Yours Carbon Black Lounge leather", "Jet Black/Jet Black Accents", "Black w/Red Accent Stitching cloth", "PREMIUM LEATHER EBONY", "Black w/Red", "Black/Dark Sienna Brown", "Black/Lizard Green", "Lounge Carbon Black leather", "Ebony Suede Leather", "Titan Black Leatherette Interior", "BLACK ONYX CLOTH SEATS", "Light Oyster/Light Oyster/Ebony", "Black Leather Seats", "Charcoal Black Leather Interior", "Black/Bordeaux", "Black / Gray", "Individual Platinum Black Full Merino Leather", "Morello Red w/Jet Black Accents", "BLACK LTHR TRIMMED BUCKET", "EBONY BLK UNIQUE CLOTH SEAT", "EBONY BLACK LTHR-TRIM SEATS", "Black SensaTec", "Jet Black / Dark Titanium", "Light Titanium/Ebony", "Black/Lava Blue Pearl Leather Interior", "Ebony cloth", "Black/Saddle Leather Interior", "Jet Black/Graystone", "Leather Package in Black/Garnet Red", "Leather Interior in Black with Checkered Sport-Tex Seat Centers", "Almond / Ebony", "Black w/Medium Dark Slate", "Black w/ Silver Crust", "Ebony Seats", "Black w/Blue", "Ebony/Medium Slate", "Blk/Black", "Blk/Grey", "Black/TURCHESE", "Black/Phoenix red"],
                        'Blue' => ['Blue','Blue Stitching Leather', 'Navy/Beige', 'Blue w/StarTex Upholstery', 'Steel Blue', 'Night Blue/Black', 'Ultramarine Blue/Dune', 'Graphite Blue/Chalk', 'Indigo Blue', 'Ultramarine Blue', 'Fjord Blue', 'Night Blue/Dark Oyster', 'Raven Blue/Ebony', 'Admiral Blue/Light Slate', 'Sea Blue', 'Navy Blue', 'Admiral Blue', 'Coastal Blue', 'Blue/White', 'Yas Marina Blue', 'Navy w/Blue Stitching', 'Blue Gray', 'Blue Haze Metallic/Sandstorm', 'Rhapsody Blue', 'Deka Gray/Blue Highlight', 'Estoril Blue', 'Thunderbird Blue', 'Electric Blue', 'Meteor Blue', 'Deep Ocean Blue', 'Blue Bay / Sand', 'Nightshade Blue', 'Light Blue Gray', 'Navy/Gray', 'Gray/Blue', 'Graphite Blue', 'Marine Blue', 'Rhapsody Blue Recaro Seat', 'Yachting Blue', 'Blue / White', 'Medium Dark Flint Blue', 'Midnight Blue', 'Coastal Blue w/Alpine St', 'Raven Blue/Ebony Perforated Ultrafa', 'Dark Blue/Dune', 'Diamond Blue', 'ADMIRAL BLUE LEATHER SEATS', 'Leather Interior in Graphite Blue/Chalk', 'Leather Interior in Dark Night Blue/Chalk', 'Navy Pier W/Orange Stitch', 'ADMIRAL BLUE LT SLATE LEATH', 'Indigo Blue/Brown', 'Vivid Blue', 'Tension/Twilight Blue Dipped', 'Blue Agave', 'Midnight Blue With Grabber Blue Stitch', 'Navy/Harbour Grey', 'Charles Blue', 'Bugatti Light Blue Sport', 'Blue Accent', 'Neva Gray/Biscaya Blue', 'Aurora Blue', 'Light Blue', 'Indigo Blue / Brown', 'Deep Sea Blue/Silk Beige', 'Liberty Blue/Liberty Blue', 'Night Blue', 'Blue-Dark', 'Silver with Blue', 'Blue/Grey', 'Yacht Blue', 'Blue Leather', 'Blue Sterling', 'Deep Blue', 'Slate Blue', 'Tension Blue/Twilight Blue Dipped', 'Graphite Blue/Chalk Leather', 'Imperial Blue', 'Dark Pewter / Electric Blue', 'LAPIZ BLUE METALLIC BLUE', 'Dark Blue', 'Midnight Blue Grabber Blue Stitch', 'Brown/Indigo Blue', 'Neva Grey / Biscaya Blue MB-Tex', 'Blue & White', 'Metropol Blue', 'Aurora Blue / Alcantara', 'Royal Blue/Cream', 'Ocean Blue', 'Spectral Blue', 'Denim Blue', 'Klein Blue with Beluga', 'NAVY W/ORANGE', 'NAVY/ORANGE', 'Atlantic Blue', 'Beyond Blue', 'RHAPSODY BLUE RECARO', 'BLUE & TAN', 'TENSION/TWILIGHT BLUE DIPPED LEATHER', 'TWLIGHT BLUE', 'Tension Blue', 'Liberty Blue / Perlino', 'Saffron/Imperial Blue', 'PREM LTHR-TRMD BEYOND BLUE', 'Blue Haze', 'Aurora Blue/Electron Yellow', 'MANUFAKTUR Signature Yacht Blue', 'BLUE ACCENT / RECARO SEAT', 'Nightshift Blue', 'Dark Blue/Denim w/White Piping', 'Dk. Blue'],
                        'Brown' => ["Brown", "Chestnut", "Saddle Brown", "Cognac", "Alpine Umber", "Nougat Brown", "Maroon Brown", "Tartufo", "Saddle", "Coffee", "Bahia Brown/Black", "Mocha", "Java", "Dark Brown", "Okapi Brown", "Espresso", "Tan", "Santos Brown", "Terracotta", "Caturra Brown", "Atmosphere/Brownstone", "Tan Leather", "Bahia Brown", "Saddle Brown Dakota Leather", "Touring Brown", "Brandy With Very Dark Atmosphere Accents", "Amaro Brown", "Espresso Brown", "Java Brown", "Santos Brown/Steel Gray Stitching", "Aragon Brown", "Brown/Beige", "Nut Brown/Black", "Kona Brown/Jet Black", "Kona Brown with Jet Black Accents", "Brandy w/Very Dark Atmosphere Accents", "AMG Saddle Brown", "Dark Saddle/Black", "Chestnut Brown", "Kona Brown Sauvage", "Saddle Brown/Black", "Dakota Saddle Brown Leath", "Marrakesh Brown", "designo Saddle Brown/Black", "Tartufo/Black", "Kona Brown / Jet Black", "DESERT BROWN TRIM", "Malt Brown", "Maroon Brown Perforated", "New Saddle/Black", "Nougat Brown / Black", "Beechwood/Off-Black", "Hazel Brown/Off-Black", "Brownstone/Jet Black", "Sienna Brown/Black", "Mauro Brown", "Saddle Brown / Black", "Balao Brown", "Cinnamon Brown Nevada Leather Interior", "Brown/Tan interior", "Chestnut Brown/Black", "Castano Dark Brown", "Saddle Brown Dakota w/Exclusive Stitching leather", "designo Light Brown", "Java Brown w/Tan", "Cinnamon With Jet Black Accents", "Glazed Caramel w/Black", "Dark Saddle / Black", "Moccasin/Black Contrast", "Noble Brown", "Taruma Brown", "Tera Excl Dalbergia Brown", "AMG Sienna Brown", "Portland Brown", "Sienna Brown", "Mountain Brown", "Kona Brown", "Shale / Brownstone", "Maroon Brown W Upholstery", "Cinnamon Brown", "Espresso Brown/Magma Grey", "Giga Brown/Carum Spice Gray", "Saddle Brown/Excl. Stitch", "Canyon Brown/Light Frost Beige", "Truffle Brown", "Maroon Brown/Havana Brown", "Desert Brown", "Ski Gray/Bark Brown", "Copper Brown/Atlas Grey", "Light Frost Brown", "Volcano Brown", "Ebony/Brown", "Tan/Brown", "Urban Brown/Glacier White", "Golden Brown", "White / Brown", "Tera Exclusive Dalbergia Brown", "Dark Brown/Ivory", "Tartufo Brown", "Vintage Brown", "Mud Gray/Terra Brown", "Dark Brown w/Grey Stitching", "Brown/Light Frost", "Canyon Brown/Lt Frost Beige", "Bark Brown/Ski Grey", "Cognac Lthr W/dark Brown", "Sable Brown / Neva Grey Nappa Leather", "Tan / Brown", "Tuscan Brown", "Saddle Brown w/Exclusive Stitching", "Tera Dalbergia Brown", "Mountain Brown/Light Mountain Brown", "Brown Leather", "Norias (Brown)", "Sarder Brown", "Bronze (Brown)", "Marsala Brown/Espresso", "Havana Brown", "Lt Mountain Brown/Brown", "Light Mountain Brown", "Brownstone", "Light Mountain Brown/Mountain Brown", "Cedar Brown", "Golden Oak", "Club Leather Interior in Truffle Brown/Cohiba Brown", "Bison Brown", "Sienna Brown MB-Tex", "Saddle Brown MB-Tex", "Espresso Brown MB-Tex", "Caramel/Ebony Accents", "Chestnut With Ebony Interior Accents", "Chestnut Brown/Ebony Accents", "Cognac/Dark Brown", "Dark Brown / Ivory", "Saddle Brown/Brown/Brown", "Brandy/Ebony Accents", "Shale with Brownstone accents", "Cognac w/Dark Brown Highlight", "Parchment w/Open-Pore Brown Walnut Trim", "Cognac w/Dk Brown Highlight", "Marrakesch Brown", "Brown w/Grey Topstitching", "Saddle Brown/Cream", "Taupe/Brown", "Madras Brown", "Noisette Brown", "Auburn Brown", "SIENNE BROWN", "Mocha Brown Leather", "Chaparral Brown", "Charcoal/Light Brown", "Saddle Brown/Dark Brown", "Espresso Brown/Magma Gray", "Dark Brown/Green", "Camel/Dark Green", "Brown / Beige", "Blk Vern Leath W/ Brown Stitch", "Marsala Brown/Espresso Brown", "Olea Club Leather in Truffle Brown", "Dark Brown w/Gray Stitching", "Giga Brown/Carum Spice", "Saddle Brown Leather", "Hazel Brown/", "Saddle Brown/Luxor Beige", "Brown/Pearl", "Brown/Lt Frost Beige", "Brownstone premium leather", "Bahia Brown Leather", "Saddle Brown/Excl. Stitch Leat", "Bahia Brown w/Grey Topstitching", "Kona Brown Sauvage Leather seats with mini-perfor", "Nut Brown / Espresso", "Brown 2-Tone", "Shale w/Brownstone a", "Audi BROWN", "Brown/Ebony", "Florence Brown", "designo Light Brown/", "Dark Sienna Brown/Bl", "Saddle Brown Br", "Marsala Brown", "STYLE Saddle Brown/B", "Kona Brown with Jet", "Cohiba Brown", "Hazel Brown", "Arabica Brown", "Palomino Brown", "Dk Brown w/Gray Stit", "Club Leather Interior in Truffle Brown", "Brown / Light Frost", "Nougat Brown Leather", "Wheat/Brown", "Brn/Brown", "Indigo/Dark Brown", "Arabica Brown/Almond White", "Sable Brown/Neva Grey", "Canyon Brown", "Brown Nv", "Truffle Brown/Cohiba", "Nutmeg Brown", "Cognac Brown", "Macchiato/Bronze Brown", "Dark Atmosphere/Loft Brown", "Earth Brown/Smoky Green", "Gray/Brown", "Ebony / Brown", "Natural Brown", "Dk. Brown", "Impala Brown", "Palomino w/ Open-Pore Brown Walnut Trim", "Palomino w/Wood Brown Trim", "Canyon Brown / Light Beige", "Pecan Brown", "Amarone Brown", "Brown / Indigo Blue", "Leather Exclusive Brown", "Dk Brown w/Gray Stitching", "Saddle Brown Nappa Leather", "Birch Nuluxe® With Open Pore Brown Walnut Trim (Premium)", "Dark Brown / Light Pebble Beige", "Sable Brown Pearl/Espresso Brown", "Palomino semi-aniline leather and Open-Pore Brown", "Espresso Brown 114", "Mocha w/ Orange Stitching", "Brown Bw", "Caturra Brown Kf2", "Gray w/Brown Bolsters", "Noisette Brown Leather", "Castano Brown", "Maroon Brown - RA30", "Maroon Brown - RC30", "LIGHT BROWN", "Club Leather Interior in Truffle Brown/Cohiba Brow", "Exclusive Manufaktur Interior in Cohiba Brown and", "Criollo Brown", "Brown DINAMICA w/Grey", "Porcelain/Espresso Brown", "Saddle Brown Dakota", "designo Auburn Brown", "Mauro Brown Vienna Leather", "Bison Brown/Mountain Brown", "Ebony/Brown w/Premium Leather", "Vermont Brown", "designo Saddle Brown", "brown/tan", "Leather Brown", "Brown Nappa Leather", "Truffle Brown/Cohiba Brown", "Khkc/Brown", "AMG Saddle Brown MB-Tex", "MANUFAKTUR Mahogany Brown / Macchiato Beige Exclusive Nappa Leather", "Dark Brown and Tan", "Espr Brown Perforated Veganza", "Murillo Brown", "Brown / Saddle Leather", "Portland Brown Full Leather Interior", "Club Leather Interior in Cohiba Brown", "Leather Interior in Saddle Brown/Luxor Beige"],
                        'Gold' => ['Gold','Golden Oak & Black', 'Golden Oak/Black', 'Cream/Gold', 'Golden Oak & Black - CF', 'Agate Grey/Lime Gold'],
                        'Gray' => ["Gray", "Cirrus", "Medium Gray", "Graphite", "Ash", "Dark Gray", "AMG Neva Gray", "Gray w/Yellow Stitching", "Grey", "Neva Grey/Black", "Espresso/Gray", "Wilderness Startex", "Titanium Gray", "Rock Gray", "Storm Gray", "Macchiato/Magmagray", "Diesel Gray/Black", "Ski Gray/Black", "Light Gray", "Titan Blk Clth", "Cement", "Slate Grey", "Steel Gray w/Anthracite", "Rotor Gray w/Anthracite", "Steel Gray w/Anthracite Stitching", "Gray/Black", "Grey/Carbon Black", "Graystone", "Steel Gray", "Cocoa/Dune", "Ash/Black", "Gideon/Very Dark Atmosphere", "Dark Slate 40 20 40", "Dark Charcoal", "Shale", "Dark Gray Leather Interior", "Gray Cloth Interior", "Aviator Gray", "Dark Galvanized Gray", "Sky Cool Gray", "Gray w/Orange Stitching", "Sky Gray With Santorini Blue Accents", "Rotor Gray", "Leather Interior in Agate Grey/Pebble Grey", "Sandstorm Gray w/Nappa Le", "Gray Cloth", "Slate Gray", "Cement Gray", "Gray Flannel", "Dark Ash Gray Sky Gray", "Dark Atmosphere/Medium Ash Gray", "Steel Grey", "Cocoa/ Light Ash Gray", "Medium Ash Gray", "Gray w/Leatherette Seating Surface", "Macchiato/Magma Grey", "Dark Galvanized Sky Gray", "Titanium Grey/Black", "Adelaide Grey", "Medium Earth Gray", "DARK GRAY CLOTH 40CONSOLE40", "Grey Flannel", "Lunar Gray", "Silverstone/Black", "Agate Gray", "Titanium Grey Pearl", "Dark Ash with Jet Black Interior Accents", "Greige/Black", "DARK PALAZZO GRAY VINYL", "DARK PALAZZO GRAY CLOTH", "PRFM GRAY ACTIVEX SEAT MTRL", "Medium Ash Gray/Jet Black", "Gray 2-Tone", "Dark Earth Gray", "Neva Gray/Sable Brown", "Grey/Blue", "Agate Grey", "Neva Grey/Sable Brown", "Ash Gray/Glacier White", "Magma Grey/Black", "Dark Atmosphere/ Medium Ash Gray", "MED GRAY CLOTH 40CONSOLE40", "Rock Gray/Gray", "Rock Gray / Black", "Diesel Gray / Black", "Gray / Black", "Dk Khaki/Lt Graystone", "SPACE GRAY ACTIVEX TRIM SEA", "Stonegray", "Gray Cloudtex & Cloth", "Rotor Gray w/Anthracite Stitching", "Gray w/Blue Bolsters", "Dark Palazzo Gray", "Earth Gray", "DARK EARTH GRAY CLOTH SEATS", "Light Blue Gray/Black", "Gray Dakota Leather", "Dark Walnut/Dark Ash Grey", "Medium Slate Gray", "LTH-TRM/VINYL GRAY/NAVY STS", "Dark Slate Gray/Med Slate Gray", "Light Titanium/Dark Titanium Accents", "Dark Space Gray", "Medium Greystone/Dark Slate", "Dark Gray/Camel", "Gray/Dark Gray Leather Interior", "Fog Gray 2-Tone", "Satellite Grey", "Dark Slate Gray/Medium Slate Gray Cloth Interior", "Birch w/Black Open Pore", "Giga Cassia/Spice Gray", "Metro Gray", "French Roast/Black", "Diesel Grey/Black", "Palazzo Grey", "Medium Titanium/Jet Black", "Ebony seats with Slate interior accents", "Grayblack", "Dark Earth Gray cloth", "Gray MB Tex", "Marble Gray", "Stratus Gray", "Dark Slate Grey/Med Slate Grey", "Standard Interior in Slate Grey", "Light Space Gray", "Gray/Green 3-Tone", "Dark Ash Gray/Sky Cool Gray", "Neva Gray", "Ebony w/Red Accent Stitching", "Alpine Gray", "Satellite Gray", "Space Gray", "Cashmere grey/Phoenix Red", "Creme Light/Black Accent", "Ash/Gray", "Dark/Medium Slate Gray", "Light Blue Gray / Black", "Magma Grey", "Pastel Slate Gray", "Gray / Silver", "Mega Carum Spice Gray", "Dark Walnut/Dark Ash Gray", "Quartz Gray", "Cloud Gray", "Dark Slate/Medium Graystone", "Crystal White/Silver Gray Pearl", "Medium Flint Gray", "Dark Galvanized /Sky Cool Gray", "Dark Space Gray w/ Navy Pier", "Dark Walnut/Very Dark Ash Gray", "Monaco Gray", "DARK PALAZZO GREY VINYL", "Medium Slate Gray Leather Cloth D5", "Gray / Dark Gray", "Slate/Graystone", "BLK W/GRAY INSERTS", "Dark Gray/Vanilla", "Dark Slate Gray Interior", "Dark Ash Gray", "Gray/Metallic", "Jade Gray", "Gray w/Pure White", "Rock Gray / Granite Gray", "Gray / White", "Lt Stone W/Gray Piping", "Gray (yth)", "Dark Gray w/Navy Pier", "Crystal Gray", "Light Titanium W/Ebony Accents", "Misty Gray", "Dark Walnut/Dark Ash Grey Forge Perforated Leather Seating Surf", "Montana Gray", "Gray/Beige", "Charcoal Gray", "Dark Slate Gray/Medium Slate Gray", "Magma Gray", "Agate Grey / Pebble Grey", "GRAY CLOTH 40/20/40", "Gray leather", "Ebony w/Smoke Gray", "MEDIUM GRAY CLOTH 40/20/40", "Medium Earth Gray cloth", "Silverstone II Atlas Grey", "Diesel Gray", "Grey Fabric", "Gray Leatherette", "Charcoal/Misty Gray", "Dark Gray/Onyx", "Standard Interior in Agate Grey", "Leather Interior in Slate Gray/Chalk", "Hai Gray", "Melange/Light Gray", "Two-Tone Gray Cloth", "Cocoa/Light Ash Gray", "Palazzo Gray", "Platinum Grey", "Neva Grey", "Dark Grey", "Leather Interior in Slate Grey", "Phantom Gray", "Mega Carum Spice Grey/Carum Spice Grey", "Agate Grey/Pebble Grey", "Medium Slate Gray/Light Shale", "Gravity Gray", "Grey 40 Console 40", "Moonstone/Grey Flannel", "Natural Gray", "Medium Slate Gray cloth leather", "Light Ash Gray/Ceramic White", "Greystone", "Graphite Grey", "Pebble Grey", "Neva Grey / Sable Brown MB-Tex", "Neva Grey / Biscaya Blue Leather", "Light Ash Gray", "Dark Walnut / Dark Ash Grey", "Lt Gray", "Dove Grey", "Radar Red/Dark Slate Gray", "Graphite Gray", "Stone Gray/Raven", "Dark Gray/Med Gray", "Dk/Lt Slate Gray", "Platinum Gray", "Dark Slate Gray/Saddle Tan", "Dark Palazzo Grey", "Dark Slate Gray/Medium Slate Gray Cloth Bucket", "Medium Ash Gray Premium cloth seat trim", "Flint Gray", "GREY CLOTH 40/20/40", "Dark/Light Slate Gray", "Dk/Med Slate Gray", "Med Slate Gray", "Dark Khaki/Light Graystone", "Gray / Blue", "Gray cloth leather", "Giga Cassia/Carum Spice Grey", "Gray with Orange Stitching", "Light Grey", "PALAZZO GREY CLOTH SEATS", "Gray Partial Leather", "Coral and Gray", "Gray - GR", "Pando Gray", "Dark Slate Gray Cloth", "Md Slate Gray/Lt Shale", "Art Gray", "Storm Gray Leatherette", "GYT/GRAY", "Dark Slate Gray / Medium Slate Gray", "AMG Neva Grey", "Ash / Gray", "Metropolis Gray", "Two-Tone Gray", "Cinder Grey/Ebony", "Crystal Grey", "Scivaro Gray", "Dark Ash Gray / Light Ash Gray", "Dark Graystone/Medium Graystone", "Moonrock Gray", "Everest Gray", "Light Gray Leather", "Cognac w/Granite Gray", "Dark Graystone / Medium Graystone", "Neva Grey/Biscaya Blue"],
                        'Green' => ["Green","Light Argento Metallic/Sage Green","Green / Beige", "Forest Green/Beige", "Dark Green", "Sage Green", "Evergreen","Green Pearlcoat","GREEN / BLACK","Carbon Black/GREEN", "Urban Green","Rialto Green", "Agave Green", "Cactus Green", "Cumbrian Green", "Pine Green", "Nero Ade w/ Green and Orange", "Mori Green", "Sage Green w/Lime Accents" ,"Shadow Green", "Light green" , "Dark Green/Glacier White" , "Cumbrian Green Hide", "Forest Green", "Moss Green", "Dark Green 2-Tone"],
                        'Orange' => ["Orange", "Orange Stitching w/Cloth Upholst", "Orange Stitching Leather", "Kyalami Orange/Black", "Saffrano Orange & Black", "Kyalami Orange", "Inferno Orange", "Sakhir Orange", "Orange/White", "SAKHIR ORANGE/BLK", "Orange Accent", "Burnt Orange", "Orange Zest", "CODE ORANGE", "LT.ORANGE"],
                        'Pink' => ['Pink','Peony Pink', 'Club Pink Plaid/Black'],
                        'Purple' => ['Purple','Dark Auburn With Jet Black Accents', 'Garnet Seats With Ebony Interior Accents', 'Q Deep Purple', 'Purple Silk'],
                        'Red' => ["Red", "Bengal Red", "Tacora Red", "Bengal Red/Black", "Circuit Red", "Classic Red", "Red Amber", "Rioja Red", "Mars Red/Ebony/Mars Red", "Cockpit Red", "Red w/Ultra Suede Perforated SofTe", "Red Leather", "Red Stitch Leather", "Magma Red", "Burgundy Red", "Fiona Red", "Fiona Red/Black", "Flare Red", "Red & Zegna", "AMG Cranberry Red/Black", "Carmine Red/Black", "Exclusive Carmine Red/Black", "Tacora Red w/Contrast Stitching", "Classic Red/Black", "AMG Power Red/Black", "Garnet Red/Black", "Cranberry Red", "Arras Red", "Adrenaline Red", "Cranberry Red/Black", "Red/Black Bicolor", "Red Pepper/Black", "Mars Red/Flame Red Stitch", "Coral Red/Black", "Circuit Red w/Hadori Aluminum", "EBONY ACTIVEX/RED STITCHING", "Ruby Red", "Bordeaux Red", "Coral Red Dakota Leather", "Bordeaux Red/Black", "Red / Black", "Dream Red Leather Interior", "Charcoal w/Lava Red Stit", "Jet / Red", "Dark Ruby Red", "Porcelain/Titian Red", "Salsa Red", "Redwood", "Barcelona Red", "Circuit Red/Dark Graphite", "Charcoal w/Lava Red Stitch", "Red/tan", "Red/Tan/Yellow", "Rioja Red w/Dark Graphite Aluminum", "Crimson Red", "Monaco Red/Graphite", "Bk/Wred", "Red Merino", "Sevilla Red", "Spice Red", "Charcoal w/Lava Red", "designo Mystic Red", "Red / Tan", "Express Red", "Red Pepper", "Mahogany Red", "Ceramic White With Red Stitching", "Sporting Red", "Monaco Red", "Red Leather Interior", "Flamenco Red Metallic", "AMG Power Red", "AMG Cranberry Red", "Mugello Red", "RED NAPPA AND RED CARPET", "Mars Red / Ebony", "Red Oxide", "Jet / Jet / Redzone", "Ebony w/Red Stitch", "Red Recaro Nismo Leather", "Manufaktur Classic Red", "Circuit Red w/Scored Aluminum Trim", "Coral Red Boston Leather", "Showstop Red Lthr Trim Pw", "Garnet Red", "Ebony w/Red Stitching", "ADRENALINE RED FRONT LEATHER SEATING SURFACES", "Adrenaline Red Dipped", "Hotspur Red", "TACORA RED SENSATEC", "Charcoal / Red", "Charcoal w/Lava Red St", "BELUGA/RED", "Bordeaux Red w/ Chalk Stitching", "Commissioned Collection Phoenix Red", "Ebony/Red/Red Stitch", "MSO Bespoke Red", "Morello Red", "CARMINE RED LEATHER-TRIMMED", "Torch Red", "Custom White / Red Carbon Leathe", "Charcoal/Red Nappa Leather", "EBNY PART VNYL/CLTH&RED STI", "EBONY LTHR TRIM/RED STITCH", "Phoenix Red/arctic white", "Macchiato/Lounge Red", "Fox Red Novillo", "Berry Red", "Graphite with Red Stitch", "Consort Red", "Vermilion Red", "Red Accent", "Pimento Red/Ebony Inserts & Stitch", "Commissioned Collection Mugello Red", "Full Red", "Venom Red", "Circuit Red w/ Hadori Aluminum Trim", "Imola Red", "Phoenix Red", "Ebony With Red Stitching", "Ardent Red", "Red Leather Seating Surfaces", "White/Red", "Coral Red", "Red Rock", "Rioja Red w/Silver Performance Trim", "Red with Jet Stitching and Red Interior", "Chancellor Red", "Ebony with Mars Red Stitching", "Ebony/Mars Red", "Ebony/Pimento Red with Pimento Red Stitching", "Showstopper Red", "Brick Red/Cashmere Accents", "Signal Red", "Show Stopper Red", "Circuit Red w/Hadori Aluminum Trim", "Spicy Red", "Ebony/Eternal Red", "Carmine Red", "F-Sport Rioja Red", "Rioja Red w/Dark Graphite", "MAHGNY RED LTH SEAT SURFACE", "Circuit Red W/Hadori Alum", "Dark Knight/Spicy Red", "Flame Red Clearcoat", "Circut Red", "All Eclipse Red", "Mars Red/Ebony/Mars Red Leathe", "Cockpit Red leather", "Mars Red/Mars Red/Ebony", "Carrera Red", "Marsala Red", "Ebony/Red Stitch", "Checkered", "Red w/Amman Stitching", "Carbon and Martian Rock Red", "Pillar Box Red", "RED/BL", "Ebony w/Mars Red Stitch", "Pimento Red / Ebony / Ebony", "Anthracite/Red Leather", "Adrenaline Red/Jet B", "Red Line", "Circuit Red W/Satin Chrom", "Chill Red", "Fox Red", "Bengel Red", "Tacora Red Perforated SensaTec", "Burgundy Red perforated and quilted Veganza", "Chancellor Red/Ivory Leather", "Dk Charcoal/Red", "Two-Tone Exclusive Manufaktur Leather Interior in Bordeaux Red and Choice of Color: Cream", "Leather Interior in Bordeaux Red", "Redzone/Redzone Stitch", "Tacora Red w/Contrast Stitching/Piping", "Arras Red Design Selection", "Express Red Fine Nappa Leather", "NH-883PX/RED", "Vermillion Red", "Red/Red", "Circuit Red/F Aluminum", "Morello Red Dipped", "TORRED CLEARCOAT [RED]", "Ebony DuoLeather seats with Mars Red stitch", "Sevilla Red Two-Tone", "Red Nv", "F Sport Rioja Red", "Lords Red", "Claret Red", "Oxide Red", "Brick Red", "Red / Cream", "Flare Red With", "Circuit Red Nul", "Rioja Red Nulux", "Circut Red w/Satin Chrome Trim", "Ebony / Mars Red", "Ferrari Red", "Red Pepper Manufaktur Sin", "Charcoal & Red", "VELVET RED PEARLCOAT", "Ebony / Pimento Red / Ebony", "Circuit Red NuLuxe and Hadori Aluminum", "Amber Red", "Brick Red / Cashmere", "Circuit Red w/Naguri Aluminum Trim", "Merlot Red", "Red/White", "Red Mica", "BLK/RED", "Ceramic White w/Red Stitching", "RedL", "Sevilla Red 2-Tone", "KING RANCH RED BUCKET SEATS", "DESIGNO BENGEL RED", "Magma Red w/Anthracite Stitching", "Burgundy Red Perforated Veganz", "Pimento Red", "Cranberry Red Leather", "Cayenne Red", "EBONY W/ RED STITCH", "EBONY/ RED STITCHING", "EBONY W/RED STITCH ACCENTS", "Brick red & cashmere", "Deep Red", "Color to Sample Red & White", "REDWOOD VENETIAN LEATHER", "BENGAL RED NAPPA", "Red - RE", "Mars Red/Ebony/Mars Red - 301YG", "ADRENALIN RED", "Redwood Leather", "Sport Red", "Red Kj3", "SHOWSTOP RED LTHR-TRIM PWR", "Red Td1", "Red (td1)", "Adrenalin Red - 704", "Demonic Red Laguna Leather", "Red (d3l)", "Exclusive Carmine Red", "Rioja Red Eb33", "SHOWSTOPPER RED RECARO LTHR", "Rioja Red - EA21", "Lounge Championshipred", "Red Copper", "Charcoal with Lava Red Stitch with Front & Rear Leather Seat Trim (1st / 2nd Rows)", "Pure Red", "Mars Red with Flame Red Stitching", "Adrenaline Red Napa leather seating surfaces with", "Divine Red", "GTS Interior Package in Carmine Red", "MANUFAKTUR Signature Carmine Red", "AMG Classic Red / Black Exclusive Nappa Leather", "Boxster Red", "Onyx/Red", "Arras Red Valcona", "Dark Red", "Flare Red w/Ginsumi", "CHARCOAL VENTIRED NAPPA L", "Ebony/Flame Red Stitch", "Red & Zegna w/Amman", "designo Bengal Red", "White & Red", "Infrared"],
                        'Silver' => ['Silver','Steel','Platinum','Silverstone','Light Platinum/Jet Black','Light Platinum w/Jet Black Accents','Silver w/Silver Trim','SILVER','Lunar Silver','Silverstone/Coffee','Dark Pewter w/Silver Trim','Silverstone/Vintage Coffee','Silver w/Blue Trim','IGNOT SILVER','Dark Pewter / Silver','Symphony Silver','Pyrite Silver Metallic','Manufaktur Cry Wte/Silver','LIQUID SILVER METALLIC S','Ingot Silver Metallic','Silver Pearl','Silverstone Sensafin','Sil/silver','Rhodium Silver','BLADE SILVER METALLIC','Silver / Blue','Silver with Silver Trim','Silver Bison','Shimmering Silver Pearl','Pastel Silver'],
                        'White' => ['White', 'Blond','Ivory','Warm Ivory','Parchment','Medium Stone','Ivory/Ebony/Ivory/Ebony','Light Oyster','White/Black','Cashmere','Ivory Lth','Ceramic White','Almond','Ivory White','Gallery White','Smoke White','Designo Diamond White Metallic Vinyl','Tafeta White','Ivory White/Night Blue','Ivory White/Black','Off White','Deep White/Black','Opal White','Melange/Lt Gray','Glacier White','WHITE/BLK','Arctic White','Bespoke White','designo Platinum White','White Sands/Espresso','White Leather','Parchment White','Summit White','White/Ivory','White/Brown','Platinum White Pearl','Graphite/White Stitching','Ultra White','Giga Ivory White','Vianca White','Macchiato/Magmagrey','Grace White','GRACE WHITE / BLACK / PEONY PINK','Turchese/ARCTIC WHITE','TAILORED PURPLE/GRACE WHITE/BLACK','GRACE WHITE/COBALTO BLUE','Ivory White/Dark Oyster','Grace White/peony pink','Crystal White','AMG Neva Grey MB-Tex','White Sands','FUJI WHITE','F SPORT White','White Ivory Leather','Sea Salt/White','Platinum White','White / Tan','Grace White/Havana','White/Peppercorn','Pure White','Bright White Clearcoat','Ivory White/Atlas Grey','White Premium leath','Neva White/Magma Grey','Neva White/Magma Grey MB-Tex','White Frost Tricoat','Glacier White w/Copper','Opal White/Amaro Brown','Oxford White','Ivory White Nappa','Whi/White','designo Platinum White Pearl / Blac','Q Glacier White','Champagne/Blue','White w/Satin Chrome Trim','Ivory White Nappa Leather','Eminent White Pearl','Super White','Polar White','Ivory White w/Dark Oyster Highlight','White or Light Beige','Ivory White Ext Merino Leather','Veganza Perforated Qlt Smoke White','VCFU Smoke White Extended Merino L','Mandarin with Grace White','Grace White with Pine Green','White/white','MACCHIATO/ MAGMAGREY','Smoke White/Night Blue','White and blue','Macchiato/Magmagrey - 115','White D1s','offwhite leather','Ceramic White Pearl','White by Mulliner','Ivory White w/ Contrast Stitching','Nero-White Stitching','Graphite/White cloth','Ivory White Extended Merino Leather','Ivory White/Night Blue Extended Merino Leather','Cream (Off-White)','Steam (White)','Glacier White - GLW','Commissioned Collection Arctic White','Pearl White','White Diamond Pearl','Ivory White/Atlas Gray'],
                        'Yellow' => ['Yellow', 'Amber', 'Yellow Stitching w/StarTex Uphols', 'Yellow w/Yellow Trim', 'Forge Yellow'],
                        'Other' => ['Other', '–', '----', 'Unspecified']
                    ];

                    $selectedInteriorColorValues = $request->autoWebInteriorColorCheckbox;
                    $mappedwebInteriorColorValues = [];

                    foreach ($selectedInteriorColorValues as $value) {
                        if (isset($exteriorColorMapping[$value])) {
                            $mappedwebInteriorColorValues = array_merge($mappedwebInteriorColorValues, $exteriorColorMapping[$value]);
                        }
                    }

                    $mappedwebInteriorColorValues = array_unique($mappedwebInteriorColorValues);

                    if ($request->has('allWebInteriorColorName') && $request->allWebInteriorColorName == 'allWebInteriorColorValue') {
                        // Logic for 'all' selection
                    } else if ($request->has('autoWebInteriorColorCheckbox')) {
                        $query->whereIn('interior_color', $mappedwebInteriorColorValues);
                    }
                }

                // if ($request->autoWebExteriorColorCheckbox != null) {
                //     $colors = array_map(function($color) {
                //         return '%' . $color . '%';
                //     }, $request->autoWebExteriorColorCheckbox);

                //     $query->where(function ($query) use ($colors) {
                //         $query->whereRaw(implode(' OR ', array_fill(0, count($colors), 'exterior_color LIKE ?')), $colors);
                //     });
                // }

                // if ($request->autoWebInteriorColorCheckbox != null) {
                //     $colors = array_map(function($color) {
                //         return '%' . $color . '%';
                //     }, $request->autoWebInteriorColorCheckbox);

                //     $query->where(function ($query) use ($colors) {
                //         $query->whereRaw(implode(' OR ', array_fill(0, count($colors), 'interior_color LIKE ?')), $colors);
                //     });
                // }


                // if ($request->autoWebExteriorColorCheckbox != null) {
                //     $query->where(function ($query) use ($request) {
                //         foreach ($request->autoWebExteriorColorCheckbox as $color) {
                //             $query->orWhere('exterior_color', 'like', '%' . $color . '%');
                //         }
                //     });
                // }

                // if ($request->autoWebInteriorColorCheckbox != null) {
                //     $query->where(function ($query) use ($request) {
                //         foreach ($request->autoWebInteriorColorCheckbox as $color) {
                //             $query->orWhere('interior_color', 'like', '%' . $color . '%');
                //         }
                //     });
                // }




            // if ($request->autoWebDriveTrainCheckbox != null) {
            //     dd($request->autoWebDriveTrainCheckbox);
            //     if ($request->has('allWebDriveTrainlName') && $request->allWebFuellName == 'allWebDriveTrainValue') {
            //     } else if ($request->has('autoWebDriveTrainCheckbox')) {
            //         $query->whereIn('drive_info', $request->autoWebDriveTrainCheckbox);
            //     }
            // }

            if ($request->autoWebDriveTrainCheckbox != null) {
                // Mapping array
                $driveTypeMapping = [
                    '4WD' => ['Four-wheel Drive', '4WD'],
                    'AWD' => ['All-wheel Drive', 'AWD'],
                    'FWD' => ['Front-wheel Drive', 'FWD'],
                    'RWD' => ['Rear-wheel Drive', 'RWD'],
                    'Other' => ['Unknown', '–']
                ];

                $selectedValues = $request->autoWebDriveTrainCheckbox;
                $mappedValues = [];

                // Map selected checkboxes to database values
                foreach ($selectedValues as $value) {
                    if (isset($driveTypeMapping[$value])) {
                        $mappedValues = array_merge($mappedValues, $driveTypeMapping[$value]);
                    }
                }

                // Remove duplicate values
                $mappedValues = array_unique($mappedValues);

                // Debugging the mapped values
                // dd($mappedValues);

                if ($request->has('allWebDriveTrainlName') && $request->allWebFuellName == 'allWebDriveTrainValue') {
                    // Your existing logic for this condition
                } else if ($request->has('autoWebDriveTrainCheckbox')) {
                    // Apply the mapped values to your query
                    $query->whereIn('drive_info', $mappedValues);
                }
            }


            // if ($request->autoMobileDriveTrainCheckbox != null) {
            //     if ($request->has('allMobileDriveTrainlName') && $request->allWebFuellName == 'allMobileDriveTrainValue') {
            //     } else if ($request->has('autoMobileDriveTrainCheckbox')) {
            //         $query->whereIn('drive_info', $request->autoMobileDriveTrainCheckbox);
            //     }
            // }


            if ($request->autoMobileDriveTrainCheckbox != null) {
                // dd($request->autoMobileDriveTrainCheckbox);
                // Mapping array
                $mobileDriveTypeMapping = [
                    '4WD' => ['Four-wheel Drive', '4WD'],
                    'AWD' => ['All-wheel Drive', 'AWD'],
                    'FWD' => ['Front-wheel Drive', 'FWD'],
                    'RWD' => ['Rear-wheel Drive', 'RWD'],
                    'Other' => ['Unknown', '–']
                ];

                $mobileSelectedValues = $request->autoMobileDriveTrainCheckbox;
                $mobileMappedValues = [];

                // Map selected checkboxes to database values
                foreach ($mobileSelectedValues as $value) {
                    if (isset($mobileDriveTypeMapping[$value])) {
                        $mobileMappedValues = array_merge($mobileMappedValues, $mobileDriveTypeMapping[$value]);
                    }
                }

                // Remove duplicate values
                $mobileMappedValues = array_unique($mobileMappedValues);

                if ($request->has('allWebDriveTrainlName') && $request->allWebDriveTrainlName == 'allWebDriveTrainValue') {
                    // Your existing logic for this condition
                } else if ($request->has('autoMobileDriveTrainCheckbox')) {
                    // Apply the mapped values to your query
                    $query->whereIn('drive_info', $mobileMappedValues);
                }

            }


            if ($request->mobileBody != null) {
                $mobile_body = $request->mobileBody;
                $query->Where('body_formated', 'LIKE', '%' . $mobile_body . '%');
            }


            if ($request->webBodyFilter != null) {
                $web_body = $request->webBodyFilter;
                $query->Where('body_formated', 'LIKE', '%' . $web_body . '%');
            }


            if ($request->webColorFilter != null) {
                $web_body = $request->webColorFilter;

                $query->where(function ($subQuery) use ($web_body) {
                    foreach ($web_body as $body) {
                        $subQuery->orWhere('exterior_color', 'LIKE', '%' . $body . '%');
                    }
                });
            }

            if ($request->autoMaxBodyCheckbox != null) {
                if ($request->autoMaxBodyCheckbox[0] == null) {
                    $query->whereNull('body_formated')->orWhereIn('body_formated', $request->autoMaxBodyCheckbox);
                } else {

                    $query->whereIn('body_formated', $request->autoMaxBodyCheckbox);
                }
            }
            if ($request->autoMobileMakeCheckbox != null) {
                $query->whereIn('make', $request->autoMobileMakeCheckbox);
            }
            if ($request->secondFilterMakeInputNew != null) {
                $query->where('make', $request->secondFilterMakeInputNew);
            }
            if ($request->secondFilterModelInputNew != null) {
                $query->where('model', $request->secondFilterModelInputNew);
            }
            if ($request->autoMobileMaxBodyCheckbox != null) {
                if ($request->autoMobileMaxBodyCheckbox[0] == null) {
                    $query->whereNull('body_formated')->orWhereIn('body_formated', $request->autoMobileMaxBodyCheckbox);
                } else {

                    $query->whereIn('body_formated', $request->autoMobileMaxBodyCheckbox);
                }
            }

            if ($homeBodySearch != null) {

                if ($homeBodySearch == 'SUV') {
                    $query->where('body_formated', 'suv / crossover');
                } elseif ($homeBodySearch == 'Electric') {
                    $query->where('fuel', 'Electric');
                } elseif ($homeBodySearch == 'Hybrid') {
                    $query->where('fuel', 'Hybrid');
                } else {
                    $query->where('body_formated', $homeBodySearch);
                }
            }


            // mobile filter start here
            if ($request->secondFilterZipInput != null) {
                $query->where('zip_code', $request->secondFilterZipInput);
            }
            if ($request->secondFilterMakeInput != null) {
                $query->where('make', $request->secondFilterMakeInput);
            }
            if ($request->secondFilterModelInput != null) {
                $query->where('model', $request->secondFilterModelInput);
            }
            if ($request->webMakeFilterMakeInput != null) {
                Cookie::queue(Cookie::forget('searchData'));
                $searchData = ['webMakeFilterMakeInput' => $request->webMakeFilterMakeInput];
                Cookie::queue('searchData', json_encode($searchData), 120);
                $query->where('make', $request->webMakeFilterMakeInput);
            }
            if ($request->webModelFilterInput != null) {
                $query->where('model', $request->webModelFilterInput);
            }
            if ($request->totalLoanAmountCalculation != null) {
                $format_price  = intVal(str_replace(',', '', $request->totalLoanAmountCalculation));
                $query->whereBetween('payment_price', [0, $format_price]);
            }

            if ($request->mobileColorFilter != null) {
                $mobile_color = $request->mobileColorFilter;
                $query->where(function ($subQuery) use ($mobile_color) {
                    foreach ($mobile_color as $mobile_color) {
                        $subQuery->orWhere('exterior_color', 'LIKE', '%' . $mobile_color . '%');
                    }
                });
            }


            // mobile filter end here
        } else {
            if ($lowestValue != null) {
                $query->orderBy('price');
            }
            if ($lowestMileageValue != null) {
                $query->orderBy('miles');
            }
        }


        // Apply filters based on the request
        if ($request->has('make_data') && !empty($request->make_data)) {
            $query->where('make', $request->make_data);
        }

        if ($request->has('dealer_name') && !empty($request->dealer_name)) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('name', $request->dealer_name);
            });
        }

        if ($request->has('dealer_city') && !empty($request->dealer_city)) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('city', $request->dealer_city);
            });
        }

        if ($request->has('dealer_state') && !empty($request->dealer_state)) {
            $query->whereHas('dealer', function ($query) use ($request) {
                $query->where('state', $request->dealer_state);
            });
        }

        if ($request->has('inventory_date') && !empty($request->inventory_date)) {
            $query->whereDate('created_at', $request->inventory_date);
        }


        if ($id != null) {
            $query->where('deal_id', $id)->where(['status' => '1', 'is_visibility' => '1']);
        } else {
            $query->where(['status' => '1', 'is_visibility' => '1']);
        }

        return $query;
    }



    public function getItemByUser($id)
    {
        // $inventory = Inventory::where('deal_id',$id)->get();
        $inventory = MainInventory::where('deal_id', $id)->get();
        return $inventory;
    }


    function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2, $earthRadius = 3959)
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
