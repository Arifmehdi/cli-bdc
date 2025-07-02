<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ResearchManageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $data = Blog::orderByDesc('id')->where('status', '1')->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($data) {
                    return $data->id; // Use any unique identifier for your rows
                })
                ->addColumn('Image', function ($row) {
                    $html = '<img width="20%" src="' . asset("frontend/assets/images/news/" . $row->img) . '" />';
                    return $html;
                })

                ->addColumn('status', function ($row) {
                    // $html = '<p>' .($row->status==1 ? 'Active' : 'Inactive'). '</p>';
                    // return  $html;
                    $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<a
          data-id="' . $row->id . '"
          data-title="' . $row->title . '"
          data-sub_title="' . $row->sub_title . '"
          data-image="' . $row->img . '"
          data-description="' . htmlspecialchars($row->description, ENT_QUOTES, 'UTF-8') . '"
          data-status="' . $row->status . '"
          data-seo_description="' . $row->seo_description . '"
          data-seo_keyword="' . $row->seo_keyword . '"
          style="margin-right:3px"
          href="javascript:void(0);"
          class="btn btn-info btn-sm editBtn">
          <i class="fa fa-edit"></i>
           </a>' .
                        '<a href="javascript:void(0);" data-id="' . $row->id . '" style="margin-right:3px" href="" class="btn btn-primary btn-sm single-news-show"><i class="fa fa-eye"></i></a>' .
                        '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
           id="news_delete"><i class="fa fa-trash"></i></a>';
                    return $html;
                })->rawColumns(['action', 'Image', 'status'])
                ->make(true);
        }

        return view('backend.admin.research.index');
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
