<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DueOrder;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\MainInventory;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{


    public function InvoiceShow(Request $request)
    {

        $invoices = Invoice::with('lead','inventories','dealer')->whereIn('id',$request->ids)->get();
        //   dd($invoices);
        return view('backend.admin.cart.cart', compact('invoices'));
    }
    public function show(Request $request)
    {

        $ids = $request->input('ids', []);
        $inventory_ids = $request->input('inventory_ids', []);
        $inventories = Inventory::whereIn('id',$inventory_ids)->get();
        $invoices = Invoice::whereIn('id',$ids)->get();
        $user = $request->input('dealer-info');
        // Store the IDs in the session
        Session::put('invoice_data', [
          'invoices' => $invoices,
          'inventory_id' =>$inventory_ids,
          'ids' =>$ids,
        ]);
        $userInfo = [];
        $userInfo = User::where('id', $user)->first();
        return view('backend.admin.invoice.invoice-show', compact('userInfo','inventories'));
    }

    public function invoiceNewStore(Request $request)
    {
        try {



            $ids = $request->invoiceData['ids'] ?? '';
            $inventoryIds = $request->invoiceData['inventory_id'] ?? '';

            $user_id = $request->user_id ?? '';
            $total = str_replace('$', '', $request->total);
            $subtotal = str_replace('$', '',  $request->subtotal);

            $total = (float)$total;
            $subtotal = (float)$subtotal;
            if (!empty($ids)) {

                if (!is_array($ids)) {
                    $ids = [$ids]; // Wrap single ID in an array
                }
                $invoices = Invoice::whereIn('id', $ids)->get();
                $generated_id = generateNewInvoiceId();

                if($request->type == 'Membership')
                {
                    $user = User::find($user_id);
                    $old_package = $user->membership_id;
                    $user->membership_id = $request->invoiceData['invoices'][0]['package'];
                    $user->save();

                    foreach ($invoices as $invoice) {
                        $invoice->old_membership =$old_package;
                        $invoice->user_id = $request->user_id;
                        $invoice->subtotal = $subtotal;
                        $invoice->total = $total;
                        $invoice->discount = $request->discount;
                        $invoice->type = $request->type;
                        $invoice->cost = $request->cost;
                        $invoice->is_cart = '1';
                        $invoice->total_count = $request->total_count;
                        $invoice->generated_id = $generated_id;
                        $invoice->save();
                    }
                }


                if($request->type == 'Lead')
                {
                    foreach($invoices as $invoice)
                    {

                        $invoice->user_id = $request->user_id;
                        $invoice->subtotal = $subtotal;
                        $invoice->total = $total;
                        $invoice->discount = $request->discount;
                        $invoice->type = $request->type;
                        $invoice->cost = $request->cost;
                        $invoice->is_cart = '1';
                        $invoice->total_count = $request->total_count;
                        $invoice->generated_id = $generated_id;
                        $invoice->save();

                        if (!empty($inventoryIds)) {
                            // Attach inventories without detaching existing relationships
                            $invoice->inventories()->syncWithoutDetaching($inventoryIds);
                        }

                    }
                }

                if($request->type == 'Listing')
                {
                    foreach($invoices as $invoice)
                    {

                        $invoice->user_id = $request->user_id;
                        $invoice->subtotal = $subtotal;
                        $invoice->total = $total;
                        $invoice->discount = $request->discount;
                        $invoice->type = $request->type;
                        $invoice->cost = $request->cost;
                        $invoice->is_cart = '1';
                        $invoice->total_count = $request->total_count;
                        $invoice->generated_id = $generated_id;
                        $invoice->save();

                        if (!empty($inventoryIds)) {
                            // Attach inventories without detaching existing relationships
                            $invoice->inventories()->syncWithoutDetaching($inventoryIds);
                        }


                    }
                }


            }

            if($request->type == 'Membership')
            {
                $downloadPdfUrl = route('admin.dealer.download.membership.invoice',['id' => $generated_id]);
            }
            else
            {

                $downloadPdfUrl = route('admin.invoice.pdf', ['id' => $generated_id]);
            }

            $response = [
                'status' => Auth::user()->hasRole('dealer') ? 'desuccess' : 'success',
                'message' => 'Invoice created successfully',
                'download_url' => $downloadPdfUrl,
                'user_id' => $request->user_id,
            ];
            return response()->json($response);

        }catch(Exception $e){
            Log::error('Error in DueOrder creation: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function allInvoice(Request $request, $id = null)
    {


        if($request->ajax()){

            $data = Invoice::with('dealer')->select('invoices.*')->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('invoices')
                      ->groupBy('generated_id');
            })->get();



            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($row) {
                    return $row->id; // Use any unique identifier for your rows
                })
                ->editColumn('invoiceNo', function ($row) {
                    return $row->generated_id;
                })
                ->addColumn('dealer_name', function ($row) {
                    return isset($row->dealer) ? explode(' in ', $row->dealer->name)[0] : 'Null';
                })->addColumn('type', function ($row) {
                    return  $row->type;
                })
                ->addColumn('Payment method', function ($row) {
                    return '<p>Cash</p>';
                })
                ->addColumn('Payment date', function ($row) {
                    $paymentDate = Carbon::parse($row->updated_at)->format('m-d-Y');
                    return '<p>' . $paymentDate . '</p>';
                })->addColumn('Status', function ($row) {
                    $html = "<select class='action-select change_status " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->generated_id' name='change_status'>
                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Paid</option>
                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Pending</option>
                </select>";
                return $html;
                })
                ->addColumn('action', function ($row) {
                    $downloadPdfUrl = route('admin.dealer.download.membership.invoice', $row->generated_id);
                    $html = '<a href="' . $downloadPdfUrl . '" title="Download PDF" target="_blank" class="download_pdf btn btn-success btn-sm"  data-id="' . $row->generated_id . '" data-type="' . $row->type . '"><i class="fa fa-file-pdf fs-5"></i></a>&nbsp;'.
                    '<a href="#" data-id="'.$row->generated_id.'"  class="btn btn-sm delete_two text-white btn-danger invoice-delete"  title="Delete"> <i class="fa fa-trash"></i> </a> &nbsp;';
                    return $html;
                })
                ->rawColumns(['dealer_name','type','action','Payment method', 'Payment date','Status'])
                ->make(true);
        }

        return view('backend.admin.invoice.invoice-list');
    }

    public function invoicePdf($id)
    {

        $invoice =  Invoice::where('generated_id',$id)->first();
       try
       {
           $user = User::where('id',$invoice->user_id)->first();

           if($invoice->type == 'Membership')
           {
            $memberships = Membership::all();
            $new_membership = [];
            $old_membership = [];
            foreach($memberships as $membership)
            {
                if($membership->id == $user->membership_id)
                {
                    $new_membership = [
                        'new_name' => $membership->name,
                        'new_price' => $membership->membership_price,
                    ];
                }
                if($membership->id == $invoice->old_membership)
                {
                    $old_membership = [
                        'old_name' => $membership->name,
                        'old_price' => $membership->membership_price,
                    ];
                }
            }
            $name = Str::before($user->name, 'in');
            $dataToCompact = [
                'username' => $name ?? '',
                'phone' => $user->phone ?? '',
                'email' => $user->email ?? '' ,
                'address' => $user->address ?? '' ,
                'membership_type' => $new_membership['new_name'],
                'membership_price' => $new_membership['new_price'],
                'membership_type_old' => $old_membership['old_name'],
                'membership_price_old' => $old_membership['old_price'],
                'invoice_id' => $invoice->generated_id,
                'invoice' => $invoice,
            ];

           $pdf = PDF::loadView('backend.admin.pdf.membership_invoice',$dataToCompact);
            // return $pdf->stream('invoice_' . rand(1234, 9999) . '.pdf');
           }

           if($invoice->type == 'Lead')
           {
            $dataToCompact = Invoice::with('inventories')->where('generated_id',$id)->first();
            $user_info = User::where('id',$dataToCompact->user_id)->first();
            $pdf = PDF::loadView('backend.admin.pdf.invoice-download', compact('dataToCompact', 'user_info'));

           }

           if($invoice->type == 'Listing')
           {
            $dataToCompact = Invoice::with('inventories')->where('generated_id',$id)->first();
            $user_info = User::where('id',$dataToCompact->user_id)->first();
            $pdf = PDF::loadView('backend.admin.pdf.listing-invoice-download', compact('dataToCompact', 'user_info'));

           }

           return $pdf->stream('invoice_' .$id . '.pdf');

       }catch(Exception $e)
       {
           // Log::error('PDF generation failed: ' . $e->getMessage());
           return response()->json(['error' => $e->getMessage()], 500);
       }


    }

    public function invoiceDelete(Request $request){

        Invoice::where('generated_id', $request->id)->delete();
        return response()->json([
            'status'=>'success',
            'message'=>'Invoice Deleted Successfully'
        ]);
    }

    public function changeStatus(Request $request)
    {

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the invoice record
            $invoice = DueOrder::find($request->id);

            // Find the inventory records
            $inventory_ids = explode(",", $request->inventory_id);
            $inventory = Inventory::whereIn('id', $inventory_ids)->get();

            // Check if the invoice and inventory exist
            if (!$invoice || $inventory->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Invoice or Inventory not found'], 404);
            }

            // Update the status based on the current value
            if ($invoice->status == '1') {
                $invoice->status = '0';
                foreach ($inventory as $item) {
                    $item->is_lead_feature = '0';
                    $item->save();
                }
            } else {
                $invoice->status = '1';
                foreach ($inventory as $item) {
                    $item->is_lead_feature = '1';
                    $item->save();
                }
            }

            // Save the updated invoice record
            $invoice->save();

            // Commit the transaction
            DB::commit();

            // Return a success response
            return response()->json(['status' => 'success', 'message' => 'Status Updated']);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Return an error response
            return response()->json(['status' => 'error', 'message' => 'An error occurred while updating status', 'error' => $e->getMessage()], 500);
        }
    }


    public function membershipInvoiceDownload($id)
    {
       $invoice =  Invoice::where('generated_id',$id)->select('id','total','subtotal','user_id','type','old_membership','generated_id','discount','cost')->first();

       try
       {
           $user = User::where('id',$invoice->user_id)->select('id','name','phone','email','address','membership_id')->first();

           if($invoice->type == 'Membership')
           {
            $memberships = Membership::select('id','name','type','membership_price')->get();

            $new_membership = [];
            $old_membership = [];
            foreach($memberships as $membership)
            {
                if($membership->id == $user->membership_id)
                {
                    $new_membership = [
                        'new_name' => $membership->name,
                        'new_price' => $membership->membership_price,
                    ];
                }
                if($membership->id == $invoice->old_membership)
                {
                    $old_membership = [
                        'old_name' => $membership->name,
                        'old_price' => $membership->membership_price,
                    ];
                }
            }

            $name = Str::before($user->name, 'in');

            $dataToCompact = [
                'username' => $name ?? '',
                'phone' => $user->phone ?? '',
                'email' => $user->email ?? '' ,
                'address' => $user->address ?? '' ,
                'membership_type' => $new_membership['new_name'],
                'membership_price' => $new_membership['new_price'],
                'membership_type_old' => $old_membership['old_name'],
                'membership_price_old' => $old_membership['old_price'],
                'invoice_id' => $invoice->generated_id,
                'invoice' => $invoice,
            ];
            
           $pdf = PDF::loadView('backend.admin.pdf.membership_invoice',$dataToCompact)->setPaper('a4')
           ->setOption('isHtml5ParserEnabled', true)
           ->setOption('isRemoteEnabled', false);
           
            // return $pdf->stream('invoice_' . rand(1234, 9999) . '.pdf');
           }

           if($invoice->type == 'Lead')
           {
            // $dataToCompact = Invoice::with('inventories')->where('generated_id',$id)->first();
            $dataToCompact = Invoice::with('mainInventories')->where('generated_id',$id)->first();
            $user_info = User::where('id',$dataToCompact->user_id)->first();
            $pdf = PDF::loadView('backend.admin.pdf.invoice-download', compact('dataToCompact', 'user_info'));

           }
           if($invoice->type == 'Listing')
           {
            // $dataToCompact = Invoice::with('inventories')->where('generated_id',$id)->first();
            $dataToCompact = Invoice::with('mainInventories')->where('generated_id',$id)->first();
            $user_info = User::where('id',$dataToCompact->user_id)->first();
            $pdf = PDF::loadView('backend.admin.pdf.listing-invoice-download', compact('dataToCompact', 'user_info'));

           }

           return $pdf->stream('invoice_' .$id . '.pdf');

       }catch(Exception $e)
       {
           // Log::error('PDF generation failed: ' . $e->getMessage());
           return response()->json(['error' => $e->getMessage()], 500);
       }

    }

    public function membershipInvoiceStatusChange(Request $request)
    {
        DB::beginTransaction();
        try {
                $invoices = Invoice::with('mainInventories')->where('generated_id',$request->id)->select('type','status','lead_id','user_id')->get();

                foreach ($invoices as $invoice) {
                //Lead logic check
                    if ($invoice->type == 'Lead') {
                        $invoice->status = $request->status;
                        $invoice->save();

                        // Update lead status
                        $lead = Lead::find($invoice->lead_id);
                        if ($lead) {
                            $lead->invoice_status = $request->status;
                            $lead->save();
                        }
                    }

                    //membership logic check
                    if($invoice->type == 'Membership')
                    {
                        // $inventories = Inventory::where('deal_id',$invoice->user_id)->get();
                        $inventories = MainInventory::where('deal_id',$invoice->user_id)->select('id','is_feature','active_till','featured_till','status')->get();

                            foreach ($inventories as $inventory) {
                                if ($request->status == '1') {
                                    $inventory->is_feature = '1';
                                    $inventory->active_till = now();
                                    $inventory->featured_till = Carbon::now()->addDays(30);
                                    $invoice->status = '1';
                                } else {
                                    $inventory->is_feature = '0';
                                    $inventory->active_till = null;
                                    $inventory->featured_till = null;
                                    $invoice->status = '0';
                                }
                                $inventory->save();

                            }
                            $invoice->save();
                        }
                        //Listing logic check
                        if ($invoice->type == 'Listing') {
                            $invoice->status = $request->status;
                            $invoice->save();
                            foreach($invoice->mainInventories as $inventory)
                            {
                            // $inventory = Inventory::find($inventory->id);
                            $inventory = MainInventory::find($inventory->id);
                            if ($request->status == '1') {
                                $inventory->is_feature = '1';
                                $inventory->active_till = now();
                                $inventory->featured_till = Carbon::now()->addDays(30);
                                } else {
                                    $inventory->is_feature = '0';
                                    $inventory->active_till = null;
                                    $inventory->featured_till = null;
                                }
                                $inventory->save();
                            }
                        }
                }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Status changed successfully!'], 200);
        } catch (\Exception $e) {
            // Rollback on failure
            DB::rollBack();
            return response()->json('message',$e);
            return response()->json(['status' => 'error', 'message' => 'Failed to change status!'], 500);
        }
    }



    public function bulkAction(Request $request)
    {
        if (isset($request->invoice_id)) {
            if ($request->action_type == 'move_to_trash') {

                Invoice::whereIn('generated_id', $request->invoice_id)->delete();
                return response()->json([
                    'status'=>'success',
                    'message'=>'Invoice Deleted Successfully'
                ]);

            }elseif ($request->action_type == 'restore_from_trash') {

                Invoice::withTrashed()->whereIn('generated_id', $request->invoice_id)->restore();
                return response()->json('Invoice are restored successfully');

            } elseif ($request->action_type == 'delete_permanently') {

                Invoice::onlyTrashed()->whereIn('generated_id', $request->invoice_id)->forceDelete();
                return response()->json('Invoice are permanently deleted successfully');
            } else {
                return response()->json('Action is not specified.');
            }
        } else {
            return response()->json(['message' => 'No Item is Selected.'], 401);
        }
    }


    public function restore($id)
    {

        try
        {
            Invoice::withTrashed()->where('generated_id', $id)->restore();
            return response()->json('Invoice restored successfully');
        }catch(Exception $e)
        {
            return response()->json($e);
        }

    }

    public function permanentDelete($id)
    {
        Invoice::onlyTrashed()->where('generated_id', $id)->forceDelete();
        return response()->json('Invoice deleted successfully');
    }

}
