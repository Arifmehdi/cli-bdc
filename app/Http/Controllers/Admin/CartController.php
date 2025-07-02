<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function getcartItem()
    {
    // $today = Carbon::now()->toDateString();
    $authUser = Auth::user();
    if($authUser->hasAllaccess())
    {
        $invoices = Invoice::with('lead','membership')
        ->where('is_cart',0)
        ->latest()
        ->get();


    }else
    {
        $invoices = Invoice::with('lead','membership')->where('user_id', Auth::id())
        ->where('is_cart',0)
        ->latest()
        ->get();
    }


    $html = '';
    if ($invoices) {
        $html .= '<form action="' . route('admin.invoice.show') . '" method="GET" id="invoice_form_submit">';
        $html .= csrf_field();
        $html .='<table class="table">';
        $html .='<tbody>';

        foreach($invoices as $invoice) {
            $html .= '<tr style="">';
            if ($invoice->lead_id) {
                $lead = $invoice->lead;

                if ($lead && $lead->inventory) {
                    $inventory = $lead->inventory;

                    // Extract image URL
                    $image_obj = $inventory->local_img_url;
                    $image_splice = explode(',', $image_obj);
                    $image = str_replace(["[", "'"], "", $image_splice[0]);
                    $image = trim($image);
                    $image_url = asset('frontend/' . $image);

                    $html .= '<td style="padding: 10px; font-size:14px"><img src="' . $image_url . '" alt="Image" style="width:70px; height:50px;"></td>';
                    $html .= '<td style="padding: 10px; font-size:14px">' . $inventory->title . '</td>';
                    $html .= '<td style="padding: 5px"><input type="hidden" name="inventory_ids[]" value="' . $invoice->lead_id . '"></td>';
                    $html .= '<td style="padding: 5px"><input type="hidden" name="ids[]" value="' . $invoice->id . '"></td>';
                    $html .= '<td style="padding: 5px"><input type="hidden" name="dealer-info" value="' . $inventory->user_id . '"></td>';
                    $html .= '<td style="padding: 5px"><a href="#" class="deleteCart" data-id="' . $invoice->id . '"><i class="fa fa-trash mt-2"></i></a></td>';
                } else {
                    // Handle the case where lead or inventory is null
                    $html .= '<td colspan="5" style="padding: 10px; font-size:14px">Inventory details not available.</td>';
                }
            }elseif($invoice->type =='Listing')
            {
                foreach($invoice->inventories as $inventory)
                {
                    $html .= '<tr>'; // Start a new row for each inventory

                    $image_obj = $inventory->local_img_url;
                    $image_splice = explode(',', $image_obj);
                    $image = str_replace(["[", "'"], "", $image_splice[0]);
                    $image = trim($image);
                    $image_url = asset('frontend/' . $image);

                    $html .= '<td style="padding: 10px; font-size:14px"><img src="' . $image_url . '" alt="Image" style="width:70px; height:50px;"></td>';
                    $html .= '<td style="padding: 10px; font-size:14px">' . $inventory->title . '</td>';
                    $html .= '<td style="padding: 5px"><input type="hidden" name="inventory_ids[]" value="' . $inventory->id . '"></td>';
                    $html .= '<td style="padding: 5px"><input type="hidden" name="ids[]" value="' . $invoice->id . '"></td>';
                    $html .= '<td style="padding: 5px"><input type="hidden" name="dealer-info" value="' . $invoice->user_id . '"></td>';
                    $html .= '<td style="padding: 5px"><a href="#" class="deleteCart" data-id="' . $inventory->id . '" data-invoice_id="' . $invoice->id . '" data-type="' . $invoice->type . '"><i class="fa fa-trash mt-2"></i></a></td>';

                    $html .= '</tr>'; // Close the row
                }

            }
             // Display Membership Details
             elseif ($invoice->type == 'Membership') { // Assuming 'membership' relationship exists

                $html .= '<tr>';
                $html .= '<td style="padding: 15px; font-size:14px; text-align:left; color:#555;">';
                $html .= '<strong>Name:</strong> ' . $invoice->membership->name . '<br>';
                $html .= '<strong>Price:</strong> $' . number_format($invoice->membership->membership_price, 2) . '<br>';
                $html .= '</td>';

                // Hidden inputs
                $html .= '<td style="padding: 10px; text-align:center;">';
                $html .= '<input type="hidden" name="user_id" value="' . $invoice->user_id . '">';
                $html .= '<input type="hidden" name="package" value="' . $invoice->package . '">';
                $html .= '<input type="hidden" name="ids[]" value="' . $invoice->id . '">';
                $html .= '</td>';

                // Action Button (Delete)
                $html .= '<td style="padding: 10px; text-align:center;">';
                $html .= '<a href="#" class="deleteCart" data-invoice_id="' . $invoice->id . '" style="color:#e74c3c; font-size:20px;"><i class="fa fa-trash"></i></a>';
                $html .= '</td>';
                $html .= '</tr>';
            }


            }


            $html .= '</tr>';
        }

        $html .='</tbody>';
        $html .='</table>';


        $html .= '<button class="btn btn-success checkInvoiceNull mb-2" type="submit" style="position:absolute; bottom:0; right:5px">Checkout</button>';
        $html .= '</form>';
        if (count($invoices) > 0) {
            $html .= '<button style="position:absolute; bottom:0; left:5px" class="btn btn-danger clearAllBtn mb-2" type="button" >Clear All</button>';
        }

        return response()->json(['status' => 'success', 'data' => $html,'count' => count($invoices)]);
    }


public function deleteAllCartItem()
    {

        $deleteCartData = Invoice::where('is_cart',0)->get();
        foreach ($deleteCartData as $cartItem) {
            $cartItem->forceDelete();
        }

        return response()->json(['status'=>'success','message'=>'Clear All']);
    }


    public function deleteCartItem(Request $request)
    {



        if ($request->type == 'Listing') {

            $inventory_id = $request->id;
            $invoice = Invoice::find($request->invoice_id);

            // Check if the inventory item exists in the invoice before detaching
            if ($invoice && $invoice->inventories->contains($inventory_id)) {
                // Detach only if the inventory item belongs to this invoice
                $invoice->inventories()->detach($inventory_id);
                return response()->json(['status' => 'success', 'message' => 'Inventory item removed successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Inventory item not found in this invoice'], 404);
            }

        } else {

            // Force delete the entire invoice if type is not specified
            Invoice::where('id', $request->invoice_id)->forceDelete();
            return response()->json(['status' => 'success', 'message' => 'Invoice deleted successfully']);
        }
    }

}
