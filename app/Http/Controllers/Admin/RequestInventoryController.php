<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\RequestInventory;
use App\Models\VehicleMake;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class RequestInventoryController extends Controller
{
    public function index(Request $request)
    {


        if ($request->ajax()) {
            $data = RequestInventory::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })
                ->addColumn('Image', function ($row) {
                    $images = json_decode($row->img_from_url, true);
                    $imageFileName = $images[0] ?? null;
                    
                    if ($imageFileName) {
                        return '<img width="80%" src="' . asset('frontend/assets/images/listings/' . $imageFileName) . '" />';
                    }
                    
                })

                ->addColumn('status', function ($row) {
                    $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id' id='banner_activeInactive'>
                        <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                        <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                    </select>";
                    return $html;
                })

                ->addColumn('action', function ($row) {
                    $html = '<a
                data-id="' . $row->id . '"
                data-name="' . $row->name . '"
                data-image="' . $row->image . '"
                data-description="' . $row->description . '"
                data-status="' . $row->status . '"
                data-position="' . $row->position . '"
                data-renew="' . $row->renew . '"
                style="margin-right:3px"
                href="javascript:void(0);"
                class="btn btn-info btn-sm editBanner">
                <i class="fa fa-edit"></i>
            </a>';
                    return $html;
                })




                ->rawColumns(['action', 'Image', 'status'])
                ->make(true);
        }
        return view('backend.admin.requestInventory.index');
    }
    public function status(Request $request)
    {
        try {
            $requestInventory = RequestInventory::findOrFail($request->id);
            $requestInventory->status = $request->status === '1' ? 1 : 0;
            $requestInventory->save();

            $inventory = new Inventory();

            $inventory->status = $requestInventory->status;

            $path = 'frontend/assets/images/listings/';
            $imageNames = [];

            if ($requestInventory->img_from_url) {
                $oldImages = json_decode($requestInventory->img_from_url, true);
                foreach ((array)$oldImages as $oldImage) {
                    $oldImagePath = public_path($path) . $oldImage;
                    if (file_exists($oldImagePath)) {
                        $imageNames[] = $oldImage;
                    }
                }
                $inventory->img_from_url = json_encode($imageNames);
            }
            
            $make = VehicleMake::where('make_name', $requestInventory->make)->first();
            $inventory->title = $requestInventory->year . $requestInventory->make . $requestInventory->model;
            $inventory->vehicle_make_id = $make->id ?? 'null';

            $inventory->year = $requestInventory->year;
            $inventory->make = $requestInventory->make;
            $inventory->model = $requestInventory->model;
            $inventory->vin = $requestInventory->vin;
            $inventory->price = $requestInventory->price;
            $inventory->exterior_color = $requestInventory->exterior_color;
            $inventory->transmission = $requestInventory->transmission;
            $inventory->miles = $requestInventory->miles;
            $inventory->type = 'used';
            $inventory->dealer_id = $requestInventory->user_id;
            $inventory->fuel = $requestInventory->fuel;
            $inventory->drive_info = $requestInventory->drive_info;

            $inventory->save();

            return response()->json(['status' => 'success', 'message' => 'Status updated successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
