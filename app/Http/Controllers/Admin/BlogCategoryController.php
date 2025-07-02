<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str; 

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $datas = DB::table('blog_categories')->orderBy('created_at','desc')->get();
        if ($request->ajax()) {
            return DataTables::of($datas)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($user) {
                        return $user->id; // Use any unique identifier for your rows
                    })

                    ->addColumn('check', function ($row) {
                        $html = '<input type="checkbox" name="state_id[]" value="' . $row->id . '" class="mt-2 check1">';
                        return $html;
                    })
                    ->addColumn('stat', function ($row) {
                        return $row->status== 1 ? 'Active' : 'Inactive' ;
                    })
                    ->addColumn('action', function ($row) {
                        $html = '<a href="#" class="btn btn-sm btn-success edit" title="Edit" data-edit="'.$row->id.'" id="editState"><i class="fa fa-edit"></i></a> &nbsp;<a href="#" class="btn btn-sm btn-danger delete" title="Delete" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a> &nbsp;';
                        return $html;
                    })

                    ->rawColumns(['action','check','stat'])
                    ->make(true);
        }
        return view('backend.admin.blog.category');
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
            'name' => 'required|unique:blog_categories',
            'status' => 'required'
        ]);

        DB::table('blog_categories')->insert([
            'name' => ucfirst($request->name),
            'slug' => Str::slug($request->name),
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => 'Blog Category Added Successfully']);
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
        $data = DB::table('blog_categories')->where('id',$id)->first();
        if ($data) {
             return response()->json(['categoryName' => $data->name,'statusData' => $data->status, 'idData' => $data->id]);
         } else {
             // Handle case where the vehicle with the given ID is not found
             return response()->json(['error' => 'Blog Category not found'], 404);
         }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'edit_category_name' => 'required',
            'status' => 'required'
        ]);
        DB::table('blog_categories')
        ->where('id', $request->idData)
        ->update([
            'name' => $request->edit_category_name,
            'slug' => Str::slug($request->edit_category_name),
            'status' => $request->status,
            'updated_at' => now()
        ]);
        return response()->json(['success' => 'Blog Category updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('blog_categories')->where('id',$id)->delete();
        return response()->json(['success' => 'Blog Category Deleted Successfully']);
    }
}
