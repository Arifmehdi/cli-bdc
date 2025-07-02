<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MembershipType;
use App\Http\Controllers\Controller;
use App\Models\AdminDealerManagement;
use App\Interface\InventoryServiceInterface;
use App\Interface\UserServiceInterface;
use App\Models\Banner;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;
use Yajra\DataTables\DataTables;
use App\Interface\LeadServiceInterface;
use App\Models\DueOrder;
use App\Models\MainInventory;
use App\Models\Membership;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AdminDealerManagementController extends Controller
{

    public function __construct(private UserServiceInterface $userService,private InventoryServiceInterface $inventoryService, private LeadServiceInterface $leadService)
    {
    }


    public function index(Request $request)
    {

        if ($request->ajax()) {
            $dealer = AdminDealerManagement::latest()->get();

            return Datatables::of($dealer)

                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })->addColumn('status', function ($row) {
                    $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' name='status' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                            <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                            <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                        </select>";
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<a style="margin-right:10px" title="Edit" class="btn btn-info btn-sm editBtn"
                                    data-id ="' . $row->id . '"
                                    data-name="' . $row->name . '"
                                    data-state="' . $row->state . '"
                                    data-phone="' . $row->phone . '"
                                    data-address="' . $row->address . '"
                                    data-city="' . $row->city . '"
                                    data-zip_code="' . $row->zip_code . '"
                                    data-status="' . $row->status . '"
                                    >
                                    <i class="fa fa-edit"></i></a>'
                        . '<a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                    data-id ="' . $row->id . '" id="user_delete"><i class="fa fa-trash"></i></a>';
                    return $html;
                })

                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('backend.admin.dealer.manage-dealer');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = new AdminDealerManagement();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->status = $request->status;
        $user->zip_code = $request->zip_code;
        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Dealer Create Successfully'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'up_name' => 'required',
            'up_phone' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = AdminDealerManagement::find($request->id);
        $user->name = $request->up_name;
        $user->phone = $request->up_phone;
        $user->address = $request->up_address;
        $user->state = $request->up_state;
        $user->city = $request->up_city;
        $user->status = $request->up_status;
        $user->zip_code = $request->up_zip_code;
        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Dealer updated Successfully'
        ]);
    }


    public function delete(Request $request)
    {
        $data = AdminDealerManagement::find($request->id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Dealer Delete Successfully'
        ]);
    }

    public function changeStatus(Request $request)
    {
        $data = AdminDealerManagement::find($request->id);
        if ($data->status == 1) {
            $data->status = '0';
        } else {
            $data->status = '1';
        }
        $data->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Dealer status changed successfully'
        ]);
    }


    public function profileDetails(Request $request, $id)
    {


        $user = User::with('roles')->find($id);
        // $inventory = Inventory::query();
        $inventory = MainInventory::query();
        $data['total_inventory'] = $inventory->where('deal_id', $id)->count();
        $data['total_lead'] = Lead::where('dealer_id', $id)->count();
        $data['total_banner'] = Banner::where('user_id', $id)->count();
        $data['total_invoice'] = Invoice::with('dealer')->where('user_id',$id)->whereIn('id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                  ->from('invoices')
                  ->groupBy('generated_id');
        })->count();

        if ($request->showTrashed == 'true') {


            $info = $this->inventoryService->getTrashedItem($id);
        } else {

            $info = $this->inventoryService->getItemByUser($id);
        }


        $rowCount = $this->inventoryService->getUserByRowCount($id);
        $trashedCount = $this->inventoryService->getUserByTrashedCount($id);

        if ($request->ajax()) {

            return DataTables::of($info)->addIndexColumn()
                ->addColumn('check', function ($row) {
                    $html = '<div class="text-center ">
                            <input type="checkbox" name="admin_inventory_id[]" value="' . $row->id . '" class="mt-2 check1">
                        </div>';
                    return $html;
                })
                ->addColumn('DT_RowIndex', function ($row) {
                    return $row->id;
                })
                ->addColumn('stock', function ($row) {
                    return $row->stock ?? 'No stock';
                })
                ->addColumn('title', function ($row) {
                    return $row->year .' '. $row->make .' '. $row->model ?? 'No title';
                })
                ->addColumn('make', function ($row) {
                    return $row->make ?? 'No make';
                })
                ->addColumn('listing_start', function ($row) {
                    return  $row->created_at ? Carbon::parse($row->created_at)->format('m-d-Y') : 'null';
                })->addColumn('active_start', function ($row) {
                    return  $row->active_till ? Carbon::parse($row->active_till)->format('m-d-Y') : 'null';
                })
                ->addColumn('active_end', function ($row) {
                    return  $row->featured_till ? Carbon::parse($row->featured_till)->format('m-d-Y') : 'null';
                })
                ->addColumn('paid', function ($row) {
                    return $row->is_feature == '1' ? 'Feature' : 'Free';
                })
                ->addColumn('visibility', function ($row) {
                    $today = Carbon::now();
                    $isActive = true;

                    if ($row->active_till) {
                        if (Carbon::parse($row->active_till)->diffInMonths($today) >= 1) {
                            $isActive = false;
                        }
                    } else {
                        if ($row->created_at && Carbon::parse($row->created_at)->diffInMonths($today) >= 1) {
                            $isActive = false;
                        }
                    }
                    $status = $isActive ? 'Active' : 'Inactive';
                    $colorClass = $isActive ? 'btn badge badge-success' : 'btn badge badge-danger';
                    return "<span class='{$colorClass}'>{$status}</span>";
                    // $status = ($row->is_visibility == '1') ? 'Active' : 'Inactive';
                    // $colorClass = ($row->is_visibility == '1') ? 'btn badge badge-success' : 'btn badge badge-danger';
                    // return "<span class='{$colorClass}'>{$status}</span>";
                })
                ->addColumn('action', function ($row) {
                    if ($row->trashed()) {
                        $html = '<a href="' . route('admin.inventory.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="' . route('admin.inventory.permanent.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {
                        $html = ' <a href="' . route('admin.inventory.edit.page', $row->id) . '" style="margin-right: 6px !important" class="btn btn-success btn-sm lead_view"><i class="fa fa-eye"></i></a> ' . '<a href="' . route('admin.inventory.edit.page', $row->id) . '" class="btn btn-info btn-sm" style="margin-right: 6px !important"><i class="fa fa-edit"></i></a>' . '<a href="javascript:void(0);" class="btn btn-danger btn-sm inventory_delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                    }
                    return $html;
                })
                ->rawColumns(['visibility','action', 'status', 'check'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }
        return view('backend.admin.dealer.profile.index', compact('data', 'user','id'));
    }



    public function dealerLeadShow(Request $request, $id)
    {


        // $inventory = Inventory::where('status',1);
        $user = User::with('roles')->find($id);
        // $inventory = Inventory::query();
        $inventory = MainInventory::query();
        $data['total_inventory'] = $inventory->where('deal_id', $id)->count();
        $data['total_lead'] = Lead::where('dealer_id', $id)->count();
        $data['total_banner'] = Banner::where('user_id', $id)->count();
        $data['total_invoice'] = Invoice::with('dealer')->where('user_id',$id)->whereIn('id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                  ->from('invoices')
                  ->groupBy('generated_id');
        })->count();


        if ($request->showTrashed == 'true') {
            $info = $this->leadService->getTrashedItem($id);
        } else {
            $info = $this->leadService->getLeadByFilter($id);
        }


        $rowCount = $this->leadService->getRowCount($id);
        $trashedCount = $this->leadService->getTrashedCount($id);


        if ($request->ajax()) {
            return datatables::of($info)->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })

                ->addColumn('check', function ($row) {
                    $html = '<div class="text-center ">
                            <input type="checkbox" name="lead_id[]" value="' . $row->id . '" class="mt-2 check1 check-row">
                        </div>';
                    return $html;
                })

                ->addColumn('title', function ($row) {
                    return $row->inventory->title ?? 'No title';
                })
                ->addColumn('make', function ($row) {
                    return $row->inventory->make ?? 'No Make';
                })
                ->addColumn('dealer_name', function ($row) {
                    return $row->dealer->name ?? 'No Dealer Name';
                })
                ->addColumn('state', function ($row) {
                    return $row->dealer->state ?? 'No State';
                })
                ->addColumn('city', function ($row) {
                    return $row->dealer->city ?? 'No City';
                })

                ->addColumn('name', function ($row) {
                    return $row->customer->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->customer->email;
                })
                ->addColumn('phone', function ($row) {
                    return $row->customer->phone;
                })
                ->addColumn('status', function ($row) {
                    $html = '<p>' . ($row->status == 1 ? 'Active' : 'Inactive') . '</p>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    if ($row->trashed()) {
                        $html =
                            '<a href="' . route('admin.lead.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="' . route('admin.lead.permanent.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {
                        $html = '<a data-id="' . $row->id . '" href="javascript:void(0);" style="color:white; margin-right: 6px !important" class="btn btn-info btn-sm message_view"><i class="fas fa-comment-alt"></i></a>' .
                            '<a href="' . route('admin.single.lead.view', $row->id) . '" style="margin-right: 6px !important" class="btn btn-success btn-sm lead_view"><i class="fa fa-eye"></i></a>' .
                            '<a href="javascript:void(0);" style="margin-right: 6px !important" class="btn btn-warning btn-sm lead_send_mail" data-id="' . $row->id . '"><i class="fa fa-paper-plane"></i></a>' .
                            '<a href="javascript:void(0);" class="btn btn-danger btn-sm lead_delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                    }
                    return $html;
                })

                ->rawColumns(['action', 'status', 'check'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }

        return view('backend.admin.dealer.profile.lead',compact('data','user','id'));
    }


    private function membershipUpgradeInvoice($user,$old_package)
    {


        $member_upgrade = new DueOrder();
        $member_upgrade->user_id = $user->id;
        $member_upgrade->old_membership = $old_package;
        $member_upgrade->type = 'Membership';
        $member_upgrade->save();
        $member_upgrade->get_invoice_id();
        return $member_upgrade;


}


    public function dealarManageAjax(Request $request)
    {


        $user = $this->userService->find($request->id);
        $old_package = $user->package;
        $user->package = $request->package;
        $user->save();
        $member_upgrade = $this->membershipUpgradeInvoice($user,$old_package);
            try
            {
                $user = User::where('id',$member_upgrade->user_id)->first();

                $user->refresh();
                $memberships = Membership::all();
                $new_membership = [];
                $old_membership = [];
                foreach($memberships as $membership)
                {
                    if($membership->membership_type == $user->package)
                    {
                        $new_membership = [
                            'new_name' => $membership->name,
                            'new_price' => $membership->membership_price,
                        ];
                    }

                    if($membership->membership_type == $member_upgrade->old_membership)
                    {
                        $old_membership = [
                            'old_name' => $membership->name,
                            'old_price' => $membership->membership_price,
                        ];
                    }
                }

                $dataToCompact = [
                    'username' => $user->name ?? '',
                    'phone' => $user->phone ?? '',
                    'email' => $user->email ?? '' ,
                    'membership_type' => $new_membership['new_name'],
                    'membership_price' => $new_membership['new_price'],
                    'membership_type_old' => $old_membership['old_name'],
                    'membership_price_old' => $old_membership['old_price'],
                    'invoice_id' => $member_upgrade->invoice_id
                ];



               $pdf = PDF::loadView('backend.admin.pdf.membership_invoice',$dataToCompact);
              return $pdf->stream('invoice_' . rand(1234, 9999) . '.pdf');


            }catch(Exception $e)
            {
                // Log::error('PDF generation failed: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }

        return response()->json(['status' => 'Membership updated successfully!'], 200);
    }


    public function dealerInvoiceShow(Request $request, $id)
    {
        // $user = User::with('roles')->find($id);   // id 
        $user = User::with(['roles' => function($query) {
            $query->select('id', 'name'); // Select specific role columns if needed
        }])
        ->find($id, ['id', 'image', 'name', 'email', 'gender', 'address', 'phone', 'city', 'state', 'zip', 'created_at']);

        // $inventory = Inventory::query();
        $inventory = MainInventory::query();
        $data['total_inventory'] = $inventory->where('deal_id', $id)->count();
        $data['total_lead'] = Lead::where('dealer_id', $id)->count();
        $data['total_banner'] = Banner::where('user_id', $id)->count();
        $data['total_invoice'] = Invoice::with('dealer')->where('user_id',$id)->whereIn('id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                  ->from('invoices')
                  ->groupBy('generated_id');
        })->count();
        $data['total_trash'] = Invoice::with('dealer')->where('user_id',$id)->whereIn('id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                  ->from('invoices')
                  ->groupBy('generated_id');
        })->onlyTrashed()->count();

        $rowCount = $data['total_invoice'];
        $trashedCount = $data['total_trash'];


        if ($request->showTrashed == 'true') {
            $info = Invoice::with('dealer')->where('user_id',$id)->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('invoices')
                      ->groupBy('generated_id');
            })->select('id','generate_id','type','status','created_at')->onlyTrashed()->get();
        } else {
            $info = $this->leadService->getInvoiceByFilter($id);
        }

        if ($request->ajax()) {
            return datatables::of($info)->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($row) {
                    return $row->id; // Use any unique identifier for your rows
                })
                ->addColumn('check', function ($row) {
                    $html = '<div class="text-center ">
                            <input type="checkbox" name="invoice_id[]" value="' . $row->generated_id . '" class="mt-2 check1 check-row">
                        </div>';
                    return $html;
                })
                ->addColumn('invoice_id', function ($row) {
                    return $row->generated_id ?? '';
                })
                ->addColumn('type', function ($row) {
                    return $row->type ?? '';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('m-d-y') ?? '';
                })
                ->addColumn('payment_date', function ($row) {
                    return '---';
                })
                ->addColumn('payment_method', function ($row) {
                    return 'Cash';
                })
                ->addColumn('status', function ($row) {
                    $html = "<select class='action-add change_status " . ($row->status == 1 ? ' bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->generated_id' name='change_status'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Paid</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Pending</option>
                                </select>";
                        return $html;
                })

                ->addColumn('action', function ($row) {
                    $downloadPdfUrl = route('admin.dealer.download.membership.invoice', $row->generated_id);
                    if ($row->trashed()) {
                        $html ='<a href="'.route('admin.invoice.restore', $row->generated_id).'" class="btn btn-info btn-sm restore" data-id="' . $row->generated_id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="'.route('admin.invoice.permanent.delete',$row->generated_id).'" class="btn btn-danger btn-sm c-delete" data-id="' . $row->generated_id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {

                        $html = ' <a href="' . $downloadPdfUrl . '" title="Download PDF" target="_blank" class="download_pdf btn btn-primary btn-sm"  ><i class="fa fa-file-pdf fs-5"></i></a>&nbsp;' .' <a href="javascript:void(0);" class="btn btn-danger btn-sm invoice_delete" data-id="' . $row->generated_id . '"><i class="fa fa-trash"></i></a>';
                    }
                    return $html;
                })

                ->rawColumns(['action','invoice_id', 'status', 'type','created_at','payment_date','payment_method','check'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }

        return view('backend.admin.dealer.profile.invoice',compact('data', 'user','id'));
    }


    public function deleteInvoice(Request $request)
    {

        $data = Invoice::where('generated_id',$request->id)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Invoice Delete Successfully'
        ]);
    }


    public function addtoCartMembership(Request $request)
    {


        // Find the membership that matches the requested package type
        $membership = Membership::where('id', $request->package)->first();

        // Check if the membership was found
        if (!$membership) {
            return response()->json([
                'status' => 'error',
                'message' => 'Membership package not found!'
            ]);

        }

        // Create a new invoice
        $existingInvoice = Invoice::where('is_cart', '0')->where('type','Membership')->first();

        if ($existingInvoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only one membership can be invoiced at a time.',
            ]);
        }
        $lead_id = $request->lead_id ?? null;
        $invoice = new Invoice();
        $invoice->user_id = $request->id;
        $invoice->lead_id = $lead_id;
        $invoice->type = 'Membership';
        $invoice->package = $request->package;
        $invoice->price = $membership->membership_price;
        $invoice->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Added to cart successfully!'
        ]);
    }

}
