<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterContent;
use App\Models\FooterMenu;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class AdminFooterController extends Controller
{
    public function index(Request $request)
    {
        $footer_content  =  FooterContent::latest()->first();
        $pages = Page::where('status',1)->select('id','slug')->get();
        if($request->ajax()){
            $data = FooterMenu::orderBy('name', 'asc')->get();
            return DataTables::of($data)
            ->addIndexColumn()
        ->addColumn('DT_RowIndex', function ($data) {
            return $data->id; // Use any unique identifier for your rows
        })->addColumn('status', function($row){

            $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
        })->addColumn('menu_priority', function($row){
            $html = "<select class='menu_priority form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>";
            // Add options based on the status of the item

            for ($i = 1; $i <= $row->count(); $i++) { // Change '5' to the maximum priority value you have
                $html .= "<option " . ($row->menu_priority == $i ? 'selected' : '') . " value='$i'>$i</option>";
            }
            $html .= "</select>";
            return $html;
        })->addColumn('action', function($row){
          $html ='<a
          data-id="' . $row->id . '"
          data-name="' . $row->name . '"
          data-slug="' . $row->slug . '"
          data-status="' . $row->status . '"
          data-route_url="' . $row->route_url . '"
          data-param="' . $row->param . '"
          data-column_position="' . $row->column_position . '"

          style="margin-right:3px"
          href="javascript:void(0);"


          class="btn btn-info btn-sm editFooter">
          <i class="fa fa-edit"></i>
           </a>' . '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
           id="menu_delete"><i class="fa fa-trash"></i></a>';
           return $html;

        })



        ->rawColumns(['action','status','menu_priority'])
        ->make(true);
       }
        return view('backend.admin.footer.index',compact('footer_content','pages'));
    }

    public function contentStore(Request $request)
    {
        if($request->id)
        {
            $footer_content = FooterContent::find($request->id);
            $footer_content->title = $request->title;
            $footer_content->description = $request->description;
            $footer_content->copyright = $request->copyright;
            $footer_content->save();
        }else
        {
            $footer_content = new FooterContent();
            $footer_content->title = $request->title;
            $footer_content->description = $request->description;
            $footer_content->copyright = $request->copyright;
            $footer_content->save();
        }

        return response()->json(['status'=>'success','message'=> 'footer content saved successfully']);

    }

    public function menuStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:footer_menus,name',
            'footer_col' => 'required',
        ], [
            'name.required' => 'Name is required',
            'name.unique' => 'This name has already been taken',
            'footer_col.required' => 'Footer Column is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $footer_menu = new FooterMenu();
        $footer_menu->name = $request->name;
        if($request->slug)
        {
            $footer_menu->slug = $request->slug;
        }else
        {
            $footer_menu->slug ='#';
        }

        $footer_menu->column_position = $request->footer_col;
        $footer_menu->route_url = $request->route_url;
        $footer_menu->param = $request->param;
        $footer_menu->status = $request->status;
        $footer_menu->save();
        return response()->json(['status' => 'success', 'message' => 'Footer Menu Saved Successfully.']);
    }


    public function update(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'footer_col_up' => 'required',
        ], [
            'name.required' => 'Name is required',
            'footer_col_up.required' => 'Footer Column is required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error','errors' => $validator->errors()]);
        }

        $footer_update = FooterMenu::find($request->footer_id);
        $footer_update->name = $request->name;

         if($request->slug)
         {
           $footer_update->slug = $request->slug;
         }else
         {
           $footer_update->slug ='#';         }


        $footer_update->route_url = $request->route;
        $footer_update->param = $request->param;
        $footer_update->column_position = $request->footer_col_up;
        $footer_update->status = $request->status;
        $footer_update->save();


        return response()->json([
            'status'=>'success',
            'message'=>'Footer menu updated successfully'
        ]);

    }

    public function menuDelete(Request $request)
    {
        $data = FooterMenu::find($request->id);
        $data->delete();
        return response()->json([
            'status'=>'success',
            'message'=>'Footer Menu Deleted Successfully'
        ]);
    }

    public function changeStatus(Request $request)
    {
        $data = FooterMenu::find($request->id);
        if($data->status==1){
            $data->status ='0';
        }else{
            $data->status ='1';
        }
        $data->save();
        return response()->json([
            'status'=>'success',
            'message'=>' Footer menu status update successfully'
        ]);
    }

}
