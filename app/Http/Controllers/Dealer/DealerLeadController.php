<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Interface\LeadServiceInterface;
use App\Models\DueOrder;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DealerLeadController extends Controller
{
    public function __construct(private LeadServiceInterface $leadService){}

    public function checkInvoiceStatus($userId,$leadId =null)
    {


        $membershipCheck = Invoice::where(['type'=>'Membership','user_id'=> $userId])->orderBy('generated_id','desc')->first();

       if($membershipCheck)
       {
        if ($membershipCheck->status == 1 && now()->lt($membershipCheck->updated_at->addMonth())) {
            return true;

        }
       }

        if($leadId)
        {
            $invoice = Invoice::where('lead_id',$leadId)->first();
            if(!$invoice)
            {
                return false;

            }
        }
        $invoices = Invoice::where(function ($query) {
            $query->where('type', 'Lead')
                ->orWhere('type', 'Listing');
        })->where('user_id', $userId)->get();


        foreach ($invoices as $invoice) {
            if ($invoice->status == 1 && now()->lt($invoice->updated_at->addMonth())) {
                return true;
            }
        }
        return false;
    }


    public function leadShow(Request $request)
  {

    // $inventory = Inventory::where('status',1);
    $memberships = Membership::where('status', '1')
    ->offset(1)
    ->limit(PHP_INT_MAX)
    ->get();
    $id = Auth::id();
    $inventory = Inventory::query();
    // $inventory = Inventory::query();
    $data['inventory_make'] = $inventory->where('deal_id',$id)->distinct('make')->pluck('id','make')->toArray();
    $data['inventory_dealer_name'] = User::where('id',$id)->pluck('id','name')->toArray();
    $data['inventory_dealer_city'] = User::where('id',$id)->whereNotNull('city')
    ->where('city', '!=', '')->pluck('id','city')->toArray();
    $data['inventory_dealer_state'] = User::where('id',$id)->whereNotNull('state')
    ->where('state', '!=', '')->pluck('id','state')->toArray();
    ksort($data['inventory_make']);
    ksort($data['inventory_dealer_name']);
    ksort($data['inventory_dealer_city']);
    ksort($data['inventory_dealer_state']);


    if ($request->showTrashed == 'true') {
        $info = $this->leadService->getTrashedItem();
    } else {
        $info = $this->leadService->getUserItemByFilter($id,$request);

    }

    $rowCount = $this->leadService->getUserRowCount($id);
    $trashedCount = $this->leadService->getUserTrashedCount($id);

    if($request->ajax()){

        return datatables::of($info)->addIndexColumn()
        ->addColumn('DT_RowIndex', function ($user) {
            return $user->id; // Use any unique identifier for your rows
        })

        ->addColumn('check', function ($row) {
            $html = '<div class=" text-center">
            <input type="checkbox" name="lead_id[]" value="' . $row->id . '" class="mt-2 check1 check-row" id="selected_item">
            <input type="hidden" name="dealer_id[]" value="' . $row->dealer_id . '" class="mt-2 check1 check-row">

        </div>';
            return $html;
        })

        ->addColumn('title', function($row){
            return $row->mainInventory->title ?? 'No title';
        })->addColumn('stock', function($row){
            return $row->mainInventory->stock ?? 'Null';
        })
        ->addColumn('make', function($row){
            return $row->mainInventory->make ?? 'No Make';
        })
        ->addColumn('dealer_name', function($row){
            return $row->dealer->name ?? 'No Dealer Name';
        })
        ->addColumn('state', function($row){
            return $row->dealer->state ?? 'No State';
        })
        ->addColumn('city', function($row){
            return $row->dealer->city ?? 'No City';
        })

        ->addColumn('name', function ($row) use ($id) {

            $isShow = $this->checkInvoiceStatus($id,$row->id);
            if ($isShow) {
                return $row->customer->name ?? '[Null]';
            } else {
                return '[Purchase/upgrade]';
            }
        })
        ->addColumn('email', function ($row) use ($id) {
            $isShow = $this->checkInvoiceStatus($id,$row->id);

            if ($isShow) {
                return $row->customer->email ?? '[Null]';
            } else {
                return '[Purchase/upgrade]';
            }
        })
        ->addColumn('phone', function ($row) use ($id) {
            $isShow = $this->checkInvoiceStatus($id,$row->id);
            if ($isShow) {
                return $row->customer->phone ?? '[Null]';
            } else {
                return '[Purchase/upgrade]';
            }
        })
        ->addColumn('status', function($row){
             $html = '<p>' .($row-> status==1? 'Active': 'Inactive'). '</p>';
        //     $html = '<div class="form-check form-switch">
        //     <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
        //     <label class="form-check-label" for="flexSwitchCheckDefault">Default switch checkbox input</label>
        //   </div>';
            return $html;
        })
        ->addColumn('action', function($row) use ($id) {
            $isShow = $this->checkInvoiceStatus($id);
            if ($row->trashed()) {
                $html ='<a href="'.route('dealer.lead.restore', $row->id).'" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                    '<a href="'.route('dealer.lead.permanent.delete', $row->id).'" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
            }else
            {
                if($isShow)
                {
                    $html = '<a href="' . route('dealer.single.lead.view', $row->id) . '" style="margin-right: 6px !important" class="btn btn-success btn-sm lead_view"><i class="fa fa-eye"></i></a>'.'<a data-id="' . $row->id . '" href="javascript:void(0);" style="color:white; margin-right: 6px !important" class="btn btn-info btn-sm message_view"><i class="fas fa-comment-alt"></i></a>';
                }
                if($isShow)
                {
                    $html = '<a href="' . route('dealer.single.lead.view', $row->id) . '" style="margin-right: 6px !important" class="btn btn-success btn-sm lead_view"><i class="fa fa-eye"></i></a>'.'<a data-id="' . $row->id . '" href="javascript:void(0);" style="color:white; margin-right: 6px !important" class="btn btn-info btn-sm message_view"><i class="fas fa-comment-alt"></i></a>';
                }
                else{
                    $html ='<a href="javascript:void(0)" title="Purchase" class="btn btn-dark btn-sm purchase" data-row_id="' . $row->id . '" ><i class="fa fa-cart-plus"></i></a>';
                }

                $html .= ' <a title="Delete" href="javascript:void(0)" class="text-white btn btn-sm  btn-danger lead_delete" data-id ="'.$row->id.'"><i class="fa fa-trash"></i></a>';
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
    return view('backend.dealer.lead.lead-show',compact('memberships'),$data);
  }


public function checkPrice($request)
{

    $membership = Membership::where('id',$request->membership_id)->first();
    switch ($request->user_click) {
        case 'Lead':
            return $membership->membership_price;
        case 'Listing':
            return $membership->membership_price;
        default:
            return 0;
    }


}

    public function addCart(Request $request)
    {


        try {


            $leadIds = $request->lead_id;
            $user_click = $request->user_click;
            $selectedData = explode(',',$leadIds[0]);
            $existingInvoices = Invoice::whereIn("lead_id", $selectedData)->get();
            $existingDataIds = $existingInvoices->pluck("lead_id")->toArray();

            // new array data collected
            $newData = array_diff($selectedData, $existingDataIds);


            if($user_click == 'Membership')
            {

                $membership = Membership::where('id', $request->membership_id)->first();
                // Check if the membership was found
                if (!$membership) {
                    return redirect()->back()->with('status', 'error')->with('message', 'Membership package not found!');

                }
                // Create a new invoice
                $existingInvoice = Invoice::where('status', '0')->where(['type'=>'Membership','user_id'=>Auth::id()])->first();

                if ($existingInvoice) {

                    return redirect()->back()->with('status', 'error')->with('message', 'Only one membership can be invoiced at a time.');

                }
                $invoice = new Invoice();
                $invoice->user_id = Auth::id();
                $invoice->type = $user_click;
                $invoice->package = $membership->id;
                $invoice->price = $membership->membership_price;
                $invoice->created_at = now();
                $invoice->updated_at = now();
                $invoice->save();
                return redirect()->back()->with('status', 'success')->with('message', 'Added to cart successfully!');


            }elseif(!empty($newData))
            {
                $invoicesToInsert = [];

                foreach ($newData as $id) { // Change $ids to $newData
                    // Assuming $price and $package are coming from somewhere

                    $price = $this->checkPrice($request);

                    $invoicesToInsert[] = [
                        'lead_id' => $id,
                        'price' => $price, // Assuming 'price' is a key in your request
                        'user_id' => Auth::id(),
                        'type' => $user_click,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                Invoice::insert($invoicesToInsert);
                return redirect()->back()->with('status', 'success')->with('message', 'Add to cart Successfully!');
            }
           else {
                return redirect()->back()->with('status', 'error')->with('message', 'All data already checked');

            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


    }







  public function purchase(Request $request)
  {
    $leadIds = $request->lead_id;
    $item = explode(',',$leadIds[0]);
    $invoices = Lead::with('inventory')->whereIn('id',$item)->get();
    $user_click = $request->user_click;
    if($user_click == 'lead')
    {
        // $data['details'] = 'lead';
        $data['price'] = $request->lead_price;
        return view('backend.dealer.cart.cart', compact('invoices','data'));
    }
    elseif($user_click == 'membership')
    {
        return $user_click;
    }
    else
    {
        return $user_click;
    }
    return view('backend.admin.cart.cart', compact('invoices'));
  }

  public function invoiceshow(Request $request)
  {

    if($request->ajax()){
        $data = Invoice::with('dealer')->where('user_id',Auth::id())->whereIn('id', function($query) {
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
            ->addColumn('type', function ($row) {
                return  $row->type;
            })
            ->addColumn('Payment method', function ($row) {
                return '<p>Cash</p>';
            })
            ->addColumn('created_at', function ($row) {
                $paymentDate = Carbon::parse($row->updated_at)->format('m-d-Y');
                return '<p>' . $paymentDate . '</p>';
            })->addColumn('status', function ($row) {
                $status = '<p>' . ($row->status == '0' ? 'Pending' : 'Paid') . '</p>';
                return '<p>' . $status . '</p>';
            })
            ->addColumn('action', function ($row) {
                $downloadPdfUrl = route('admin.invoice.pdf', ['id' => $row->generated_id]);
                $html = '<a href="' . $downloadPdfUrl . '" title="Download PDF" target="_blank" class="btn btn-sm btn-primary  download_pdf"  data-id="' . $row->generated_id . '" data-type="' . $row->type . '"><i class="fa fa-file-pdf fs-5"></i></a>&nbsp;';
                if ($row->status == '0') {
                    $html .= '<a href="#" data-id="' . $row->generated_id . '" class="btn btn-sm delete_two text-white btn-danger invoice-delete" title="Delete"><i class="fa fa-trash"></i></a>&nbsp;';
                }
                return $html;
            })
            ->rawColumns(['type','action','Payment method', 'created_at','status'])
            ->make(true);
    }
    return view('backend.dealer.invoice.invoice-view');
  }
}
