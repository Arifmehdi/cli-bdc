<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Icon;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class AdminLogoController extends Controller
{
    public function logoShow(Request $request)
    {

        if($request->ajax()){
            $data = Logo::get();

            return DataTables::of($data)
            ->addIndexColumn()
        ->addColumn('DT_RowIndex', function ($user) {
            return $user->id; // Use any unique identifier for your rows
        })
        ->addColumn('image', function($row) {
            $html = '<img width="20%" src="' . asset("frontend/assets/images/logos/" . $row->image) . '" />';
            return $html;
        })
        ->addColumn('upload_by', function($row) {
            return $row->userName->name ?? 'N/A';
        })
        ->addColumn('status', function($row){
            // $html = '<p>' .($row->status==1 ? 'Active' : 'Inactive'). '</p>';
            // return  $html;
            $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
            return $html;
        })
        ->addColumn('action', function($row){
            $html = '<a
            data-id="' . $row->id . '"

            data-status="' . $row->status . '"
            style="margin-right:3px"
            href="javascript:void(0);"

            class="btn btn-info btn-sm editLogo">
            <i class="fa fa-edit"></i>
            </a>';
            return $html;


        })



        ->rawColumns(['action', 'image', 'status','upload_by'])
        ->make(true);
       }


        return view('backend.admin.logo.index');

    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'logo_image' => 'required',
        ], [
            'logo_image.required' => 'logo  is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $logo = new Logo();
        if($request->hasFile('logo_image')){
            $path = 'frontend/assets/images/logos/';
            $image = $request->file('logo_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path($path), $imageName);
            $logo->image = $imageName;
          }
          $logo->status = $request->status;
          $logo->upload_by = Auth::id();
          $logo->save();
          return response()->json(['status' => 'success', 'message' => 'Logo added successfully']);

    }


    public function update(Request $request){
        $logo = Logo::find($request->logo_id);
        if ($request->hasFile('up_img')) {
            $path = 'frontend/assets/images/logos/';
            $image = $request->file('up_img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Delete the old image if it exists
            if ($logo->image != null) {
                unlink(public_path($path) . $logo->image);
            }

            // Move the new image to the specified path
            $image->move(public_path($path), $imageName);

            // Update the link's image attribute with the new image name
            $logo->image = $imageName;
        } else {
            // If no new image is uploaded, keep the existing image name
            $logo->image = $logo->image;
        }

        $logo->status = $request->up_status;
        $logo->save();
        return response()->json([
            'status'=>'success',
            'message'=>'Logo update successfully'
        ]);

    }



    public function delete(Request $request)
    {
        $logo = Logo::find($request->id);

        // Delete the logo image file from the filesystem
        $path = 'frontend/assets/images/logos/' . $logo->image;
        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }

        $logo->delete();

        return response()->json(['status' => 'success', 'message' => 'Logo deleted successfully']);

    }



    public function statusChange(Request $request)
    {
        $data = Logo::find($request->id);
        if($data->status==1){
            $data->status ='0';
        }else{
            $data->status ='1';
        }
        $data->save();
        return response()->json([
            'status'=>'success',
            'message'=>'logo status update successfully'
        ]);
    }




}
