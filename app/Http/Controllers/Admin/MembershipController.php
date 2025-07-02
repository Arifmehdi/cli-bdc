<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MembershipController extends Controller
{


    public function add(Request $request){
        if($request->ajax()){


            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'price' => 'required',
            ], [
                'name.required' => 'Name is required',
                'price.required' => 'Price is required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $mem = new Membership();
            $mem->name = $request->name;
            $mem->membership_price = $request->price;
            $mem->status = $request->status;
            $mem->type = 'membership';
            $mem->save();
            return response()->json([
                'status'=>'success',
                'message'=>'Membership add successfully'
            ]);





        }else{
            return ('no request here');
        }
    }
    public function update(Request $request){



            $validator = Validator::make($request->all(), [
                'up_name' => 'required',
                'up_price' => 'required',
            ], [
                'up_name.required' => 'Name is required',
                'up_price.required' => 'Price is required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $mem = Membership::find($request->membership_id);
            $mem->name = $request->up_name;
            $mem->membership_price = $request->up_price;
            $mem->status = $request->status;
            $mem->save();
            return response()->json([
                'status'=>'success',
                'message'=>'Membership update successfully'
            ]);






    }

    public function index(Request $request){
        if($request->ajax()){
            $data = Membership::orderBy('id', 'desc')->get();
            return DataTables::of($data)
            ->addIndexColumn()
        ->addColumn('DT_RowIndex', function ($user) {
            return $user->id; // Use any unique identifier for your rows
        })

        ->addColumn('status', function($row){
            $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
        })

        ->addColumn('date', function($row) {
            $formattedDate = \Carbon\Carbon::parse($row->created_at)->format('m-d-y');
            return '<p>' . $formattedDate . '</p>';
        })




        ->addColumn('action', function($row) {
            $html = '<a
                data-id="' . $row->id . '"
                data-name="' . $row->name . '"
                data-type="' . $row->membership_type . '"
                data-price="' . $row->membership_price . '"
                data-status="' . $row->status . '"
                style="margin-right: 3px;"
                href="javascript:void(0);"
                class="btn btn-info btn-sm editMeb">
                <i class="fa fa-edit"></i>
            </a>';

            if (!in_array($row->type, ['listing', 'lead'])) {
                $html .= '<a
                    data-id="' . $row->id . '"
                    style="margin-right: 3px;"
                    href="javascript:void(0);"
                    class="btn btn-danger btn-sm"
                    id="membership_delete">
                    <i class="fa fa-trash"></i>
                </a>';
            }

            return $html;
        })




        ->rawColumns(['action','status','date'])
        ->make(true);
       }

        return view('backend.admin.membership.membership');
    }

    public function delete(Request $request){
        $data = Membership::find($request->id);
        $data->forceDelete();

        return response()->json([
            'status'=>'success',
            'message'=>'Membership Deleted Successfully'
        ]);
    }

    public function change(Request $request)
{
    $data = Membership::find($request->id);
    if($data->status==1){
        $data->status ='0';
    }else{
        $data->status ='1';
    }
    $data->save();
    return response()->json([
        'status'=>'success',
        'message'=>'Membership status update successfully'
    ]);
}
}
