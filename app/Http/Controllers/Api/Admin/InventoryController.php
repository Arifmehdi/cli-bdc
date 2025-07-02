<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\MainInventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function dashboard(Request $request)
    {
        return([
            'inventory' => MainInventory::where('status', 1)->count(),
            'dealer' => Dealer::where('status',1)->count(),
            'leads' => Lead::where('status',1)->count(),
            'invoice' => Invoice::where('is_cart', 1)->count(),
        ]);
    }

    public function index(Request $request)
    {
        $query = DB::table('main_inventories')
            ->select([
                'id',
                'deal_id',
                'stock',
                'year',
                'make',
                'model',
                'vin',
                'active_till',
                'featured_till',
                'payment_date',
                'package',
                'image_count',
                'inventory_status'
            ]);

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vin', 'like', "%{$search}%")
                    ->orWhere('stock', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Add sorting
        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'asc');
        }

        return $query->paginate($request->per_page ?? 50);
    }
}
