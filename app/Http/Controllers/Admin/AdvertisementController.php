<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{


    public function add_show(Request $request){
        if($request->ajax()){
            $data = Advertisement::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
            ->addIndexColumn()
        ->addColumn('DT_RowIndex', function ($user) {
            return $user->id; // Use any unique identifier for your rows
        })
        ->addColumn('description', function($row){
            return '<p>' . $row->description . '</p>';
        })
        ->addColumn('status', function($row){
            $html = "<select class='add-action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
        })->addColumn('action', function($row){
          $html ='<a
          data-id="' . $row->id . '"
          data-title="' . $row->title . '"
          data-description="' . $row->description . '"
          data-status="' . $row->status . '"
           style="margin-right:3px"
          href="javascript:void(0);"
          class="btn btn-info btn-sm editAd">
          <i class="fa fa-edit"></i>
           </a>' . '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
           id="ad_delete"><i class="fa fa-trash"></i></a>';
           return $html;

        })->rawColumns(['action','description','status'])
        ->make(true);
       }

       return view('backend.admin.advertisement.advertisement-show');
    }


    public function add(Request $request)

{


    if ($request->ajax()) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required',

        ], [
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $ad = new Advertisement();



       $ad->title = $request->title;
       $ad->status = $request->status;

       $ad->description = $request->description;
       $ad->save();

        return response()->json(['status' => 'success', 'message' => 'Ads created successfully']);
    } else {
        return 'hi';
    }
}

public function delete(Request $request){
    $data = Advertisement::find($request->id);
    $data->delete();
    return response()->json([
        'status'=>'success',
        'message'=>'Ads Deleted Successfully'
    ]);
}

public function update (Request $request){


    $validator = Validator::make($request->all(), [
        'up_title' => 'required|string',
        'up_description' => 'required',

    ], [
        'up_description.required' => 'The description field is required.',
        'up_title.required' => 'The title field is required.',

    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }



    $ad=Advertisement::find($request->ad_id);


     $ad->title = $request->up_title;
     $ad->description = $request->up_description;
     $ad->status = $request->status;

     $ad->save();



    return response()->json([
        'status'=>'success',
        'message'=>'Ads updated successfully'
    ]);

}

public function changeStatus(Request $request)
{
    $data = Advertisement::find($request->id);
    if($data->status==1){
        $data->status ='0';
    }else{
        $data->status ='1';
    }
    $data->save();
    return response()->json([
        'status'=>'success',
        'message'=>'Ads status update successfully'
    ]);
}

}
