<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuPriority;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class NavigationController extends Controller
{
    public function index(Request $request)
    {
        // return siteSetting_variable_create();
        $parents = Menu::where('parent',0)->get();
        $pages = Page::where('status',1)->select('id','slug')->get();

        if($request->ajax()){
             $data = Menu::with(['user','parent_name'])->orderBy('name', 'asc')->get();
            return DataTables::of($data)
            ->addIndexColumn()
        ->addColumn('DT_RowIndex', function ($data) {
            return $data->id; // Use any unique identifier for your rows
        })
        ->addColumn('name', function($row){
            return '<p>' . $row->name . '</p>';
        })
        ->addColumn('slug', function($row){
            return '<p>' . $row->slug . '</p>';
        })
        ->addColumn('route', function($row){
            return '<p>' . $row->route_url . '</p>';
        })
        ->addColumn('param', function($row){
            return '<p>' . $row->param . '</p>';
        })
        ->addColumn('parent', function($row){
            return  ($row->parent_name ? $row->parent_name->name : '') ;
        })
        ->addColumn('created_by', function($row){
            return '<p>' . $row->user->name . '</p>';
        })
        ->addColumn('priority', function ($row) {
            $priorities = MenuPriority::orderBy('id','asc')->get();
            $html = '';
            if ($row->parent == 0) {
            $html .= "<select class='menu_priority form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id' name='priority'>";
            $html .= "<option   value=''>~select~</option>";
            foreach($priorities as $priority)
            {
                $html .= "<option " . ($row->column_position == $priority->position  ? 'selected' : '') . " value='$priority->position'> $priority->position </option>";

            }
            $html .= "</select>";
        } else {
            $html = "--";
        }
        return $html;

        })
        ->addColumn('status', function($row){
            $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
        })
        ->addColumn('action', function($row){
          $html ='<a
          data-id="' . $row->id . '"
          data-name="' . $row->name . '"
          data-parent="' . $row->parent . '"
          data-slug="' . $row->slug . '"
          data-status="' . $row->status . '"
          data-route_url="' . $row->route_url . '"
          data-param="' . $row->param . '"

          style="margin-right:3px"
          href="javascript:void(0);"


          class="btn btn-info btn-sm editMenu">
          <i class="fa fa-edit"></i>
           </a>' . '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
           id="menu_delete"><i class="fa fa-trash"></i></a>';
           return $html;

        })



        ->rawColumns(['action','name','created_by','status','route','param','slug','priority'])
        ->make(true);
       }

        return view('backend.admin.menu.index',compact('parents','pages'));
    }
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:menus,name',
        ], [
            'name.required' => 'Name is required',
            'name.unique' => 'This name has already been taken',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }


        $menu = new Menu();
        $menu->name = $request->name;
        if($request->parent != null ) {
            $menu->parent = $request->parent;
        }

        if($request->slug)
        {
            $menu->slug =$request->slug;
        }else
        {
            $menu->slug ='#';
        }


        $menu->created_by = Auth::id();
        $menu->route_url = $request->route;
        $menu->param = $request->param;
        $menu->status = $request->status;
        $menu->save();
        return response()->json(['status' => 'success', 'message' =>'Menu Saved Successfully']);

    }



    public function changeStatus(Request $request)
    {
        $data = Menu::find($request->id);
        if($data->status==1){
            $data->status ='0';
        }else{
            $data->status ='1';
        }
        $data->save();
        return response()->json([
            'status'=>'success',
            'message'=>'menu status update successfully'
        ]);
    }

    public function updateMenu(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|string',

        ], [
            'name.required' => 'The name field is required.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $menu_update = Menu::find($request->menu_id);
         $menu_update->name = $request->name;
         if($request->parent != null ) {
             $menu_update->parent = $request->parent;
         }
         if($request->slug)
         {
            $menu_update->slug = $request->slug;
         }else
         {
            $menu_update->slug ='#';         }

         $menu_update->created_by = Auth::id();
         $menu_update->route_url = $request->route;
         $menu_update->param = $request->param;
         $menu_update->status = $request->status;
         $menu_update->save();


        return response()->json([
            'status'=>'success',
            'message'=>'Menu updated successfully'
        ]);

    }

    public function changePriority(Request $request)
    {


        $existingMenu = Menu::where('column_position', $request->priority)->first();

        if ($existingMenu) {
            return response()->json(['status'=>'error','message' => 'Menu position already exists']);
        } else {

            $data = Menu::find($request->id);
            $data->column_position = $request->priority;
            $data->save();
            return response()->json([
                'status'=>'success',
                'message'=>'Priority Added Successfully!'
            ]);

        }

    }


    public function deleteMenu(Request $request)
    {
        $data = Menu::find($request->id);
        $data->delete();
        return response()->json([
            'status'=>'success',
            'message'=>'Menu Deleted Successfully'
        ]);
    }
}
