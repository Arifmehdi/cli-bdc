<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str; 


class BlogSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = DB::table('blog_categories')->select('id','name','slug','status','img')->get();
        $datas = DB::table('blog_sub_categories')->orderBy('created_at','desc')->get();
        if ($request->ajax()) {
            return DataTables::of($datas)
                    ->addIndexColumn()
                    ->addColumn('DT_RowIndex', function ($user) {
                        return $user->id; // Use any unique identifier for your rows
                    })
                    ->addColumn('category_name', function ($row) {
                        return $row->blog_category_id ? DB::table('blog_categories')->where('id',$row->blog_category_id)->value('name') : 'N/A';
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
        return view('backend.admin.blog.subcategory', compact('categories'));
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
            'category_id' => 'required',
            'name' => 'required|unique:blog_categories',
            'status' => 'required'
        ]);

        DB::table('blog_sub_categories')->insert([
            'blog_category_id' => $request->category_id,
            'name' => ucfirst($request->name),
            'slug' => Str::slug($request->name),
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => 'Blog Sub Category Added Successfully']);
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
        $data = DB::table('blog_sub_categories')->where('id',$id)->first();
        if ($data) {
             return response()->json(['category_id' => $data->blog_category_id,'subCategoryName' => $data->name,'statusData' => $data->status, 'idData' => $data->id]);
         } else {
             // Handle case where the vehicle with the given ID is not found
             return response()->json(['error' => 'Blog Sub Category not found'], 404);
         }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function subcategory_update(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'edit_subcategory_name' => 'required',
            'status' => 'required'
        ]);
        DB::table('blog_sub_categories')
        ->where('id', $request->idData)
        ->update([
            'blog_category_id' => $request->category_id,
            'name' => $request->edit_subcategory_name,
            'slug' => Str::slug($request->edit_category_name),
            'status' => $request->status,
            'updated_at' => now()
        ]);
        return response()->json(['success' => 'Blog Sub Category Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('blog_sub_categories')->where('id',$id)->delete();
        return response()->json(['success' => 'Blog Sub Category Deleted Successfully']);
    }
}
