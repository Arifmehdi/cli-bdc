<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Setting;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class AprRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $states = Setting::orderByDesc('key')->where('data_type',1);
        if ($request->ajax()) {
            return DataTables::of($states)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($user) {
                        return $user->id; // Use any unique identifier for your rows
                    })

                    ->addColumn('credit_score', function ($row) {
                        return $row->key;
                    })
                    ->addColumn('rate', function ($row) {
                        return $row->value ;
                    })
                    ->addColumn('status', function ($row) {
                        return $row->status== 1 ? 'Active' : 'Inactive' ;
                    })
                    ->addColumn('action', function ($row) {
                        $html = '<a href="'. route('admin.rates.edit',$row->id).'" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editState"><i class="fa fa-edit"></i></a> &nbsp;<a href="'. route('admin.rates.destroy',$row->id).'" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })
                    ->addColumn('check', function ($row) {
                        $html = '<input type="checkbox" name="state_id[]" value="' . $row->id . '" class="mt-2 check1">';
                        return $html;
                    })

                    ->rawColumns(['action','check','status'])
                    ->make(true);
        }
        return view('backend.admin.apr_rates.index');
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
            'credit' => 'required',
            'rate' => 'required',
            'status' => 'required'
        ]);
        // dd($request->all());
        $make =new  Setting();
        $make->key = $request->credit;
        $make->value = $request->rate;
        $make->data_type = 1;
        $make->status = $request->status;
        $make->save();

        return response()->json(['success' => 'APR added successfully']);
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
       $creditScoreData = Setting::find($id);
       if ($creditScoreData) {
            return response()->json(['creditScore' => $creditScoreData->key,'creditRate' => $creditScoreData->value,'statusData' => $creditScoreData->status, 'idData' => $creditScoreData->id]);
        } else {
            // Handle case where the vehicle with the given ID is not found
            return response()->json(['error' => 'APR not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'key' => 'required',
            'value' => 'required',
            'status' => 'required'
        ]);

        $make =Setting::find($request->idData);
        $make->key = $request->key;
        $make->value = $request->value;
        $make->data_type = 1;
        $make->status = $request->status;
        $make->save();

        return response()->json(['success' => 'APR Updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $make = Setting::find($id);
        $make->delete();
        return response()->json(['success' => 'APR deleted successfully']);
    }
}
