<?php

namespace App\Service;

use App\Interface\InventoryServiceInterface;
use App\Interface\LeadServiceInterface;
use App\Models\DueOrder;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Lead;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeadService implements LeadServiceInterface
{

    public function all()
    {
    }

    public function store(array $attributes)
    {
    }

    public function update(array $attributes, int $id)
    {
    }

    public function find(int $id)
    {
    }

    public function trash(int $id)
    {
    }

    public function bulkInactive(array $ids){}
    public function bulkActive(array $ids){}

    public function bulkTrash(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            $item = Lead::find($id);
            $item->delete($item);
        }

        return $item;
    }

    public function getTrashedItem($id =null)
    {
        // abort_if(!auth()->user()->can('hrm_departments_index'), 403, 'Access Forbidden');
        $item = Lead::onlyTrashed()->orderBy('id', 'desc');
        return $item;
    }



    //Permanent Delete
    public function permanentDelete(int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        $item = Lead::onlyTrashed()->find($id);
        $item->forceDelete();
        return $item;
    }

    //Restore Trashed Item
    public function restore(int $id)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        $item = Lead::withTrashed()->find($id)->restore();
        return $item;
    }

    //Restore Trashed Item
    // public function restoreInvoice(int $id)
    // {
    //     // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
    //     $item = DueOrder::withTrashed()->find($id)->restore();
    //     return $item;
    // }

    public function bulkPermanentDelete(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            $item = Lead::onlyTrashed()->find($id);
            $item->forceDelete($item);
        }

        return $item;
    }



    public function bulkRestore(array $ids)
    {
        // abort_if(! auth()->user()->can('hrm_visit_delete'), 403, 'Access Forbidden');
        foreach ($ids as $id) {
            $item = Lead::withTrashed()->find($id);
            $item->restore($item);
        }

        return $item;
    }

    public function getRowCount($id = null)
    {
        // abort_if(!auth()->user()->can('hrm_departments_index'), 403, 'Access Forbidden');
        if($id)
        {
            $count = Lead::where('dealer_id',$id)->count();
        }else
        {
            $count = Lead::count();
        }

        return $count;
    }

    public function getTrashedCount($id =null)
    {
        // abort_if(!auth()->user()->can('hrm_departments_index'), 403, 'Access Forbidden');
        if($id)
        {
            $count = Lead::where('dealer_id',$id)->onlyTrashed()->count();
        }else
        {
            $count = Lead::onlyTrashed()->count();
        }

        return $count;
    }

    public function getUserRowCount($id)
    {
        // abort_if(!auth()->user()->can('hrm_departments_index'), 403, 'Access Forbidden');
        $count = Lead::where('dealer_id',$id)->count();
        return $count;
    }

    public function getUserTrashedCount($id)
    {
        // abort_if(!auth()->user()->can('hrm_departments_index'), 403, 'Access Forbidden');
        $count = Lead::where('dealer_id',$id)->onlyTrashed()->count();
        return $count;
    }

    public function getItemByFilter($request)
    {

        $query = Lead::with('inventory', 'customer','dealer')->orderBy('created_at', 'desc');

        if ($request->make_data != null) {
            // dd($request->all());
            $query->whereHas('inventory', function ($q) use ($request) {
                $q->where('make', $request->input('make_data'));
            });
        }
        if ($request->dealer_name != null) {
            $query->whereHas('dealer', function ($q) use ($request) {
                $q->where('name', $request->input('dealer_name'));
            });
        }

        if ($request->dealerCity_data != null) {
            $query->whereHas('dealer', function ($q) use ($request) {
                $q->where('city', $request->input('dealerCity_data'));
            });
        }
        if ($request->has('dealer_state') && !empty($request->dealer_state)) {
            $query->whereHas('dealer', function($query) use ($request) {
                $query->where('state', $request->dealer_state);
            });
        }

        if ($request->has('inventory_date') && !empty($request->inventory_date)) {
            $query->whereDate('created_at', $request->inventory_date);
        }

        return $query;
    }

    public function getUserItemByFilter($id,$request)
    {

        $query = Lead::where('dealer_id',$id)->with('mainInventory', 'customer','dealer')->orderBy('created_at', 'desc');

        if ($request->make_data != null) {
            // dd($request->all());
            $query->whereHas('mainInventory', function ($q) use ($request) {
                $q->where('make', $request->input('make_data'));
            });
        }
        if ($request->dealer_name != null) {
            $query->whereHas('dealer', function ($q) use ($request) {
                $q->where('name', $request->input('dealer_name'));
            });
        }

        if ($request->dealerCity_data != null) {
            $query->whereHas('dealer', function ($q) use ($request) {
                $q->where('city', $request->input('dealerCity_data'));
            });
        }
        if ($request->has('dealer_state') && !empty($request->dealer_state)) {
            $query->whereHas('dealer', function($query) use ($request) {
                $query->where('state', $request->dealer_state);
            });
        }

        if ($request->has('inventory_date') && !empty($request->inventory_date)) {
            $query->whereDate('created_at', $request->inventory_date);
        }

        return $query;
    }


    public function bulkInvoice(array $ids,$dealer)
    {
        try {
            $selectedData = $ids;
            // dd($selectedData);
            $existingInvoices = Invoice::whereIn("lead_id", $selectedData)->get();
            $existingDataIds = $existingInvoices->pluck("lead_id")->toArray();
            // new array data collected
            $newData = array_diff($selectedData, $existingDataIds);
            if (!empty($newData)) {
                $invoicesToInsert = [];

                foreach ($newData as $id) { // Change $ids to $newData
                    // Assuming $price and $package are coming from somewhere
                    $invoicesToInsert[] = [
                        'lead_id' => $id,
                        'price' => '4.99', // Assuming 'price' is a key in your request
                        'user_id' => $dealer[0],
                        'type' => 'Lead',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // dd($invoicesToInsert);

                Invoice::insert($invoicesToInsert);
                return redirect()->back()->with('status', 'success')->with('message', 'Added to cart successfully!');
            } else {
                return response()->json(['status'=>'error','message'=>'All data already checked']);
                // return redirect()->back()->with('status', 'error')->with('message', );
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getLeadByFilter($id)
    {

        $query = Lead::with('inventory', 'customer')->orderBy('created_at', 'desc')->where('dealer_id',$id);

        return $query;
    }

    public function getInvoiceByFilter($id)
    {

        $query = Invoice::with('dealer')->where('user_id',$id)->whereIn('id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                  ->from('invoices')
                  ->groupBy('generated_id');
        })->select('id','generated_id','type','status','created_at')->get();

        return $query;
    }
}
