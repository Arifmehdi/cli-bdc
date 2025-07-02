<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MembershipType;
use App\Models\Lead;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Subscribe;
use App\Models\Notification;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\MainInventory;
use App\Models\Membership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{


    public function index()
    {
        $user = Auth::user();
        
        // Cache key for admin or dealer
        $cacheKey = 'dashboard_counts_' . ($user->hasAllaccess() ? 'admin' : 'dealer_' . $user->id);
    
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            if ($user->hasAllaccess()) {
                return [
                    'inventory' => MainInventory::where('status', 1)->count(),
                    'user' => User::count(),
                    'leads' => Lead::count(),
                    'invoice' => Invoice::where('is_cart', 1)->count(),
                ];
            } else {
                return [
                    'inventory' => MainInventory::where('deal_id', $user->id)->where('status', 1)->count(),
                    'leads' => Lead::where('dealer_id', $user->id)->count(),
                    'invoice' => Invoice::where('is_cart', 1)->where('user_id', $user->id)->count(),
                ];
            }
        });
    
        return view('backend.admin.layouts.pages.home', $data);
    }

    // public function index()
    // {

    //     $user = Auth::user();
    //     $data = [];
    //     if($user->hasAllaccess())
    //     {
    //         // $data['inventory'] = Inventory::where('status',1)->pluck('id')->count();
    //         $data['inventory'] = MainInventory::where('status',1)->pluck('id')->count();
    //         $data['user'] = User::pluck('id')->count();
    //         $data['leads'] = Lead::pluck('id')->count();
    //         $data['invoice'] = Invoice::where('is_cart',1)->pluck('id')->count();
    //     }else
    //     {
    //         // $data['inventory'] = Inventory::where('deal_id',$user->id)->where('status',1)->pluck('id')->count();
    //         $data['inventory'] = MainInventory::where('deal_id',$user->id)->where('status',1)->pluck('id')->count();
    //         $data['leads'] = Lead::where('dealer_id',$user->id)->pluck('id')->count();
    //         $data['invoice'] = Invoice::where(['is_cart' => 1,'user_id' => $user->id])->pluck('id')->count();
    //     }

    // // dd($data);
    //     return view('backend.admin.layouts.pages.home', $data);
    // }

    public function user(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        $memberships = Membership::orderBy('name')->get();
        $data = User::with('roles')->where('status', 1)->orderBy('id', 'DESC');


        if ($request->has('userRoleFilter') && $request->userRoleFilter) {
            $data->where('role_id', $request->userRoleFilter);
        }

        if ($request->has('userMembership') && $request->userMembership) {
            $data->where('membership_id', $request->userMembership);
        }

        if ($request->has('userStatusFilter') && $request->userStatusFilter) {
            $data->where('status', $request->userStatusFilter);
        }

        if ($request->ajax()) {

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })
                ->addColumn('name', function ($user) {
                    if ($user->roles->first() && $user->roles->first()->name == 'dealer') {
                        return '<a href="' . route('admin.dealer.profile', $user->id) . '" style="text-decoration:underline ">' . $user->name . '</a>';
                    } else {
                        return $user->name;
                    }
                })
                ->addColumn('phone', function ($user) {
                    return $user->formatted_phone_number;
                })
                ->addColumn('role', function ($user) {
                    return $user->roles->first() ? ucfirst($user->roles->first()->name) : 'Buyer';
                })
                ->addColumn('created_at', function ($user) {

                    return $user->created_at ? $user->created_at->format('m-d-y') : '';
                })
                ->addColumn('package', function ($row) {

                    $memberships = Membership::where('status', '1')->get();
                    if ($row->roles->first() && $row->roles->first()->name == 'dealer') {
                        $membership_html_1 = "<select name='package' id='package_" . $row->id . "' class='packages display-select form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='" . $row->id . "'>";
                        foreach ($memberships as $membership) {
                            $membership_html_1 .= "<option data-price='" . $membership->membership_price . "' value='" . $membership->id . "' " . ($row->membership_id == $membership->id ? 'selected' : '') . ">" . $membership->name . "</option>";
                        }
                        $membership_html_1 .= "</select>";
                        return $membership_html_1;
                    } else {
                        return '---';
                    }
                })->addColumn('status', function ($row) {

                    $status = "<select name='status' id='$row->id' class='status display-select form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='" . $row->id . "'>";

                    $status .= "<option value='1' " . ($row->status == '1' ? 'selected' : '') . ">Active</option>";
                    $status .= "<option value='0' " . ($row->status == '0' ? 'selected' : '') . ">Inactive</option>";

                    $status .= "</select>";
                    return $status;
                })->addColumn('action', function ($row) {
                    $html = '<a style="margin-right:10px" title="Edit" class="btn btn-info btn-sm editBtn"
                                data-id ="' . $row->id . '"
                                data-name="' . $row->name . '"
                                data-email="' . $row->email . '"
                                data-phone="' . $row->phone . '"
                                data-address="' . $row->address . '"
                                data-role="' . ($row->roles->first()->name ?? '') . '">
                                <i class="fa fa-edit"></i></a>'
                        . '<a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                data-id ="' . $row->id . '" id="user_delete"><i class="fa fa-trash"></i></a>';
                    return $html;
                })

                ->rawColumns(['action', 'role', 'name', 'created_at', 'package', 'status'])
                ->make(true);
        }

        return view('backend.admin.user.create_user', compact('roles', 'memberships'));
    }



    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_name' => 'required',
            'user_email' => 'required|email|unique:users,email',
            'user_role' => 'required|exists:roles,id',
            'user_password' => 'required|min:8',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create a new user instance
            $user = new User();
            $user->name = $request->user_name;
            $user->email = $request->user_email;
            $user->phone = $request->user_phone ?? null;
            $user->address = $request->user_address ?? null;
            $user->password = Hash::make($request->user_password);
            $user->save();

            // Assign role to the user
            $role = Role::find($request->user_role);
            // $role = Role::find(2);
            if (!$role) {
                return response()->json([
                    'status' => 'error',
                    'message' => "The specified role does not exist or is not for the 'web' guard.",
                ], 422);
            }

            // Assign the role to the user
            $user->assignRole($role);
            // Return success response with the newly created user
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user, // Return the created user's data
            ], 201); // HTTP status code 201 for created
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500); // HTTP status code 500 for internal server error
        }
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'up_user_id' => 'required|exists:users,id',
            'up_user_name' => 'required|string',
            'up_user_email' => 'required|email|unique:users,email,' . $request->up_user_id,
            'up_user_role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        // Find the user by ID
        $user = User::find($request->up_user_id);

        if ($request->hasFile('image')) {


            $path = 'frontend/assets/images/';
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Delete the old image if it exists
            if ($user->image != null) {
                $oldImagePath = public_path($path) . $user->image;
                if (file_exists($oldImagePath)) {
                    try {
                        unlink($oldImagePath);
                    } catch (\Exception $e) {
                        error_log('Error deleting old image: ' . $e->getMessage());
                    }
                }
            }

            // Move the new image to the specified path
            $image->move(public_path($path), $imageName);

            // Update the link's image attribute with the new image name
            $user->image = $imageName;
        } else {
            // If no new image is uploaded, keep the existing image name
            $user->image = $user->image;
        }
        // Update the user information
        $user->name = $request->up_user_name;
        $user->email = $request->up_user_email;
        $user->address = $request->up_user_address;
        $user->phone = $request->up_user_phone;
        $user->gender = $request->gender ?? '';
        $user->city = $request->city ?? '';
        $user->state = $request->state ?? '';
        $user->zip = $request->zip ?? '';

        // Update the password if provided
        if ($request->up_user_password != null) {

            $user->password = Hash::make($request->up_user_password);
        }

        // $user->role_id = $request->up_user_role;

        // Save the changes
        $user->save();
        $user->syncRoles($request->up_user_role);
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully'
        ]);
    }


    public function delete(Request $request)
    {
        $data = User::find($request->id);
        $data->forceDelete();
        return response()->json([
            'status' => 'success',
            'message' => 'User Delete Successfully'
        ]);
    }

    public function subscriber_show(Request $request)
    {
        if ($request->ajax()) {
            $data = Subscribe::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })

                ->addColumn('status', function ($row) {
                    $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
                })



                ->addColumn('action', function ($row) {
                    $html = '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
           id="sub_delete"><i class="fa fa-trash"></i></a>';
                    return $html;
                })



                ->rawColumns(['action', 'description', 'status'])
                ->make(true);
        }

        return view('backend/admin/subscriber/subscriber-show');
    }


    public function sub_delete(Request $request)
    {
        $data = Subscribe::find($request->id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Subscriber Deleted Successfully'
        ]);
    }

    public function statusChange(Request $request)
    {
        $data = Subscribe::find($request->id);
        if ($data->status == 1) {
            $data->status = '0';
        } else {
            $data->status = '1';
        }
        $data->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Subscriber status update successfully'
        ]);
    }

    public function changeUserStatus(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Update user status
        $user->status = $request->status;
        $user->save();

        // Update related inventories' status
        Inventory::where('deal_id', $user->id)->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'User status updated successfully'
        ]);
    }
}
