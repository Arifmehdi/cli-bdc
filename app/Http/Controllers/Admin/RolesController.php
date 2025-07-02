<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $All_permissions = Permission::all();

        $group_permissions = User::group_byName();

        if ($request->ajax()) {

            $data = Role::with('permissions')->orderBy('name', 'asc')->get();

            return Datatables::of($data)

                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($data) {

                    return $data->id;
                    // return $roles->id; // Use any unique identifier for your rows
                })
                ->addColumn('role', function ($data) {
                   return ucfirst($data->name);

                })
                ->addColumn('permission', function ($data) {
                    $html = '<a href="#" style="font-weight: bold; font-size:20px; text-align:center" class="mb-2 view_permission" data-role-id="' . $data->id . '"><i class="fa fa-eye"></i> </a>
                    <div style="display: none" id="show_permission_' . $data->id . '">';

                    foreach ($data->permissions as $permission) {
                        $html .= '<button class="btn btn-info btn-sm mb-1"> ' . $permission->name . '</button>' . ' ';
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<a href="' . route('admin.roles.edit', $row->id) . '" class="btn btn-info btn-sm mb-1"><i class="fa fa-edit"></i></a> ' .
                            ' <a href="#" class="btn btn-danger btn-sm mb-1"><i class="fa fa-trash"></i></a>';
                            // '<a href="' . route('admin.roles.destroy', $row->id) . '" class="btn btn-danger btn-sm mb-1"><i class="fa fa-trash"></i></a>';
                    return $html;
                })
                ->rawColumns(['role','permission','action'])
                ->make(true);
        }
        return view('backend.admin.user.permission.create_role',compact('All_permissions','group_permissions'));
        // return view('backend.admin.user.permission.create_role');
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
        //  return $request->all();
        //validate input
        $request->validate([
            'rolename'=> 'required:unique:roles,name'
         ]);
         $role = $request->rolename;
         $permissions = $request->permissions;
         $role = Role::create(['name' => $role]);
         if (!empty($permissions))
         {
             $role->syncPermissions($permissions);
         }

       return response()->json(['message' => 'Role create successfully!']);
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
        $role = Role::findById($id);
        //all Permission show
        $All_permissions = Permission::all();
        //all GroupName show
        $group_permissions = User::group_byName();
        return view('backend.admin.user.permission.edit_role',compact('role','All_permissions','group_permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(),[
            'rolename'=> 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $permissions = $request->permissions;
        $role = Role::findById($id);
        $role->update(['name'=> $request->rolename,'guard_name'=>'web']);
        if (!empty($permissions))
        {
            $role->syncPermissions($permissions);
        }

        return response()->json(['message' => 'Role permission update successfully! ']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // $role = Role::findById($id);
        // dd($role);
    }


    public function permissionStore(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(),[
            'permission_group_name'=> 'required|unique:permissions,group_name',
            'permission_name'=> 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $group_name = $request->permission_group_name;
        $permissions = $request->permission_name;
        foreach ($permissions as $permission)
        {
            $this->permmission($group_name,$permission);
        }
        return response()->json(['message' => 'Permission Save Successfully !']);
    }

    public function permmission($group_name,$permission)
    {
        $permission_store = new Permission();
        $permission_store->name = $permission;
        $permission_store->guard_name = 'web';
        $permission_store->group_name = $group_name;
        $permission_store->save();
    }


    public function permissionList(Request $request)
    {

        if ($request->ajax()) {
            $data = Permission::all();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($data) {
                    return $data->id;
                })
                ->addColumn('group_name', function ($data) {
                    return $data->group_name;
                })
                ->addColumn('permission', function ($data) {
                    return $data->name;
                })
                ->addColumn('action', function ($row) {
                    $html = '<a href="javascript:void(0);" data-id="' . $row->id . '" class="btn btn-info btn-sm editPermissionBtn"><i class="fa fa-edit"></i></a>' .
                            '<form id="btndelete' . $row->id . '" action="' . route('admin.permission.destroy', $row->id) . '" method="POST" style="display:inline">' .
                                csrf_field() .
                                '<a href="javascript:void(0);" class="btn btn-danger btn-sm" id="' . $row->id . '" onclick="btnPermissionDelete(this.id)"><i class="fa fa-trash"></i></a>' .
                            '</form>';
                    return $html;
                })

                ->rawColumns(['group_name','permission','action'])
                ->make(true);
        }

        return view('backend.admin.user.permission.permission_list');

    }

    public function permissionUpdate(Request $request)
    {

        $permission_id = $request->permission_group_id;
        $permission = Permission::findorFail($permission_id);
        $permission->name = $request->permission_name;
        $permission->group_name = $request->permission_group_name;
        $permission->save();
        return redirect()->back()->with('message','permission update successfully!');
    }

    public function permissionDelete($id)
    {
        $permission = Permission::find($id);
        $permission->delete();
        return redirect()->back()->with('message','permission delete successfully !');
    }

}
