<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class LinkController extends Controller
{
    public function create(Request $request){
        if($request->ajax()){
            $data = Icon::orderBy('created_at', 'desc')->get();
            return datatables::of($data)
            ->addIndexColumn()
            ->addColumn('Icon', function($row){
                return '<img width="10%" src="' . asset("frontend/assets/images/links/{$row->image}") . '" />';
            })
            ->addColumn('status', function($row){
                $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
             </select>";
                return $html;
            })
            ->addColumn('action', function($row){
                $html ='<a
                data-id="' . $row->id . '"
                data-status="' . $row->status . '"
                data-title="' . $row->title . '"
                data-image="' . $row->image . '"
                data-link="' . $row->link . '"style="margin-right:3px"
                href="javascript:void(0);" class="btn btn-info btn-sm editBtn">
                <i class="fa fa-edit"></i>
                 </a>' . '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
                 id="links_delete"><i class="fa fa-trash"></i></a>';
                 return $html;

              })
        ->rawColumns(['action', 'Icon', 'status'])
        ->make(true);
        }
        return view('backend.admin.link.link-create');
    }
    public function add(Request $request){
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'link' => 'required|url',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ], [
                'title.required' => 'Title is required',
                'description.required' => 'Description is required',
                'image.required' => 'Image is required',
                'image.image' => 'Invalid image format',
                'image.max' => 'Image size should not exceed 2MB',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }
            $data = new Icon();
            if($request->hasFile('image')){
                $path = 'frontend/assets/images/links/';
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path($path), $imageName);
                 $data->image = $imageName;
              }

             $data->title = $request->title;
             $data->link = $request->link;
             $data->status = $request->status;
             $data->save();

            return response()->json(['status' => 'success', 'message' => 'Links created successfully']);
        } else {
            return 'hi';
        }
    }

    public function delete(Request $request){
        $data = Icon::find($request->id);
        $data->delete();
        return response()->json([
            'status'=>'success',
            'message'=>'Links Deleted Successfully'
        ]);
    }



    public function update (Request $request){
        $validator = Validator::make($request->all(), [
            'up_title' => 'required|string',
            'up_link' => 'required|url',
            
        ], [
            'up_link.required' => 'The Link field is required.',
            'up_title.required' => 'The title field is required.',
            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $link = Icon::find($request->links_id);
        if ($request->hasFile('up_img') && isset($request->up_img)) {
            $path = 'frontend/assets/images/links/';
            $image = $request->file('up_img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            // Delete the old image if it exists
            if ($link->image != null) {
                $oldImagePath = public_path($path) . $link->image;
                if (file_exists($oldImagePath)) {
                    try {
                        unlink($oldImagePath);
                    } catch (\Exception $e) {
                        error_log('Error deleting old image: ' . $e->getMessage());
                    }
                }
            }

            // Move the new image to the specified path
            $image->move(public_path($path), $imageName);

            // Update the link's image attribute with the new image name
            $link->image = $imageName;
        } else {
            // If no new image is uploaded, keep the existing image name
            if ($link->image != null) {
                $link->image = $link->image;
            }
            
        }
        $link->title = $request->up_title;
        $link->link = $request->up_link;
        $link->status = $request->status;
        $link->save();
        return response()->json([
            'status'=>'success',
            'message'=>'Links updated successfully'
        ]);

    }



    public function statusChange(Request $request)
    {
        $data = Icon::find($request->id);
        if($data->status==1){
            $data->status ='0';
        }else{
            $data->status ='1';
        }
        $data->save();
        return response()->json([
            'status'=>'success',
            'message'=>'Link status updated successfully'
        ]);
    }





}
