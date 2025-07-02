<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interface\InventoryServiceInterface;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\MainInventory;
use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AdminInventoryController extends Controller
{

    public function __construct(private InventoryServiceInterface $inventoryService) {}


    public function index(Request $request)
    {

        // $data = $this->inventoryService->all();
        $authUser = Auth::user();

        // $inventory = Inventory::query();
        $inventory = MainInventory::query();

        if($authUser->hasAllaccess())
        {
            $data['inventory_make'] = $inventory->distinct('make')->pluck('id','make')->toArray();

            $users = User::whereHas('roles', function($query) {
                $query->where('name', 'dealer');
            })
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->get(['id', 'name', 'city', 'state']);
            $data['inventory_dealer_name'] = $users->pluck('id', 'name')->toArray();
            $data['inventory_dealer_city'] = $users->pluck('id', 'city')->toArray();
            $data['inventory_dealer_state'] = $users->pluck('id', 'state')->toArray();
        }else
        {
            $data['inventory_make'] = $inventory->where('deal_id',$authUser->id)->distinct('make')->pluck('id','make')->toArray();
            $data['inventory_dealer_name'] = User::where('id',$authUser->id)->pluck('id','name')->toArray();
            $data['inventory_dealer_city'] = User::where('id',$authUser->id)->whereNotNull('city')
            ->where('city', '!=', '')->pluck('id','city')->toArray();
            $data['inventory_dealer_state'] = User::where('id',$authUser->id)->whereNotNull('state')
            ->where('state', '!=', '')->pluck('id','state')->toArray();
        }

        ksort($data['inventory_make']);
        ksort($data['inventory_dealer_name']);
        ksort($data['inventory_dealer_city']);
        ksort($data['inventory_dealer_state']);


        $info = [];
        $rowCount = 0;
        $trashedCount = 0;

        if ($request->showTrashed == 'true') {
            if ($authUser->hasAllaccess()) {
                // Fetch trashed items, not just the count
                $info = $this->inventoryService->getItemByFilterWithOptimized($request);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount();
                $rowCount = $this->inventoryService->getRowCountOptimized($request);
            } else {
                // Fetch trashed items for a specific dealer
                $info = $this->inventoryService->getItemByFilterWithOptimized($request,$authUser->id);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount($authUser->id);
                $rowCount = $this->inventoryService->getRowCountOptimized($request, $authUser->id);
            }
        } else {
            if ($authUser->hasAllaccess()) {
                // Fetch active inventory
                $info = $this->inventoryService->getItemByFilterWithOptimized($request);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount();
                $rowCount = $this->inventoryService->getRowCountOptimized($request);
            } else {
                // Fetch active inventory for specific dealer
                $info = $this->inventoryService->getItemByFilterWithOptimized($request, $authUser->id);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount($authUser->id);
                $rowCount = $this->inventoryService->getRowCountOptimized($request, $authUser->id);
            }
        }




        // return view('backend.admin.inventory.index');
        // dd('index', $data->get()[0], $info, $rowCount, $trashedCount);
        // dd($info->get()[0]);
        if($request->ajax()){
            // dd($request->all(), );
            return DataTables::of($info)->addIndexColumn()
            ->addColumn('check', function ($row) {
                $html = '<div class=" text-center">
                            <input type="checkbox" name="admin_inventory_id[]" value="' . $row->id . '" class="mt-2 check1">
                        </div>';
                return $html;
            })
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            // ->addColumn('local_image_num', function ($row) {

            //     $imagePaths = explode(',', $row->additionalInventory->local_img_url);
            //     // $folderPath = public_path('listing/');  // Change this to your actual image directory

            //     // $existingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //     //     return file_exists($folderPath . $image);
            //     // });

            //     $folderPath = rtrim(public_path(''), '/') . '/';
            //     $existingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //         return file_exists($folderPath . ltrim($image, '/'));
            //     });

            //     $local_image_count = count($existingImages);
            //     return '<a href="' . route('admin.image.edit', $row->id) . '">' . $local_image_count . ' Images </a>';
            // })
            ->addColumn('local_image_num', function ($row) {
                // $local_image = count(explode(',',$row->additionalInventory->local_img_url));
                // $all_image = count(explode(',',$row->img_from_url));
                // return '<a href="'.route('admin.image.edit',$row->id).'">'.$local_image .' Images </a>';
                $local_image_count = $row->image_count;
                return '<a href="' . route('admin.image.edit', $row->id) . '">' . $local_image_count . ' Images </a>';
            })
            ->addColumn('stock', function($row){
                return $row->stock ?? 'No stock';
            })
            // ->addColumn('title', function($row){
            //     return $row->year.$row->make.$row->model ?? 'No title';
            // })

            ->addColumn('year', function ($row) {
                return ($row->year ?? 'No Year');
            })
            ->addColumn('make', function ($row) {
                return $row->make ?? 'No make';
            })
            ->addColumn('model', function ($row) {
                return ($row->model ?? '') ?: 'No Model';
            })
            ->addColumn('dealer_name', function($row){
                return $row->dealer->name ?? 'No Dealer Name';
            })
            ->addColumn('state', function($row){
                return $row->dealer->state ?? 'No State';
            })
            ->addColumn('city', function($row){
                return $row->dealer->city ?? 'No City';
            })
            // <th>Visibility</th>
            ->addColumn('listing_start', function($row){
                return  $row->created_at ? Carbon::parse($row->created_at)->format('m-d-Y') : 'null';
            })->addColumn('active_start', function($row){
                return  $row->active_till ? Carbon::parse($row->active_till)->format('m-d-Y') : 'null';
            })
            ->addColumn('active_end', function($row){
                return  $row->featured_till ? Carbon::parse($row->featured_till)->format('m-d-Y') : 'null';
            })
            ->addColumn('paid', function($row){
                return $row->is_feature == '1' ? 'Feature' : 'Free';
            })
            ->addColumn('status', function($row){
                return $row->inventory_status;
            })
            ->addColumn('visibility', function($row){
                $today = Carbon::now();
                $isActive = true;

                if ($row->active_till) {
                    if (Carbon::parse($row->active_till)->diffInMonths($today) >= 1) {
                        $isActive = false;
                    }
                } else {
                    if ($row->created_at && Carbon::parse($row->created_at)->diffInMonths($today) >= 1) {
                        $isActive = false;
                    }
                }
                $status = $isActive ? 'Active' : 'Inactive';
                $colorClass = $isActive ? 'btn badge badge-success' : 'btn badge badge-danger';
                return "<span class='{$colorClass}'>{$status}</span>";
            })
            ->addColumn('action', function($row) {
                if ($row->trashed()) {
                    $html = '<a href="'.route('admin.inventory.restore', $row->id).'" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> '.
                            '<a href="'.route('admin.inventory.permanent.delete', $row->id).'" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                }else{


                    $html = '';
                            // if(Auth::user()->hasAllaccess())
                            // {
                            //     $html = ' <a href="javascript:void(0);" class="btn btn-warning btn-sm send-mail" data-id="' . $row->id . '"><i class="fa fa-paper-plane"></i></a>';
                            // }
                            $html .= '<a href="'.$row->additionalInventory->detail_url.'" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-link"></i></a> &nbsp; <a href="' . route('admin.inventory.edit.page', $row->id) . '" style="margin-right: 6px !important" class="btn btn-success btn-sm lead_view"><i class="fa fa-eye"></i></a>' .
                            '<a href="javascript:void(0);" class="btn btn-danger btn-sm inventory_delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                }
                return $html;
            })
            // ->filter(function ($query) {
            //     if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
            //         // For normal Eloquent queries
            //         $records = $query->get()->filter(function ($row) {
            //             $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //             $folderPath = public_path('/');
            //             $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //                 return !file_exists($folderPath . ltrim($image, '/'));
            //             });
            //             return count($missingImages) > 0;
            //         });
            //         $query->whereIn('id', $records->pluck('id'));
            //     } elseif ($query instanceof \Illuminate\Support\Collection) {
            //         // For collection-based queries (like trashed records)
            //         $query = $query->filter(function ($row) {
            //             $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //             $folderPath = public_path('/');
            //             $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //                 return !file_exists($folderPath . ltrim($image, '/'));
            //             });
            //             return count($missingImages) > 0;
            //         });
            //     }
            // })

            // ->filter(function ($query) {
            //     $records = $query->get()->filter(function ($row) {
            //         $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //         $folderPath = rtrim(public_path(''), '/') . '/';
            //         $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //             return !file_exists($folderPath . ltrim($image, '/'));
            //         });

            //         // Show rows where at least one image is missing
            //         return count($missingImages) > 0;
            //     });

            //     // Replace the original query with the filtered collection
            //     $query->whereIn('id', $records->pluck('id'));
            // })

            ->rawColumns(['visibility','action', 'status', 'check','local_image_num'])
            ->with([
                'allRow' => $rowCount,
                'trashedRow' => $trashedCount,
            ])
            ->smart(true)
            ->make(true);
        }
        return view('backend.admin.inventory.index', $data);
    }

    public function updateInventory(Request $request)
    {

        // $data = $this->inventoryService->all();
        $authUser = Auth::user();
        // $inventory = Inventory::query();
        $inventory = MainInventory::query();

        if($authUser->hasAllaccess())
        {
            $data['inventory_make'] = $inventory->distinct('make')->pluck('id','make')->toArray();

            $users = User::whereHas('roles', function($query) {
                $query->where('name', 'dealer');
            })
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->get(['id', 'name', 'city', 'state']);
            $data['inventory_dealer_name'] = $users->pluck('id', 'name')->toArray();
            $data['inventory_dealer_city'] = $users->pluck('id', 'city')->toArray();
            $data['inventory_dealer_state'] = $users->pluck('id', 'state')->toArray();
        }else
        {
            $data['inventory_make'] = $inventory->where('deal_id',$authUser->id)->distinct('make')->pluck('id','make')->toArray();
            $data['inventory_dealer_name'] = User::where('id',$authUser->id)->pluck('id','name')->toArray();
            $data['inventory_dealer_city'] = User::where('id',$authUser->id)->whereNotNull('city')
            ->where('city', '!=', '')->pluck('id','city')->toArray();
            $data['inventory_dealer_state'] = User::where('id',$authUser->id)->whereNotNull('state')
            ->where('state', '!=', '')->pluck('id','state')->toArray();
        }

        ksort($data['inventory_make']);
        ksort($data['inventory_dealer_name']);
        ksort($data['inventory_dealer_city']);
        ksort($data['inventory_dealer_state']);


        $info = [];
        $rowCount = 0;
        $trashedCount = 0;

        if ($request->showTrashed == 'true') {
            if ($authUser->hasAllaccess()) {
                // Fetch trashed items, not just the count
                $info = $this->inventoryService->getUpdateItemByFilterWithOptimized($request);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount();
                $rowCount = $this->inventoryService->getRowCountOptimized($request);
            } else {
                // Fetch trashed items for a specific dealer
                $info = $this->inventoryService->getUpdateItemByFilterWithOptimized($request,$authUser->id);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount($authUser->id);
                $rowCount = $this->inventoryService->getRowCountOptimized($request, $authUser->id);
            }
        } else {
            if ($authUser->hasAllaccess()) {
                // Fetch active inventory
                $info = $this->inventoryService->getUpdateItemByFilterWithOptimized($request);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount();
                $rowCount = $this->inventoryService->getRowCountOptimized($request);
            } else {
                // Fetch active inventory for specific dealer
                $info = $this->inventoryService->getUpdateItemByFilterWithOptimized($request, $authUser->id);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount($authUser->id);
                $rowCount = $this->inventoryService->getRowCountOptimized($request, $authUser->id);
            }
        }

        // dd($info->get());


        // return view('backend.admin.inventory.index');
        // dd('index', $data->get()[0], $info, $rowCount, $trashedCount);
        // dd($info->get()[0]);
        if($request->ajax()){
            // dd($request->all(), );
            return DataTables::of($info)->addIndexColumn()
            ->addColumn('check', function ($row) {
                $html = '<div class=" text-center">
                            <input type="checkbox" name="admin_inventory_id[]" value="' . $row->id . '" class="mt-2 check1">
                        </div>';
                return $html;
            })
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            // ->addColumn('local_image_num', function ($row) {

            //     $imagePaths = explode(',', $row->additionalInventory->local_img_url);
            //     // $folderPath = public_path('listing/');  // Change this to your actual image directory

            //     // $existingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //     //     return file_exists($folderPath . $image);
            //     // });

            //     $folderPath = rtrim(public_path(''), '/') . '/';
            //     $existingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //         return file_exists($folderPath . ltrim($image, '/'));
            //     });

            //     $local_image_count = count($existingImages);
            //     return '<a href="' . route('admin.image.edit', $row->id) . '">' . $local_image_count . ' Images </a>';
            // })
            ->addColumn('local_image_num', function ($row) {
                // $local_image = count(explode(',',$row->additionalInventory->local_img_url));
                // $all_image = count(explode(',',$row->img_from_url));
                // return '<a href="'.route('admin.image.edit',$row->id).'">'.$local_image .' Images </a>';
                $local_image_count = $row->image_count;
                return '<a href="' . route('admin.image.edit', $row->id) . '">' . $local_image_count . ' Images </a>';
            })
            ->addColumn('stock', function($row){
                return $row->stock ?? 'No stock';
            })
            // ->addColumn('title', function($row){
            //     return $row->year.$row->make.$row->model ?? 'No title';
            // })

            ->addColumn('year', function ($row) {
                return ($row->year ?? 'No Year');
            })
            ->addColumn('make', function ($row) {
                return $row->make ?? 'No make';
            })
            ->addColumn('model', function ($row) {
                return ($row->model ?? '') ?: 'No Model';
            })
            ->addColumn('dealer_name', function($row){
                return $row->dealer->name ?? 'No Dealer Name';
            })
            ->addColumn('state', function($row){
                return $row->dealer->state ?? 'No State';
            })
            ->addColumn('city', function($row){
                return $row->dealer->city ?? 'No City';
            })
            // <th>Visibility</th>
            ->addColumn('listing_start', function($row){
                return  $row->created_at ? Carbon::parse($row->created_at)->format('m-d-Y') : 'null';
            })->addColumn('active_start', function($row){
                return  $row->active_till ? Carbon::parse($row->active_till)->format('m-d-Y') : 'null';
            })
            ->addColumn('active_end', function($row){
                return  $row->featured_till ? Carbon::parse($row->featured_till)->format('m-d-Y') : 'null';
            })
            ->addColumn('paid', function($row){
                return $row->is_feature == '1' ? 'Feature' : 'Free';
            })
            ->addColumn('status', function($row){
                return $row->inventory_status;
            })
            ->addColumn('visibility', function($row){
                $today = Carbon::now();
                $isActive = true;

                if ($row->active_till) {
                    if (Carbon::parse($row->active_till)->diffInMonths($today) >= 1) {
                        $isActive = false;
                    }
                } else {
                    if ($row->created_at && Carbon::parse($row->created_at)->diffInMonths($today) >= 1) {
                        $isActive = false;
                    }
                }
                $status = $isActive ? 'Active' : 'Inactive';
                $colorClass = $isActive ? 'btn badge badge-success' : 'btn badge badge-danger';
                return "<span class='{$colorClass}'>{$status}</span>";
            })
            ->addColumn('action', function($row) {
                if ($row->trashed()) {
                    $html = '<a href="'.route('admin.inventory.restore', $row->id).'" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> '.
                            '<a href="'.route('admin.inventory.permanent.delete', $row->id).'" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                }else{


                    $html = '';
                            // if(Auth::user()->hasAllaccess())
                            // {
                            //     $html = ' <a href="javascript:void(0);" class="btn btn-warning btn-sm send-mail" data-id="' . $row->id . '"><i class="fa fa-paper-plane"></i></a>';
                            // }
                            $html .= '<a href="'.$row->additionalInventory->detail_url.'" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-link"></i></a> &nbsp; <a href="' . route('admin.inventory.edit.page', $row->id) . '" style="margin-right: 6px !important" class="btn btn-success btn-sm lead_view"><i class="fa fa-eye"></i></a>' .
                            '<a href="javascript:void(0);" class="btn btn-danger btn-sm inventory_delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                }
                return $html;
            })
            // ->filter(function ($query) {
            //     if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
            //         // For normal Eloquent queries
            //         $records = $query->get()->filter(function ($row) {
            //             $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //             $folderPath = public_path('/');
            //             $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //                 return !file_exists($folderPath . ltrim($image, '/'));
            //             });
            //             return count($missingImages) > 0;
            //         });
            //         $query->whereIn('id', $records->pluck('id'));
            //     } elseif ($query instanceof \Illuminate\Support\Collection) {
            //         // For collection-based queries (like trashed records)
            //         $query = $query->filter(function ($row) {
            //             $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //             $folderPath = public_path('/');
            //             $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //                 return !file_exists($folderPath . ltrim($image, '/'));
            //             });
            //             return count($missingImages) > 0;
            //         });
            //     }
            // })

            // ->filter(function ($query) {
            //     $records = $query->get()->filter(function ($row) {
            //         $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //         $folderPath = rtrim(public_path(''), '/') . '/';
            //         $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //             return !file_exists($folderPath . ltrim($image, '/'));
            //         });

            //         // Show rows where at least one image is missing
            //         return count($missingImages) > 0;
            //     });

            //     // Replace the original query with the filtered collection
            //     $query->whereIn('id', $records->pluck('id'));
            // })

            ->rawColumns(['visibility','action', 'status', 'check','local_image_num'])
            ->with([
                'allRow' => $rowCount,
                'trashedRow' => $trashedCount,
            ])
            ->smart(true)
            ->make(true);
        }
        return view('backend.admin.inventory.index', $data);
    }

    public function soldInventory(Request $request)
    {

        // $data = $this->inventoryService->all();
        $authUser = Auth::user();
        // $inventory = Inventory::query();
        $inventory = MainInventory::query();

        if($authUser->hasAllaccess())
        {
            $data['inventory_make'] = $inventory->distinct('make')->pluck('id','make')->toArray();

            $users = User::whereHas('roles', function($query) {
                $query->where('name', 'dealer');
            })
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->get(['id', 'name', 'city', 'state']);
            $data['inventory_dealer_name'] = $users->pluck('id', 'name')->toArray();
            $data['inventory_dealer_city'] = $users->pluck('id', 'city')->toArray();
            $data['inventory_dealer_state'] = $users->pluck('id', 'state')->toArray();
        }else
        {
            $data['inventory_make'] = $inventory->where('deal_id',$authUser->id)->distinct('make')->pluck('id','make')->toArray();
            $data['inventory_dealer_name'] = User::where('id',$authUser->id)->pluck('id','name')->toArray();
            $data['inventory_dealer_city'] = User::where('id',$authUser->id)->whereNotNull('city')
            ->where('city', '!=', '')->pluck('id','city')->toArray();
            $data['inventory_dealer_state'] = User::where('id',$authUser->id)->whereNotNull('state')
            ->where('state', '!=', '')->pluck('id','state')->toArray();
        }

        ksort($data['inventory_make']);
        ksort($data['inventory_dealer_name']);
        ksort($data['inventory_dealer_city']);
        ksort($data['inventory_dealer_state']);


        $info = [];
        $rowCount = 0;
        $trashedCount = 0;

        if ($request->showTrashed == 'true') {
            if ($authUser->hasAllaccess()) {
                // Fetch trashed items, not just the count
                $info = $this->inventoryService->getSoldItemByFilterWithOptimized($request);
                $trashedCount = $this->inventoryService->getSoldTrashedCountOptimizedCount();
                $rowCount = $this->inventoryService->getSoldRowCountOptimized($request);
            } else {
                // Fetch trashed items for a specific dealer
                $info = $this->inventoryService->getSoldItemByFilterWithOptimized($request,$authUser->id);
                $trashedCount = $this->inventoryService->getSoldTrashedCountOptimizedCount($authUser->id);
                $rowCount = $this->inventoryService->getSoldRowCountOptimized($request, $authUser->id);
            }
        } else {
            if ($authUser->hasAllaccess()) {
                // Fetch active inventory
                $info = $this->inventoryService->getSoldItemByFilterWithOptimized($request);
                $trashedCount = $this->inventoryService->getSoldTrashedCountOptimizedCount();
                $rowCount = $this->inventoryService->getSoldRowCountOptimized($request);
            } else {
                // Fetch active inventory for specific dealer
                $info = $this->inventoryService->getSoldItemByFilterWithOptimized($request, $authUser->id);
                $trashedCount = $this->inventoryService->getSoldTrashedCountOptimizedCount($authUser->id);
                $rowCount = $this->inventoryService->getSoldRowCountOptimized($request, $authUser->id);
            }
        }




        // return view('backend.admin.inventory.index');
        // dd('index', $data->get()[0], $info, $rowCount, $trashedCount);
        // dd($info->get()[0]);
        if($request->ajax()){
            // dd($request->all(), );
            return DataTables::of($info)->addIndexColumn()
            ->addColumn('check', function ($row) {
                $html = '<div class=" text-center">
                            <input type="checkbox" name="admin_inventory_id[]" value="' . $row->id . '" class="mt-2 check1">
                        </div>';
                return $html;
            })
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            // ->addColumn('local_image_num', function ($row) {

            //     $imagePaths = explode(',', $row->additionalInventory->local_img_url);
            //     // $folderPath = public_path('listing/');  // Change this to your actual image directory

            //     // $existingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //     //     return file_exists($folderPath . $image);
            //     // });

            //     $folderPath = rtrim(public_path(''), '/') . '/';
            //     $existingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //         return file_exists($folderPath . ltrim($image, '/'));
            //     });

            //     $local_image_count = count($existingImages);
            //     return '<a href="' . route('admin.image.edit', $row->id) . '">' . $local_image_count . ' Images </a>';
            // })
            ->addColumn('local_image_num', function ($row) {
                // $local_image = count(explode(',',$row->additionalInventory->local_img_url));
                // $all_image = count(explode(',',$row->img_from_url));
                // return '<a href="'.route('admin.image.edit',$row->id).'">'.$local_image .' Images </a>';
                $local_image_count = $row->image_count;
                return '<a href="' . route('admin.image.edit', $row->id) . '">' . $local_image_count . ' Images </a>';
            })
            ->addColumn('stock', function($row){
                return $row->stock ?? 'No stock';
            })
            // ->addColumn('title', function($row){
            //     return $row->year.$row->make.$row->model ?? 'No title';
            // })

            ->addColumn('year', function ($row) {
                return ($row->year ?? 'No Year');
            })
            ->addColumn('make', function ($row) {
                return $row->make ?? 'No make';
            })
            ->addColumn('model', function ($row) {
                return ($row->model ?? '') ?: 'No Model';
            })
            ->addColumn('dealer_name', function($row){
                return $row->dealer->name ?? 'No Dealer Name';
            })
            ->addColumn('state', function($row){
                return $row->dealer->state ?? 'No State';
            })
            ->addColumn('city', function($row){
                return $row->dealer->city ?? 'No City';
            })
            // <th>Visibility</th>
            ->addColumn('listing_start', function($row){
                return  $row->created_at ? Carbon::parse($row->created_at)->format('m-d-Y') : 'null';
            })->addColumn('active_start', function($row){
                return  $row->active_till ? Carbon::parse($row->active_till)->format('m-d-Y') : 'null';
            })
            ->addColumn('active_end', function($row){
                return  $row->featured_till ? Carbon::parse($row->featured_till)->format('m-d-Y') : 'null';
            })
            ->addColumn('paid', function($row){
                return $row->is_feature == '1' ? 'Feature' : 'Free';
            })
            ->addColumn('status', function($row){
                return $row->inventory_status;
            })
            ->addColumn('visibility', function($row){
                $today = Carbon::now();
                $isActive = true;

                if ($row->active_till) {
                    if (Carbon::parse($row->active_till)->diffInMonths($today) >= 1) {
                        $isActive = false;
                    }
                } else {
                    if ($row->created_at && Carbon::parse($row->created_at)->diffInMonths($today) >= 1) {
                        $isActive = false;
                    }
                }
                $status = $isActive ? 'Active' : 'Inactive';
                $colorClass = $isActive ? 'btn badge badge-success' : 'btn badge badge-danger';
                return "<span class='{$colorClass}'>{$status}</span>";
            })
            ->addColumn('action', function($row) {
                if ($row->trashed()) {
                    $html = '<a href="'.route('admin.inventory.restore', $row->id).'" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> '.
                            '<a href="'.route('admin.inventory.permanent.delete', $row->id).'" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                }else{


                    $html = '';
                            // if(Auth::user()->hasAllaccess())
                            // {
                            //     $html = ' <a href="javascript:void(0);" class="btn btn-warning btn-sm send-mail" data-id="' . $row->id . '"><i class="fa fa-paper-plane"></i></a>';
                            // }
                            $html .= '<a href="'.$row->additionalInventory->detail_url.'" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-link"></i></a> &nbsp; <a href="' . route('admin.inventory.edit.page', $row->id) . '" style="margin-right: 6px !important" class="btn btn-success btn-sm lead_view"><i class="fa fa-eye"></i></a>' .
                            '<a href="javascript:void(0);" class="btn btn-danger btn-sm inventory_delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                }
                return $html;
            })
            // ->filter(function ($query) {
            //     if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
            //         // For normal Eloquent queries
            //         $records = $query->get()->filter(function ($row) {
            //             $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //             $folderPath = public_path('/');
            //             $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //                 return !file_exists($folderPath . ltrim($image, '/'));
            //             });
            //             return count($missingImages) > 0;
            //         });
            //         $query->whereIn('id', $records->pluck('id'));
            //     } elseif ($query instanceof \Illuminate\Support\Collection) {
            //         // For collection-based queries (like trashed records)
            //         $query = $query->filter(function ($row) {
            //             $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //             $folderPath = public_path('/');
            //             $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //                 return !file_exists($folderPath . ltrim($image, '/'));
            //             });
            //             return count($missingImages) > 0;
            //         });
            //     }
            // })

            // ->filter(function ($query) {
            //     $records = $query->get()->filter(function ($row) {
            //         $imagePaths = explode(',', $row->additionalInventory->local_img_url ?? '');
            //         $folderPath = rtrim(public_path(''), '/') . '/';
            //         $missingImages = array_filter($imagePaths, function ($image) use ($folderPath) {
            //             return !file_exists($folderPath . ltrim($image, '/'));
            //         });

            //         // Show rows where at least one image is missing
            //         return count($missingImages) > 0;
            //     });

            //     // Replace the original query with the filtered collection
            //     $query->whereIn('id', $records->pluck('id'));
            // })

            ->rawColumns(['visibility','action', 'status', 'check','local_image_num'])
            ->with([
                'allRow' => $rowCount,
                'trashedRow' => $trashedCount,
            ])
            ->smart(true)
            ->make(true);
        }
        return view('backend.admin.inventory.index', $data);
    }


    public function noImageIndex(Request $request)
    {

        // $data = $this->inventoryService->all();
        $authUser = Auth::user();
        // $inventory = Inventory::query();
        $inventory = MainInventory::query();

        if($authUser->hasAllaccess())
        {
            $data['inventory_make'] = $inventory->distinct('make')->pluck('id','make')->toArray();

            $users = User::whereHas('roles', function($query) {
                $query->where('name', 'dealer');
            })
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->get(['id', 'name', 'city', 'state']);
            $data['inventory_dealer_name'] = $users->pluck('id', 'name')->toArray();
            $data['inventory_dealer_city'] = $users->pluck('id', 'city')->toArray();
            $data['inventory_dealer_state'] = $users->pluck('id', 'state')->toArray();
        }else
        {
            $data['inventory_make'] = $inventory->where('deal_id',$authUser->id)->distinct('make')->pluck('id','make')->toArray();
            $data['inventory_dealer_name'] = User::where('id',$authUser->id)->pluck('id','name')->toArray();
            $data['inventory_dealer_city'] = User::where('id',$authUser->id)->whereNotNull('city')
            ->where('city', '!=', '')->pluck('id','city')->toArray();
            $data['inventory_dealer_state'] = User::where('id',$authUser->id)->whereNotNull('state')
            ->where('state', '!=', '')->pluck('id','state')->toArray();
        }

        ksort($data['inventory_make']);
        ksort($data['inventory_dealer_name']);
        ksort($data['inventory_dealer_city']);
        ksort($data['inventory_dealer_state']);


        $info = [];
        $rowCount = 0;
        $trashedCount = 0;

        if ($request->showTrashed == 'true') {
            if ($authUser->hasAllaccess()) {
                // Fetch trashed items, not just the count
                $info = $this->inventoryService->getItemByFilterNoImgWithOptimized($request);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount();
                $rowCount = $this->inventoryService->getRowCountOptimized($request);
            } else {
                // Fetch trashed items for a specific dealer
                $info = $this->inventoryService->getItemByFilterNoImgWithOptimized($request, $authUser->id);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount($authUser->id);
                $rowCount = $this->inventoryService->getRowCountOptimized($request, $authUser->id);
            }
        } else {
            if ($authUser->hasAllaccess()) {
                // Fetch active inventory
                $info = $this->inventoryService->getItemByFilterNoImgWithOptimized($request);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount();
                $rowCount = $this->inventoryService->getRowCountOptimized($request);
            } else {
                // Fetch active inventory for specific dealer
                $info = $this->inventoryService->getItemByFilterNoImgWithOptimized($request, $authUser->id);
                $trashedCount = $this->inventoryService->getTrashedCountOptimizedCount($authUser->id);
                $rowCount = $this->inventoryService->getRowCountOptimized($request, $authUser->id);
            }
        }




        // return view('backend.admin.inventory.index');
        // dd('index', $data->get()[0], $info, $rowCount, $trashedCount);
        // dd($info->get()[0]);
        if($request->ajax()){
            return DataTables::of($info)->addIndexColumn()
            ->addColumn('check', function ($row) {
                $html = '<div class=" text-center">
                            <input type="checkbox" name="admin_inventory_id[]" value="' . $row->id . '" class="mt-2 check1">
                        </div>';
                return $html;
            })
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->addColumn('local_image_num', function ($row) {
                $local_image_count = $row->image_count;
                return '<a href="' . route('admin.image.edit', $row->id) . '">' . $local_image_count . ' Images </a>';
            })
            ->addColumn('detail_url', function ($row) {
                return $row->additionalInventory->detail_url ?? 'N/A';
            })
            ->addColumn('img_from_url', function ($row) {
                return $row->additionalInventory->img_from_url ?? 'N/A';
            })
            ->addColumn('make', function ($row) {
                return $row->make ?? 'No make';
            })
            ->addColumn('model', function ($row) {
                return ($row->model ?? '') ?: 'No Model';
            })
            ->addColumn('dealer_name', function($row){
                return $row->dealer->name ?? 'No Dealer Name';
            })
            ->addColumn('state', function($row){
                return $row->dealer->state ?? 'No State';
            })
            ->addColumn('city', function($row){
                return $row->dealer->city ?? 'No City';
            })
            ->rawColumns(['check','local_image_num'])
            ->with([
                'allRow' => $rowCount,
                'trashedRow' => $trashedCount,
            ])
            ->smart(true)
            ->make(true);
        }
        return view('backend.admin.inventory.no-img-index', $data);
    }


    public function activeInventoryListDP(Request $request)
    {

        $authUser = Auth::user();

        // Base query with relationships and selected columns
        $mainInventory = MainInventory::with([
            'additionalInventory:id,main_inventory_id,detail_url'
        ])
        ->select('id', 'deal_id', 'title', 'vin', 'year', 'make', 'model', 'image_count', 'price');

        // Apply access control filters
        if (!$authUser->hasAllaccess()) {
            $mainInventory->where('deal_id', $authUser->id);
        }

        // Clone the query for counting trashed records (with same conditions)
        $trashedQuery = clone $mainInventory;

        // Get counts
        $allRowCount = $mainInventory->count();
        $trashedRowCount = $trashedQuery->onlyTrashed()->count();

        if ($request->ajax()) {


            return DataTables::of($mainInventory)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($row) {
                    return $row->id;
                })
                ->addColumn('price', function ($row) {
                    return '$'.number_format($row->price);
                })
                ->addColumn('source_url', function ($row) {
                    return $row->additionalInventory->detail_url ?? 'N/A';
                })
                ->with([
                    'allRow' => $allRowCount,
                    'trashedRow' => $trashedRowCount,
                ])
                ->smart(true)
                ->make(true);
        }

        return view('backend.admin.inventory.active-index-dp');
    }


    public function editPage($id)
    {
        // $inventory = Inventory::find($id);
        $inventory = MainInventory::with('dealer', 'mainPriceHistory', 'additionalInventory')->find($id);
        $all_images = explode(',', $inventory->additionalInventory->local_img_url);
        return view('backend.admin.inventory.inventory-edit', compact('inventory', 'all_images'));
    }

    public function imgEditPage($id)
    {
        // $inventory = Inventory::find($id);
        $inventory = MainInventory::with('mainPriceHistory', 'additionalInventory')->find($id);
        $all_images = explode(',', $inventory->additionalInventory->local_img_url);

        return view('backend.admin.inventory.inventory-img', compact('inventory', 'all_images'));
    }

    public function edit(Request $request)
    {

        // log inventory complete
        // $inventory = Inventory::find($request->inventory_id);
        $inventory = MainInventory::find($request->inventory_id);
        $inventory->mpg_city = $request->mpg_city;
        $inventory->mpg_highway = $request->mpg_hwy;
        $inventory->miles = $request->miles;
        $inventory->stock = $request->stock;
        $inventory->price = $request->price;
        $inventory->make = $request->make;
        $inventory->model = $request->model;
        $inventory->year = $request->year;
        $inventory->type = $request->condition;
        $inventory->trim = $request->trim;
        $inventory->body_formated = $request->body_formated;
        $inventory->transmission = $request->transmission;
        $inventory->drive_info = $request->drive_info;
        $inventory->fuel = $request->fuel;
        $inventory->purchase_price = $request->purchase_price;
        $inventory->stock_date_formated = $request->purchase_date;
        $inventory->interior_color = $request->interior_color;
        $inventory->interior_description = $request->interior_description;
        $inventory->exterior_color = $request->exterior_color;
        $inventory->exterior_description = $request->exterior_description;
        $inventory->vehicle_feature_description = $request->description;
        $inventory->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Inventory update successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $this->inventoryService->trash($request);
        return response()->json([
            'status' => 'success',
            'message' => "Inventory Delete Successfully"
        ]);
    }

    public function restore($id)
    {
        $inventory = $this->inventoryService->restore($id);
        return response()->json('Inventory restored successfully');
    }

    public function permanentDelete($id)
    {
        $inventory = $this->inventoryService->permanentDelete($id);

        return response()->json('Inventory is permanently deleted successfully');
    }

    public function bulkAction(Request $request)
    {
        // dd($request->admin_inventory_id, $request->action_type);
        if (isset($request->admin_inventory_id)) {
            if ($request->action_type == 'move_to_trash') {
                $attendance = $this->inventoryService->bulkTrash($request->admin_inventory_id);
                return response()->json('Attendance are deleted successfully');
            } elseif ($request->action_type == 'restore_from_trash') {
                $attendance = $this->inventoryService->bulkRestore($request->admin_inventory_id);

                return response()->json('Attendance are restored successfully');
            } elseif ($request->action_type == 'active') {
                $this->inventoryService->bulkActive($request->admin_inventory_id);
                return response()->json('Attendance are Active successfully');
            } elseif ($request->action_type == 'inactive') {
                $this->inventoryService->bulkInactive($request->admin_inventory_id);
                return response()->json('Attendance are Inactive successfully');
            } elseif ($request->action_type == 'delete_permanently') {
                $attendance = $this->inventoryService->bulkPermanentDelete($request->admin_inventory_id);

                return response()->json('Attendance are permanently deleted successfully');
            } elseif ($request->action_type == 'listingInvoice') {
                $selectedData = $request->admin_inventory_id;
                $existingInvoices = Inventory::with('dealer')->whereIn('id', $selectedData)->get();
                // Track the dealers for the leads
                $dealers = [];
                foreach ($existingInvoices as $invoice) {
                    $dealerId = $invoice->dealer->id;
                    if (!in_array($dealerId, $dealers)) {
                        $dealers[] = $dealerId;
                    }


                    // If more than one unique dealer is found, return an error message
                    if (count($dealers) > 1) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Only one dealer lead can be added at a time.'
                        ]);
                    }
                }

                $invoice = Invoice::where('is_cart', '0')->where('type', 'Listing')->first();
                if ($invoice) {
                    if ($invoice->user_id != $dealers[0]) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Only one dealer lead can be added at a time.'
                        ]);
                    }
                }
                $membership = Membership::where('type', 'listing')->first();

                $invoice  = new Invoice();
                $invoice->price = $membership->membership_price;
                $invoice->user_id = $dealers[0];
                $invoice->type = 'Listing';
                $invoice->save();

                if (!empty($selectedData)) {
                    $invoice->inventories()->syncWithoutDetaching($selectedData);
                }

                // $existingInvoices = Invoice::whereIn("lead_id", $selectedData)->get();
                // $existingDataIds = $existingInvoices->pluck("lead_id")->toArray();
                // $newData = array_diff($selectedData, $existingDataIds);
                // if (!empty($newData)) {
                //     $invoicesToInsert = [];

                //     foreach ($newData as $id) {
                //         $invoicesToInsert[] = [
                //             'lead_id' => $id,
                //             'price' => '4.99',
                //             'user_id' => $dealers[0],
                //             'type' => 'Lead',
                //             'created_at' => now(),
                //             'updated_at' => now(),
                //         ];
                //     }

                // Invoice::insert($invoicesToInsert);
                return response()->json(['status' => 'success', 'message' => 'Added to cart successfully!']);
            } else {
                return response()->json('Action is not specified.');
            }
        } else {
            return response()->json(['message' => 'No Item is Selected.'], 401);
        }
    }
}
