<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\LocationZip;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LocationZipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $zips = LocationZip::orderBy('zip_code');
        $states = LocationState::pluck('state_name','id');
        if ($request->ajax()) {
            return DataTables::of($zips)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($user) {
                        return $user->id; // Use any unique identifier for your rows
                    })
                    ->addColumn('location_state_name',function($row){
                        return $row->city->state->state_name;
                    })
                    ->addColumn('city_name',function($row){
                        return $row->city->city_name;
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
                        $html = '<a href="'. route('admin.zips.edit',$row->id).'" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editState"><i class="fa fa-edit"></i></a> &nbsp;<a href="'. route('admin.zips.destroy',$row->id).'" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })

                    ->rawColumns(['action','check','status'])
                    ->make(true);
        }
        return view('backend.admin.location_zip.index', compact('states'));
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
            'state_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'latitude_data' => 'required|numeric',
            'longitude_data' => 'required|numeric',
            'zip_data' => 'required|numeric',
            'sales_tax' => 'required|numeric',
            'status' => 'required'
        ]);

        $make =new  LocationZip();
        $make->location_city_id  = $request->city_id;
        $make->latitude = $request->latitude_data;
        $make->longitude = $request->longitude_data;
        $make->zip_code = $request->zip_data;
        $make->sales_tax = $request->sales_tax;
        $make->status = $request->status;
        $make->save();

        return response()->json(['success' => 'Zip Code added successfully']);
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
        $vehicle = LocationZip::find($id);
        $cities = LocationCity::where('location_state_id', $vehicle->city->state->id)->pluck('city_name', 'id');
        
        if ($vehicle) {
             return response()->json([
                'stateData' => $vehicle->city->state->state_name,
                'cityData' => $vehicle->city->city_name,
                'location_city_id' => $vehicle->location_city_id,
                'latitude_data' => $vehicle->latitude,
                'longitude_data' => $vehicle->longitude,
                'zip_code' => $vehicle->zip_code,
                'sales_tax' => $vehicle->sales_tax,
                'status' => $vehicle->status, 
                'idData' => $vehicle->id,
                'cities' => $cities
            ]);
         } else {
             // Handle case where the vehicle with the given ID is not found 
             return response()->json(['error' => 'Vehicle not found'], 404);
         }
    }

    /**
     * Update the specified resource in storage.
     */

     
     public function zip_update(Request $request)
     {
         $request->validate([
            'state_id' => 'required|numeric',
            'city_name' => 'required|numeric',
            'latitude_data' => 'required|numeric',
            'longitude_data' => 'required|numeric',
            'zip_code' => 'required|numeric',
            'status' => 'required'
         ]);
 
         $zip = LocationZip::find($request->idData);
         $zip->location_city_id  = $request->city_name;
         $zip->latitude = $request->latitude_data;
         $zip->longitude = $request->longitude_data;
         $zip->zip_code = $request->zip_code;
         $zip->sales_tax = $request->sales_tax;
         $zip->status = $request->status;
         $zip->save();
         return response()->json(['success' => 'Zip Code updated successfully']);
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
        $make = LocationZip::find($id);
        $make->forceDelete();
        return response()->json(['success' => 'Zip code deleted successfully']);
    }
}
