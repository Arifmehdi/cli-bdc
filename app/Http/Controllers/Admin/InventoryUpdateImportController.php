<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\MainInventory;
use App\Models\MainPriceHistory;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InventoryUpdateImportController extends Controller
{

    public function inventoryUpdateRequest(Request $request)
    {
        $authUser = Auth::user();
        $lastUpdatedDate = MainInventory::whereNotNull('inventory_status')->max('updated_at');


        $query = MainInventory::with('dealer')->whereNotNull('inventory_status');

        // Apply access control if user doesn't have all access
        if (!$authUser->hasAllaccess()) {
            $query->where('deal_id', $authUser->id);
        }

        $rowCount = 0;
        $trashedCount = 0;

        if ($lastUpdatedDate) {
            $lastUpdatedDate = Carbon::parse($lastUpdatedDate);
            $startOfDay = $lastUpdatedDate->copy()->startOfDay();
            $endOfDay = $lastUpdatedDate->copy()->endOfDay();

            $query->whereBetween('updated_at', [$startOfDay, $endOfDay]);

            // Get total count before fetching data
            $rowCount = $query->count();

            // Count soft-deleted records (with same access control)
            $trashedQuery = MainInventory::onlyTrashed()
                ->whereNotNull('inventory_status')
                ->whereBetween('updated_at', [$startOfDay, $endOfDay]);

            if (!$authUser->hasAllaccess()) {
                $trashedQuery->where('deal_id', $authUser->id);
            }

            $trashedCount = $trashedQuery->count();
        }
        // dd(Auth::user()->id, $request->ajax());

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($row) {
                    return $row->id; // Use any unique identifier
                })
                ->addColumn('check', function ($row) {
                    return '<div class="text-center">
                                <input type="checkbox" name="contact_id[]" value="' . $row->id . '" class="mt-2 check1 check-row">
                            </div>';
                })
                ->addColumn('make', function ($row) {
                    return ucfirst($row->make) ?? '--';
                })
                ->addColumn('model', function ($row) {
                    return ucfirst($row->model) ?? '--';
                })
                ->addColumn('fuel', function ($row) {

                    return ucfirst($row->fuel) ?? '--';
                })
                ->addColumn('vin_data', function ($row) {
                    return '<a href="#" style="border-bottom: 1px solid #007bff;">' . $row->vin . '</a>';
                })
                ->addColumn('img_num', function ($row) {
                    $inventory_num = $row->image_count;
                    return $inventory_num;
                })
                ->addColumn('dealer_name', function ($row) {
                    $dealer_name = $row->dealer->name;
                    return $dealer_name;
                })
                ->addColumn('action', function ($row) {
                    if ($row->trashed()) {
                        return '<a href="' . route('admin.contact.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '">
                                    <i class="fa fa-recycle"></i>
                                </a>
                                <a href="' . route('admin.dealer.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </a>';
                    } else {
                        return '<a href="' . route('admin.dealer.delete', $row->id) . '" data-id="' . $row->id . '" class="btn btn-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                </a>';
                    }
                })
                ->rawColumns(['action', 'check', 'vin_data'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }

        return view('backend.admin.import.inventory_update');
    }

    // public function inventoryUpdateRequest(Request $request)
    // {
    //     $lastUpdatedDate = MainInventory::whereNotNull('inventory_status')->max('updated_at');

    //     $query =  MainInventory::whereNotNull('inventory_status');
    //     $rowCount = 0;
    //     $trashedCount = 0;

    //     // If $lastUpdatedDate is not null, proceed to get the data
    //     if ($lastUpdatedDate) {
    //         $lastUpdatedDate = Carbon::parse($lastUpdatedDate);
    //         $startOfDay = $lastUpdatedDate->copy()->startOfDay();
    //         $endOfDay = $lastUpdatedDate->copy()->endOfDay();

    //         $query->whereBetween('updated_at', [$startOfDay, $endOfDay]);

    //         // Get total count before fetching data
    //         $rowCount = $query->count();

    //         // Count soft-deleted records
    //         $trashedCount = MainInventory::onlyTrashed()
    //             ->whereNotNull('inventory_status')
    //             ->whereBetween('updated_at', [$startOfDay, $endOfDay])
    //             ->count();
    //     }

    //     // $authUser = Auth::user();
    //     // if($authUser->hasAllaccess())
    //     // {
    //     //     $dealerData = User::orderBy('id', 'desc')->where('status',1)->where('batch_no', $latestBatchNo)->where('import_type',1);
    //     // }else
    //     // {
    //     //     $dealerData = User::orderBy('id', 'desc')->where('status',1)->where('batch_no', $latestBatchNo)->where('import_type',1);
    //     // }

    //     if ($request->ajax()) {
    //         return DataTables::of($query->get())
    //             ->addIndexColumn()
    //             ->addColumn('DT_RowIndex', function ($row) {
    //                 return $row->id; // Use any unique identifier for your rows
    //             })

    //             ->addColumn('check', function ($row) {
    //                 $html = '<div class=" text-center">
    //                     <input type="checkbox" name="contact_id[]" value="' . $row->id . '" class="mt-2 check1 check-row">

    //                 </div>';
    //                 return $html;
    //             })
    //             ->addColumn('vin_data', function ($row) {
    //                 // $url = route('admin.inventory.list.v1', $row->deal_id);
    //                 $url = '#';
    //                 $html = '<a href="' . $url . '" style="border-bottom: 1px solid #007bff;">' . $row->vin . '</a>';
    //                 return $html;
    //             })
    //             ->addColumn('dealer_phone', function ($row) {
    //                 return $row->phone;
    //             })
    //             ->addColumn('dealer_email', function ($row) {
    //                 return $row->email;
    //             })
    //             ->addColumn('dealer_city', function ($row) {
    //                 return $row->city;
    //             })
    //             ->addColumn('dealer_state', function ($row) {
    //                 return $row->state;
    //             })
    //             ->addColumn('dealer_zip', function ($row) {
    //                 return $row->zip;
    //             })
    //             ->addColumn('inventory_num', function ($row) {
    //                 return 00;
    //             })
    //             ->addColumn('role', function ($row) {
    //                 $roleName = Role::findOrFail($row->role_id)->name;
    //                 return ucfirst($roleName);
    //             })
    //             ->addColumn('import_type', function ($row) {
    //                 return ($row->import_type == 1) ? 'Bulk Import' : ' Manual Import';
    //             })

    //             ->addColumn('action', function ($row) {

    //                 if ($row->trashed()) {
    //                     $html = '<a href="' . route('admin.contact.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
    //                         '<a href="' . route('admin.dealer.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
    //                 } else {
    //                     // $html = '<a data-id="' . $row->id . '" style="margin-right:6px !important" class="btn btn-success btn-sm view-contact"><i  class="fa fa-eye"></i></a>' .
    //                     $html = '<a href="' . route('admin.dealer.delete', $row->id) . '" data-id= "' . $row->id . '" class="btn btn-danger btn-sm delete"><i  class="fa fa-trash"></i></a>';
    //                 }
    //                 return $html;
    //             })

    //             ->rawColumns(['action', 'message', 'check','dealer_name'])
    //             ->with([
    //                 'allRow' => $rowCount,
    //                 'trashedRow' => $trashedCount,
    //             ])
    //             ->smart(true)
    //             ->make(true);
    //     }

    //     return view('backend.admin.import.inventory_update');
    // }



    public function storeCSVUpdateInventory(Request $request)
    {
        // Validate the input file
        $request->validate([
            'import_file' => 'required|mimes:csv,txt',
        ], [
            'import_file.required' => 'Please upload a CSV file.',
            'import_file.mimes' => 'Only CSV files are allowed.',
        ]);

        $datetimeData = date('Ymd');
        $fileName = 'update_inventory_'.$datetimeData.'.csv';
        $directory = public_path('uploads/update_inventory');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save the uploaded file
        $filePath = $directory . '/' . $fileName;
        $request->file('import_file')->move($directory, $fileName);

        // Open the file and process its contents
        if (($handle = fopen($filePath, 'r')) !== false) {

            $headers = [];
            $isHeaderRow = true;

            $errorRows = [];
            $importedDealers = [];
            $notImportedDealers = [];
            $updatedDealers = [];

            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }

                if ($isHeaderRow) {
                    // Read headers and convert them to lowercase
                    $headers = array_map(fn($header) => strtolower(trim($header)), $data);
                    $isHeaderRow = false;
                    continue;
                }

                // Ensure the number of columns matches the headers
                if (count($headers) !== count($data)) {
                    $errorRows[] = ['row' => $data, 'error' => 'Header and row column count mismatch'];
                    $notImportedDealers[] = implode(',', $data) . ' - Column count mismatch';
                    continue;
                }

                // Map CSV rows to headers
                $row = array_combine($headers, $data);

                $vin = $row['vin'] ?? null;
                $detail_url = $row['source_url_2'] ?? null;  // Updating detail_url using source_url_2
                $inventory_status = $row['status'] ?? null;  // Updating inventory_status using status
                $old_price = $row['old price'] ?? null;          // Updating price using old_price
                $price_data = $row['new price'] ?? null;  // Get the price data or null
                $price_data = $price_data !== null ? preg_replace('/\D/', '', $price_data) : null;  // Keep only digits
                $price = $price_data !== null ? (float)$price_data : null;  // Convert to float if valid      // Updating price using old_price

                if (!is_numeric($price) || $price === '----' || $inventory_status  == 'Sold') {
                    $price = null;  // Set to null, but don't include in update
                }

                if (empty($vin)) {
                    $errorRows[] = ['row' => $row, 'error' => 'Missing mandatory fields (Vin, Status)'];
                    $notImportedDealers[] = implode(',', $data) . ' - Missing mandatory fields';
                    continue;
                }

                // Fetch inventory details
                $inventoryInfo = MainInventory::where('vin', $vin)->first();


                if ($inventoryInfo) {
                    // Compare fields for changes
                    $changes = [];

                    if ($detail_url !== null && $detail_url !== $inventoryInfo->detail_url) {
                        $changes['detail_url'] = urldecode($detail_url);
                    }
                    if ($inventory_status !== null && $inventory_status !== $inventoryInfo->inventory_status) {
                        $changes['inventory_status'] = $inventory_status;
                    }

                    // if ($price !== null && $price != $inventoryInfo->price) {  // Ensure price is only included if it's valid
                    //     $changes['price'] = $price;
                    // }

                    // Fetch inventory details
                // dd($inventoryInfo, $changes, $price, ($inventoryInfo && $price !== null && $price != $inventoryInfo->price));
                if ($inventoryInfo && $price !== null && $price != $inventoryInfo->price) {
                    // Ensure price is only included if it's valid
                    $changes['price'] = $price;
                    $mainInventoryId = $inventoryInfo->id;

                    // Calculate the change amount and add + or - sign
                    $changeAmount = $price - $inventoryInfo->price;
                    $formattedChange = ($changeAmount > 0 ? '+' : '') . $changeAmount;

                    // dd($changes['price'], $inventoryInfo->price, $formattedChange);
                    // Create a new price history entry (NO UPDATE)
                    MainPriceHistory::create([
                        'main_inventory_id' => $mainInventoryId,
                        'change_date' => now(),
                        'change_amount' => $formattedChange,
                        'amount' => $price,
                        'status' => 1
                    ]);

                    // Update the inventory price
                    $inventoryInfo->update(['price' => $price]);
                }

                // If there are changes, update the record
                    if (!empty($changes)) {
                        $inventoryInfo->update($changes);

                        // // Format changes manually to keep the URL clickable
                        // $updatedDealers[] = "Updated VIN: {$vin} - Changes: " . json_encode($changes);

                        $formattedChanges = [];
                        foreach ($changes as $key => $value) {
                            if ($key === 'detail_url') {
                                // Wrap URL in < > to make it clickable in most text environments
                                $formattedChanges[] = "{$key}: <{$value}>";
                            } else {
                                $formattedChanges[] = "{$key}: " . (is_string($value) ? $value : json_encode($value));
                            }
                        }
                        $updatedDealers[] = "Updated VIN: {$vin} - Changes: " . implode(", ", $formattedChanges);

                    } else {
                        $notImportedDealers[] = "VIN: {$vin} - No changes detected";
                    }
                    continue;
                } else {
                    $notImportedDealers[] = "VIN: {$vin} - Inventory not found";
                }
            }

            fclose($handle);

            // Save reports
            file_put_contents($directory . '/imported_changes.txt', implode("\n", $importedDealers));
            file_put_contents($directory . '/not_imported_changes.txt', implode("\n", $notImportedDealers));
            file_put_contents($directory . '/updated_changes.txt', implode("\n", $updatedDealers));

            if (!empty($errorRows)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some rows could not be processed.',
                    'imported_count' => count($importedDealers),
                    'not_imported_count' => count($notImportedDealers),
                    'updated_count' => count($updatedDealers),
                    'error_rows' => $errorRows,
                ], 422);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "CSV file uploaded and inventory updated successfully!",
            'imported_count' => count($importedDealers),
            'not_imported_count' => count($notImportedDealers),
            'updated_count' => count($updatedDealers),
        ], 200);
    }



    // public function storeCSVUpdateInventory(Request $request)
    // {
    //     // Validate the input file
    //     $request->validate([
    //         'import_file' => 'required|mimes:csv,txt',
    //     ], [
    //         'import_file.required' => 'Please upload a CSV file.',
    //         'import_file.mimes' => 'Only CSV files are allowed.',
    //     ]);

    //     $datetimeData = date('Ymd');
    //     $fileName = 'update_inventory_'.$datetimeData.'.csv';
    //     $directory = public_path('uploads/update_inventory');

    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     // Save the uploaded file
    //     $filePath = $directory . '/' . $fileName;
    //     $request->file('import_file')->move($directory, $fileName);

    //     // $addedRows = [];
    //     // $errorRows = [];

    //     // Parse and save data to the database
    //     if (($handle = fopen($filePath, 'r')) !== false) {

    //         $headers = [];
    //         $isHeaderRow = true;

    //         $errorRows = [];
    //         $importedDealers = [];
    //         $notImportedDealers = [];
    //         $updatedDealers = []; // To track updates

    //         $latestBatchNo = User::latest('batch_no')->value('batch_no');
    //         $batchNo = $latestBatchNo ? $latestBatchNo + 1 : 10;

    //         while (($data = fgetcsv($handle, 10000, ',')) !== false) {
    //             // Skip empty rows
    //             if (empty(array_filter($data))) {
    //                 continue;
    //             }

    //             if ($isHeaderRow) {
    //                 // Read headers and convert them to lowercase
    //                 $headers = array_map(fn($header) => strtolower(trim($header)), $data);
    //                 $isHeaderRow = false;
    //                 continue;
    //             }

    //             // Ensure the number of columns matches the headers
    //             if (count($headers) !== count($data)) {
    //                 $errorRows[] = ['row' => $data, 'error' => 'Header and row column count mismatch'];
    //                 $notImportedDealers[] = implode(',', $data) . ' - Column count mismatch';
    //                 continue;
    //             }

    //             // Map CSV rows to headers
    //             $row = array_combine($headers, $data);

    //             $src_url = $row['source_url_2'] ?? null;
    //             $vin = $row['vin'] ?? null;
    //             $new_price = $row['new price'] ?? null;
    //             $old_price = $row['old price'] ?? null;
    //             $inventory_status = $row['status'] ?? null;


    //             if (empty($vin)) {
    //                 $errorRows[] = ['row' => $row, 'error' => 'Missing mandatory fields (Vin, Status)'];
    //                 $notImportedDealers[] = implode(',', $data) . ' - Missing mandatory fields';
    //                 continue;
    //             }
    //             // $name = $row['name'] ?? null;
    //             // $zipCode = $row['zip code'] ?? $row['zip'] ?? null;
    //             // Validate required fields
    //             // if (empty($name) || empty($zipCode)) {
    //             //     $errorRows[] = ['row' => $row, 'error' => 'Missing mandatory fields (Name, Zip Code)'];
    //             //     $notImportedDealers[] = implode(',', $data) . ' - Missing mandatory fields';
    //             //     continue;
    //             // }
    //             // $userInfo = User::where('name', $name)
    //             //             ->where('zip', $zipCode)
    //             //             ->first();

    //             // $inventoryInfo = MainInventory::with('mainPriceHistory')->where('vin', $vin)->first();

    //             $inventoryInfo = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price','inventory_status')
    //             ->with([
    //                 'dealer' => function ($query) {
    //                     $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id')
    //                         ->addSelect('id'); // Add id explicitly to avoid conflict
    //                 },
    //                 'additionalInventory' => function ($query) {
    //                     $query->select('main_inventory_id', 'detail_url' ,'local_img_url')  // Only necessary columns
    //                         ->addSelect('id'); // Add id explicitly to avoid conflict
    //                 },
    //                 'mainPriceHistory' => function ($query) {
    //                     $query->select('main_inventory_id', 'change_amount') // Only necessary columns
    //                         ->addSelect('id'); // Add id explicitly to avoid conflict
    //                 }
    //             ])->where('vin', $vin)->first();


    //             if ($inventoryInfo) {

    //                 // Compare fields for changes
    //                 $changes = [];
    //                 $updatableFields = [
    //                     'detail_url', 'price', 'inventory_status'
    //                 ];

    //                 // Compare and update only if changed
    //                 foreach ($updatableFields as $field => $newValue) {
    //                     if (!is_null($newValue) && $inventoryInfo->$field != $newValue) {
    //                         $changes[$field] = $newValue;
    //                     }
    //                 }

    //                 // If there are changes, update the record
    //                 if (!empty($changes)) {
    //                     $inventoryInfo->update($changes);
    //                 }

    //                 dd($inventoryInfo);
    //                 // foreach ($updatableFields as $field) {
    //                 //     if (($row[$field] ?? null) && $userInfo->$field !== $row[$field]) {
    //                 //         $changes[$field] = [
    //                 //             'old' => $userInfo->$field,
    //                 //             'new' => $row[$field]
    //                 //         ];
    //                 //         $userInfo->$field = $row[$field];
    //                 //     }
    //                 // }

    //                 if (!empty($changes)) {
    //                     $userInfo->save();
    //                     $updatedDealers[] = "Dealer ID: {$userInfo->dealer_id} - Name: {$userInfo->name} - Changes: " . json_encode($changes);
    //                 } else {
    //                     $notImportedDealers[] = implode(',', $data) . " - No changes detected";
    //                 }
    //                 continue;

    //                 // $notImportedDealers[] = implode(',', $data) . " - Dealer already exists (ID: {$userInfo->dealer_id})";
    //                 // continue;
    //             }

    //         fclose($handle);

    //         // Save reports
    //         file_put_contents($directory . '/imported_changes.txt', implode("\n", $importedDealers));
    //         file_put_contents($directory . '/not_imported_changes.txt', implode("\n", $notImportedDealers));
    //         file_put_contents($directory . '/updated_dealers.txt', implode("\n", $updatedDealers));

    //         if (!empty($errorRows)) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Some rows could not be processed.',
    //                 'imported_count' => count($importedDealers),
    //                 'not_imported_count' => count($notImportedDealers),
    //                 'updated_count' => count($updatedDealers),
    //                 'error_rows' => $errorRows,
    //             ], 422);
    //         }
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => "Dealer file uploaded and data saved successfully!",
    //         'imported_count' => count($importedDealers),
    //         'not_imported_count' => count($notImportedDealers),
    //         'updated_count' => count($updatedDealers),
    //     ], 200);
    // }
}
