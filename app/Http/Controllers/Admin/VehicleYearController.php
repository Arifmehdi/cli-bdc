<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleYear;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VehicleYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        $vehicles = VehicleYear::orderByDesc('id')->get();
        if ($request->ajax()) {
            return DataTables::of($vehicles)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($row) {
                        return $row->id; // Use any unique identifier for your rows
                    })
                    ->addColumn('sta',function($row){
                        return $row->is_read;
                    })
                    ->addColumn('check', function ($row) {
                        $html = '<input type="checkbox" name="year_id[]" value="' . $row->id . '" class="mt-2 check1">';
                        return $html;
                    })
                    ->addColumn('stat', function ($row) {
                        return $row->status== 1 ? 'Active' : 'Inactive' ;
                    })
                    ->addColumn('action', function ($row) {
                        $html = '<a href="'. route('admin.years.edit',$row->id).'" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editMake"><i class="fa fa-edit"></i></a> &nbsp;<a href="'. route('admin.years.destroy',$row->id).'" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })

                    ->rawColumns(['action','check','status'])
                    ->make(true);
        }
        return view('backend.admin.vehicle_year.index');
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
            'year' => 'required|unique:vehicle_years',
            'status' => 'required'
        ]);

        $make =new  VehicleYear();
        $make->year = $request->year;
        $make->status = $request->status;
        $make->save();

        return response()->json(['success' => 'Year added successfully']);
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
        $vehicle_year = VehicleYear::find($id);
        $years = [];
        $currentYear = date('Y');
        for ($currentYear; $currentYear >= 1991; $currentYear--){
            $years[] = $currentYear;
        }
        // dd($currentYear, $years);

        // for (var year = currentYear; year >= 1991; year--) {
        //     html += '<option value="' + year + '">' + year + '</option>';
        // }
        // for ()
        if ($vehicle_year) {
             return response()->json(['statusData' => $vehicle_year->status, 'idData' => $vehicle_year->id, 'yearData'=> $vehicle_year->year,'vehicles_years'=>$years ]);
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
            'year' => 'required|unique:vehicle_years,year,'.$id,
            'status' => 'required'
        ]);
        $vehicle = VehicleYear::find($id);
        $vehicle->year = $request->year;
        $vehicle->status = $request->status;
        $vehicle->save();
        return response()->json(['success' => 'Year updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $make = VehicleYear::find($id);
        $make->delete();
        return response()->json(['success' => 'Year deleted successfully']);
    }
}
