<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationCity;
use App\Models\LocationState;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LocationCitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cities = LocationCity::orderBy('city_name');
        $states = LocationState::pluck('state_name','id');

        if ($request->ajax()) {
            return DataTables::of($cities)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($user) {
                        return $user->id; // Use any unique identifier for your rows
                    })
                    ->addColumn('location_state_name',function($row){
                        return $row->state->state_name;
                    })
                    ->addColumn('city_name',function($row){
                        return $row->city_name;
                    })
                    ->addColumn('latitude',function($row){
                        return $row->latitude;
                    })
                    ->addColumn('longitude',function($row){
                        return $row->longitude;
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
                        $html = '<a href="'. route('admin.cities.edit',$row->id).'" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editState"><i class="fa fa-edit"></i></a> &nbsp;<a href="'. route('admin.cities.destroy',$row->id).'" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })

                    ->rawColumns(['action','check','status'])
                    ->make(true);
        }
        return view('backend.admin.location_city.index', compact('states'));
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
            'stateId' => 'required|numeric',
            'city_name' => 'required',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'status' => 'required'
        ]);
        // dd($request->all());
        $make =new  LocationCity();
        $make->location_state_id  = $request->stateId;
        $make->city_name = ucfirst($request->city_name);
        $make->latitude = $request->latitude;
        $make->longitude = $request->longitude;
        $make->status = $request->status;
        $make->save();

        return response()->json(['success' => 'City added successfully']);
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
        $vehicle = LocationCity::find($id);
        if ($vehicle) {
             return response()->json(['stateData' => $vehicle->state->state_name,'cityData' => $vehicle->city_name,'latitude' => $vehicle->latitude,'longitude' => $vehicle->longitude,'status' => $vehicle->status, 'idData' => $vehicle->id,'salesTax'=>$vehicle->sales_tax]);
         } else {
             // Handle case where the vehicle with the given ID is not found
             return response()->json(['error' => 'Vehicle not found'], 404);
         }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    public function city_update(Request $request)
    {

        $request->validate([
            'state_id' => 'numeric',
            'city_name' => 'required',
            'latitude' => 'string',
            'longitude' => 'string',
            'status' => 'required'
        ]);

        $state = LocationCity::find($request->idData);
        $state->location_state_id = $request->state_id;
        $state->city_name = ucfirst($request->city_name);
        $state->latitude = $request->latitude;
        $state->longitude = $request->longitude;
        $state->status = $request->status;
        $state->save();
        return response()->json(['success' => 'City updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $make = LocationCity::find($id);
        $make->forceDelete();
        return response()->json(['success' => 'City deleted successfully']);
    }
}
