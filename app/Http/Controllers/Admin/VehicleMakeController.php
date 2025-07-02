<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleMake;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VehicleMakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $vehicles = VehicleMake::orderByDesc('id')->get();
        
        $vehicles = VehicleMake::orderBy('make_name');
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
                        $html = '<a href="'. route('admin.makes.edit',$row->id).'" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editMake"><i class="fa fa-edit"></i></a> &nbsp;<a href="'. route('admin.makes.destroy',$row->id).'" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })

                    ->rawColumns(['action','check','status'])
                    ->make(true);
        }
        return view('backend.admin.vehicle_make.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.vehicle_make.ajax.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'make_name' => 'required|unique:vehicle_makes',
            'status' => 'required'
        ]);

        $make =new  VehicleMake;
        $make->make_name = $request->make_name;
        $make->status = $request->status;
        $make->save();

        return response()->json(['success' => 'Make added successfully']);
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
    public function edit( $id)
    {
       $vehicle = VehicleMake::find($id);
       if ($vehicle) {
            return response()->json(['makeData' => $vehicle->make_name,'statusData' => $vehicle->status, 'idData' => $vehicle->id]);
        } else {
            // Handle case where the vehicle with the given ID is not found
            return response()->json(['error' => 'Vehicle not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {


    //     $request->validate([
    //         'make_name' => 'required',
    //         'status' => 'required'
    //     ]);
    //     $vehicle = VehicleMake::find($id);
    //     $vehicle->make_name = $request->make_name;
    //     $vehicle->status = $request->status;
    //     $vehicle->save();
    //     return response()->json(['success' => 'Make updated successfully']);
    // }
    
    public function make_update(Request $request)
    {
        $request->validate([
            'make_name' => 'required',
            'status' => 'required'
        ]);
        $vehicle = VehicleMake::find($request->idData);
        $vehicle->make_name = $request->make_name;
        $vehicle->status = $request->status;
        $vehicle->save();
        return response()->json(['success' => 'Make updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $make = VehicleMake::find($id);
        $make->delete();
        return response()->json(['success' => 'Make deleted successfully']);
    }
}
