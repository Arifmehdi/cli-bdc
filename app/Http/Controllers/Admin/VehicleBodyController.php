<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class VehicleBodyController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $vehicles = VehicleBody::orderByDesc('id')->get();
        if ($request->ajax()) {
            return DataTables::of($vehicles)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($vehicle) {
                        return $vehicle->id; // Use any unique identifier for your rows
                    })
                    ->addColumn('check', function ($row) {
                        $html = '<input type="checkbox" name="make_id[]" value="' . $row->id . '" class="mt-2 check1">';
                        return $html;
                    })
                    ->addColumn('image', function ($row) {
                        $html = '<img src="' . asset("storage/" . $row->image) . '" width="100" height="50" />';
                        return $html;
                    })
                    ->addColumn('action', function ($row) {
                        $html = '<a href="'. route('admin.body.edit',$row->id).'" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editMake"><i class="fa fa-edit"></i></a> &nbsp;<a href="#" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })

                    ->rawColumns(['action','image','check'])
                    ->make(true);
        }
        return view('backend.admin.vehicle_body.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'body_name' => 'required',
            'body_image'=> 'required|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $body = new VehicleBody();
        if ($request->hasFile('body_image')) {

            $newImage = $request->file('body_image')->store('body_images', 'public');
            if ($body->image && Storage::disk('public')->exists($body->image)) {
                Storage::disk('public')->delete($body->image);
            }

            $body->image = $newImage;

        }
        $body->name = $request->body_name;
        $body->status = $request->status;
        $body->slug = Str::slug($request->body_name, '_');
        $body->save();

        return response()->json(['status'=>'success','message'=>'Body Saved Successfully']);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vehicle = VehicleBody::find($id);
        if ($vehicle) {
             return response()->json(['bodyData' => $vehicle->name,'statusData' => $vehicle->status,'imageData' => $vehicle->image, 'idData' => $vehicle->id]);
         } else {
             // Handle case where the vehicle with the given ID is not found
             return response()->json(['error' => 'Vehicle not found'], 404);
         }
    }

    /**
     * Update the specified resource in storage.
     */
    public function body_update(Request $request)
    {
        // return $request->all();

        $request->validate([
            'name' => 'required',
        ]);
        $vehicle = VehicleBody::find($request->idData);
        // return $vehicle;
        $vehicle->name = $request->name;
        if ($request->hasFile('body_image')) {

            $newImage = $request->file('body_image')->store('body_images', 'public');
            if ($vehicle->image && Storage::disk('public')->exists($vehicle->image)) {
                Storage::disk('public')->delete($vehicle->image);
            }

            $vehicle->image = $newImage;

        }
        $vehicle->status = $request->status;
        $vehicle->slug = Str::slug($request->name, '_');
        $vehicle->save();
        return response()->json([
        'status'=>'success' ,
        'message' => 'Body updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $make = VehicleBody::find($id);
        $make->delete();
        return response()->json(['success' => 'Body deleted successfully']);
    }
}
