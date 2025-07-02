<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    public function banner_show(Request $request){


        if($request->ajax()){
            $data = Banner::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
            ->addIndexColumn()
        ->addColumn('DT_RowIndex', function ($user) {
            return $user->id; // Use any unique identifier for your rows
        })
        ->addColumn('name', function($row) {
          return ucfirst($row->name);
        })->addColumn('Image', function($row) {
            $html = '<img width="20%" src="' . asset("dashboard/images/banners/" . $row->image) . '" />';
            return $html;
        })->addColumn('position', function($row) {
            return ucfirst($row->position);
        })
        ->addColumn('status', function($row) {
            $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id' id='banner_activeInactive'>
                        <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                        <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                    </select>";
            return $html;
        })

        ->addColumn('action', function($row){
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
     return view('backend.admin.banner.banner');
    }

    public function add(Request $request){




        $validator = Validator::make($request->all(), [
            'name' => 'required|string',

            'description' => 'required|string',
            'renew' => 'required',
            'status' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif',
        ], [
            'name.required' => 'The name field is required.',
            'status.required' => 'This field is required.',

            'description.required' => 'The description field is required.',
            'renew.required' => 'The renew field is required.',
            'image.required' => 'An image file is required.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }


        $banner= new Banner();
        if($request->hasFile('image')){
            $path = '/dashboard/images/banners/';
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path($path), $imageName);
            $banner->image = $imageName;
          }
          $banner->name = $request->name;
          $banner->description = $request->description;
          $banner->status = $request->status;
          $banner->renew = $request->renew;

         $banner->user_id =Auth::id();
          $banner->save();
          return response()->json(['status' => 'success']);
    }


    public function edit(Request $request)
    {
// dd($request->all());
        $validator = Validator::make($request->all(), [
            'up_name' => 'required|string',
            'up_renew' => 'required',
            'up_status' => 'required',
        ], [
            'up_status.required' => 'This field is required.',
            'up_description.required' => 'The description field is required.',
            'up_renew.required' => 'The renew field is required.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }


        $banner= Banner::find($request->banner_id);

        // if ($request->hasFile('up_image')) {
        //     $path = 'dashboard/images/banners/';
        //     $image = $request->file('up_image');
        //     $imageName = time() . '.' . $image->getClientOriginalExtension();

        //     if ( $banner->image != null) {
        //         unlink(public_path($path) .  $banner->image);
        //         $image->move(public_path($path), $imageName);
        //         $banner->image = $imageName;
        //     } else {
        //         $banner->image =  $banner->image;
        //     }
        // }

        if ($request->hasFile('up_image') && isset($request->up_image)) {
            $path = 'dashboard/images/banners/';
            $image = $request->file('up_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Delete the old image if it exists
            if ($banner->image != null) {
                $oldImagePath = public_path($path) . $banner->image;
                if (file_exists($oldImagePath)) {
                    try {
                        unlink($oldImagePath);
                    } catch (\Exception $e) {
                        // Handle the unlinking error
                        // You can log the error or perform other actions as needed
                        // For now, just log the error message
                        error_log('Error deleting old image: ' . $e->getMessage());
                    }
                }
            }

            // Move the new image to the specified path
            $image->move(public_path($path), $imageName);

            // Update the link's image attribute with the new image name
            $banner->image = $imageName;
        } else {
            // If no new image is uploaded, keep the existing image name
            $banner->image = $banner->image;
        }

         $banner->name = $request->up_name;
         $banner->status = $request->up_status;
         $banner->description = $request->up_description;
         $banner->user_id = Auth::id();
         $banner->renew = $request->up_renew;
         $banner->save();

        return response()->json([
            'status'=>'success'
        ]);

    }

    public function changeActiveInactive(Request $request)
    {
        try {
            $bannerActiveInactive = Banner::find($request->id);
            $bannerActiveInactive->status = $request->status === '1' ? 1 : 0;
            $bannerActiveInactive->save();
            return response()->json(['status' => 'success', 'message' => 'Status Change Successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
