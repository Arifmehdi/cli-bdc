<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VehicleModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vehicles_makes = VehicleMake::orderBy('make_name')->where('status',1)->get();
        $vehicles_model = VehicleModel::orderByDesc('id')->get();
        if ($request->ajax()) {
            return DataTables::of($vehicles_model)
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
                    ->addColumn('make_name', function ($row) {
                        return $row->makeData->make_name ?? '';
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
        return view('backend.admin.vehicle_model.index', compact('vehicles_makes'));
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
            'makeId' => 'required',
            // 'model_name' => 'required|unique:vehicle_models',
            'model_name' => 'required',
            'status' => 'required'
        ]);

        $model =new  VehicleModel;
        $model->vehicle_make_id = $request->makeId;
        $model->model_name = $request->model_name;
        $model->status = $request->status;
        $model->save();

        return response()->json(['success' => 'Model added successfully']);
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
        $vehicles_makes = VehicleMake::orderBy('make_name')->where('status',1)->get();
        $vehicle_model = VehicleModel::find($id);
        if ($vehicle_model) {
             return response()->json(['makelIdData' => $vehicle_model->vehicle_make_id,'modelData' => $vehicle_model->model_name,'statusData' => $vehicle_model->status, 'idData' => $vehicle_model->id, 'vehicles_makes'=>$vehicles_makes ]);
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
            'makeid' => 'required|numeric',
            // 'model_name' => 'required|unique:vehicle_models,model_name,'.$id,
            'model_name' => 'required',
            'status' => 'required'
        ]);

        $vehicle = VehicleModel::find($id);
        $vehicle->vehicle_make_id = $request->makeid;
        $vehicle->model_name = $request->model_name;
        $vehicle->status = $request->status;
        $vehicle->save();
        return response()->json(['success' => 'Model updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = VehicleModel::find($id);
        $model->delete();
        return response()->json(['success' => 'Model deleted successfully']);
    }
}
