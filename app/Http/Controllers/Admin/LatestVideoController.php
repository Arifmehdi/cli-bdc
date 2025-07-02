<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LatestVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class LatestVideoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = LatestVideo::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })

                ->addColumn('status', function ($row) {
                    $html = "<select class='add-action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
                })->addColumn('action', function ($row) {
                    $html = '<a
          data-id="' . $row->id . '"
          data-title="' . $row->title . '"
          data-sub_title="' . $row->sub_title . '"
          data-url = "' . $row->url . '"
          data-status="' . $row->status . '"
           style="margin-right:3px"
          href="javascript:void(0);"
          class="btn btn-info btn-sm editvideo">
          <i class="fa fa-edit"></i>
           </a>' . '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
           id="video_delete"><i class="fa fa-trash"></i></a>';
                    return $html;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('backend.admin.video.index');
    }
    public function add(Request $request)

    {


        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'sub_title' => 'required|string',
                'url' => 'required|string',


            ], [
                'title.required' => 'Title is required',
                'sub_title.required' => 'Sub title is required',
                'url.required' => 'Url is required',


            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $tend = new LatestVideo();



            $tend->title = $request->title;
            $tend->sub_title = $request->sub_title;
            $tend->url = $request->url;
            $tend->status = $request->status;
            $tend->save();

            return response()->json(['status' => 'success', 'message' => 'Latest video created successfully']);
        } else {
            return 'hi';
        }
    }
    public function update(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'up_title' => 'required|string',
            'up_sub_title' => 'required|string',
            'up_url' => 'required|string',


        ], [
            'up_title.required' => 'The description field is required.',
            'up_sub_title.required' => 'Sub title is required',
            'up_url.required' => 'Url is required',


        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }



        $tend = LatestVideo::find($request->video_id);


        $tend->title = $request->up_title;
        $tend->sub_title = $request->up_sub_title;
        $tend->url = $request->up_url;
        $tend->status = $request->status;

        $tend->save();



        return response()->json([
            'status' => 'success',
            'message' => 'Latest video updated successfully'
        ]);
    }
    public function delete(Request $request)
    {
        $data = LatestVideo::find($request->id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Latest video Deleted Successfully'
        ]);
    }
    public function changeStatus(Request $request)
    {
        $data = LatestVideo::find($request->id);
        if ($data->status == 1) {
            $data->status = '0';
        } else {
            $data->status = '1';
        }
        $data->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Latest video status update successfully'
        ]);
    }
}
