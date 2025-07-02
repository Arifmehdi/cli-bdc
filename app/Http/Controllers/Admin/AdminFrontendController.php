<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\UserTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
class AdminFrontendController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Slider::latest()->get();

            return Datatables::of($data)

                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($data) {

                    return $data->id;
                    // return $roles->id; // Use any unique identifier for your rows
                })
                ->addColumn('image', function ($data) {
                    $html = '<img src="' . asset('storage/'.$data->image) . '" width="100%" height="100px"/>';
                    return $html;
                })
                ->addColumn('title', function ($data) {
                    return $data->title;
                })
                ->addColumn('sub_title', function ($data) {
                    return $data->sub_title;
                })
                ->addColumn('status_one', function ($data) {
                    $html = "<select class='action-select " . ($data->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$data->id'>
                                    <option " . ($data->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($data->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<a href="#" style="margin-right:5px" class="btn btn-info btn-sm mb-1 sliderEdit" data-id="'.$row->id.'" data-title="'.$row->title.'" data-sub_title="'.$row->sub_title.'" data-image="'.$row->image.'" data-status="'.$row->status.'"><i class="fa fa-edit"></i></a>' .
                            '<a href="javascript:void(0);" data-id ="' . $row->id . '" class="btn btn-danger btn-sm mb-1 delete_slider"><i class="fa fa-trash"></i></a>';
                    return $html;
                })



                ->rawColumns(['image','title','sub_title','status_one','action'])
                ->make(true);
        }
        return view('backend.admin.slider.index');
    }


    public function store(Request $request)
    {
        $request->validate([

            'image' => 'required|mimes:jpeg,png,jpg,gif',
        ]);
    //    Slider::where('added_by',Auth::id())->delete();
        $slider = new Slider();
        if ($request->hasFile('image')) {

            $newImage = $request->file('image')->store('slider_images', 'public');
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            $slider->image = $newImage;

        }
        $slider->title = $request->title;
        $slider->sub_title = $request->sub_title;
        $slider->status = $request->status;
        $slider->added_by = Auth::id();
        $slider->save();

        return response()->json(['status'=>'success','message'=>'Slider Saved Successfully']);

    }

    public function update(Request $request)
    {
        $slider = Slider::find($request->id);

        if ($request->hasFile('image')) {

            $newImage = $request->file('image')->store('slider_images', 'public');
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            $slider->image = $newImage;

        }

        $slider->title = $request->title;
        $slider->sub_title = $request->sub_title;
        $slider->status = $request->status;
        $slider->added_by = Auth::id();
        $slider->save();
        return response()->json(['status'=>'success','message'=>'Slider Updated Successfully']);
    }

    public function delete(Request $request)
    {
        $data = Slider::find($request->id);
        $data->forceDelete();
        return response()->json([
        'status'=>'success',
        'message' => 'Slider Delete Successfully']);
    }

    public function changeStatus(Request $request)
    {
        $data = Slider::find($request->id);
        if($data->status==1){
            $data->status ='0';
        }else{
            $data->status ='1';
        }
        $data->save();
        return response()->json([
            'status'=>'success',
            'message'=>'Slider status update successfully'
        ]);
    }


    public function getUserHistory(Request $request)
    {

        if ($request->ajax()) {
            $data = UserTrack::with('user')->orderBy('id', 'desc')->get();

            return dataTables::of($data)
                ->addIndexColumn()
                ->addColumn('check', function ($row) {
                    $html = '<div class=" text-center">
                                <input type="checkbox" name="admin_inventory_id[]" value="' . $row->id . '" class="mt-2 check1">
                            </div>';
                    return $html;
                })
                ->addColumn('DT_RowIndex', function ($data) {
                    return $data->id; // Use any unique identifier for your rows
                })->addColumn('user_id', function ($data) {
                    return $data->user->name ?? null; // Use any unique identifier for your rows
                })
                ->addColumn('action', function ($row) {
                    $html = '<a data-id="' . $row->id . '" data-type="' . $row->type . '" data-title="' . $row->title . '"  data-links="' . $row->links . '"  data-image="' . $row->image . '" data-ip_address="' . $row->ip_address . '" style="margin-right:6px !important" class="btn btn-success btn-sm view_history"><i  class="fa fa-eye"></i></a>' .
                        '<a data-id= "' . $row->id . '" class="btn btn-danger btn-sm delete_history"><i  class="fa fa-trash"></i></a>';
                    return $html;
                })

                ->rawColumns(['action','check','user_id'])
                ->make(true);
        }
        return view('backend.admin.history.index');
    }

public function UserHistoryDelete(Request $request)
{
    UserTrack::find($request->id)->delete();
    return response()->json(['status'=>'success','message'=>'History Deleted Successfully!']);
}


}
