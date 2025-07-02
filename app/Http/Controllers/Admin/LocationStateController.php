<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationState;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LocationStateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $states = LocationState::orderBy('state_name');
        if ($request->ajax()) {
            return DataTables::of($states)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($user) {
                        return $user->id; // Use any unique identifier for your rows
                    })
                    ->addColumn('sta',function($row){
                        return $row->is_read;
                    })
                    ->addColumn('check', function ($row) {
                        $html = '<input type="checkbox" name="state_id[]" value="' . $row->id . '" class="mt-2 check1">';
                        return $html;
                    })
                    ->addColumn('stat', function ($row) {
                        return $row->status== 1 ? 'Active' : 'Inactive' ;
                    })
                    ->addColumn('action', function ($row) {
                        $html = '<a href="'. route('admin.states.edit',$row->id).'" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editState"><i class="fa fa-edit"></i></a> &nbsp;<a href="'. route('admin.states.destroy',$row->id).'" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })

                    ->rawColumns(['action','check','status'])
                    ->make(true);
        }
        return view('backend.admin.location_state.index');
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
            'state_name' => 'required|unique:location_states',
            'short_name' => 'required|unique:location_states',
            'sales_tax' => 'required|numeric',
            'status' => 'required'
        ]);

        $make =new  LocationState;
        $make->state_name = ucfirst($request->state_name);
        $make->short_name = strtoupper($request->short_name);
        $make->sales_tax = $request->sales_tax;
        $make->status = $request->status;
        $make->save();

        return response()->json(['success' => 'State added successfully']);
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
        $vehicle = LocationState::find($id);
        if ($vehicle) {
             return response()->json(['stateData' => $vehicle->state_name,'shortNameData' => $vehicle->short_name,'salesTax' => $vehicle->sales_tax,'statusData' => $vehicle->status, 'idData' => $vehicle->id]);
         } else {
             // Handle case where the vehicle with the given ID is not found
             return response()->json(['error' => 'Vehicle not found'], 404);
         }
    }

    /**
     * Update the specified resource in storage.
     */
    public function state_update(Request $request)
    {
        $request->validate([
            'state_name' => 'required',
            'short_name' => 'required',
            'sales_tax' => 'required|numeric',
            'status' => 'required'
        ]);
        $state = LocationState::find($request->idData);
        $state->state_name = ucfirst($request->state_name);
        $state->short_name = strtoupper($request->short_name);
        $state->sales_tax = $request->sales_tax;
        $state->status = $request->status;
        $state->save();
        return response()->json(['success' => 'State updated successfully']);
    }


    public function update(Request $request, string $id)
    {
        //
    }

    /** 
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $make = LocationState::find($id);
        $make->forceDelete();
        return response()->json(['success' => 'State deleted successfully']);
    }
}
