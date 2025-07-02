<?php

namespace App\Http\Controllers\Admin;

use App\Models\VehicleTrim;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class VehicleTrimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $vehicles = VehicleMake::orderByDesc('id')->get();

        $vehicles = VehicleTrim::orderByDesc('id')->get();
        if ($request->ajax()) {
            return DataTables::of($vehicles)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($user) {
                        return $user->id; // Use any unique identifier for your rows
                    })
                    ->addColumn('sta',function($row){
                        return $row->is_read;
                    })
                    ->addColumn('check', function ($row) {
                        $html = '<input type="checkbox" name="make_id[]" value="' . $row->id . '" class="mt-2 check1">';
                        return $html;
                    })
                    ->addColumn('stat', function ($row) {
                        return $row->status== 1 ? 'Active' : 'Inactive' ;
                    })
                    ->addColumn('action', function ($row) {
                        $html = '<a href="'. route('admin.trims.edit',$row->id).'" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editMake"><i class="fa fa-edit"></i></a> &nbsp;<a href="'. route('admin.trims.destroy',$row->id).'" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })

                    ->rawColumns(['action','check','status'])
                    ->make(true);
        }
        return view('backend.admin.vehicle_trim.index');
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
        $request->validate([
            'trim_name' => 'required|unique:vehicle_trims',
            'status' => 'required'
        ]);

        $make =new  VehicleTrim;
        $make->trim_name = $request->trim_name;
        $make->status = $request->status;
        $make->save();

        return response()->json(['success' => 'Trim added successfully']);
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
        $vehicle = VehicleTrim::find($id);
        if ($vehicle) {
             return response()->json(['trimData' => $vehicle->trim_name,'statusData' => $vehicle->status, 'idData' => $vehicle->id]);
         } else {
             // Handle case where the vehicle with the given ID is not found
             return response()->json(['error' => 'Vehicle not found'], 404);
         }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'trim_name' => 'required|unique:vehicle_trims,trim_name,'.$id,
            'status' => 'required'
        ]);
        $vehicle = VehicleTrim::find($id);
        $vehicle->trim_name = $request->trim_name;
        $vehicle->status = $request->status;
        $vehicle->save();
        return response()->json(['success' => 'Trim updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $make = VehicleTrim::find($id);
        $make->delete();
        return response()->json(['success' => 'Trim deleted successfully']);
    }
}
