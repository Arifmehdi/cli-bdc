<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Latlongs;
use App\Models\Inventory;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use App\Models\TmpInventories;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\AdditionalInventory;
use App\Models\MainInventory;
use App\Models\MainPriceHistory;
use App\Models\PriceHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Writer;
use Yajra\DataTables\DataTables;

class InventoryImportController extends Controller
{


    public function index()
    {
        $users = User::whereNotNull('name')->get();
        $authUser = Auth::user();
        if($authUser->hasAllaccess())
        {
            // $inventories = Inventory::orderBy('id', 'desc')->paginate(12);
            $inventories = MainInventory::orderBy('id', 'desc')->paginate(12);
        }else
        {
            // $inventories = Inventory::where('deal_id',$authUser->id)->orderBy('id', 'desc')->paginate(12);
            $inventories = MainInventory::where('deal_id',$authUser->id)->orderBy('id', 'desc')->paginate(12);

        }
            return view('backend.admin.import.inventory_import', compact('users', 'inventories'));
    }


    // public function inventoryImportRequest(Request $request)
    // {
    //     dd($request->all());
    // }

    public function inventoryImportRequest(Request $request)
    {
        $users = User::whereNotNull('name')->get();
        $authUser = Auth::user();
        if($authUser->hasAllaccess())
        {
            // $inventories = Inventory::orderBy('id', 'desc')->paginate(12);

            // dd($dealersWithInventoryCount);
            if ($request->showTrashed == 'true') {
                // $inventories = TmpInventories::select('deal_id', DB::raw('COUNT(*) as inventory_count'))
                $inventories = MainInventory::select('deal_id', DB::raw('COUNT(*) as inventory_count'))
                ->whereHas('dealer', function ($query) {
                    $query->where('status', 3); // Filter users with status = 3
                })
                ->groupBy('deal_id');
                // ->get();
            }else{
                // $inventories = TmpInventories::select('deal_id', DB::raw('COUNT(*) as inventory_count'))
                $inventories = MainInventory::select('deal_id', DB::raw('COUNT(*) as inventory_count'))
                ->whereHas('dealer', function ($query) {
                    $query->where('status', 3); // Filter users with status = 3
                })
                ->groupBy('deal_id');
            }
            // $rowCount = TmpInventories::count();
            // $trashedCount = TmpInventories::onlyTrashed()->count();
            $rowCount = MainInventory::count();
            $trashedCount = MainInventory::onlyTrashed()->count();
        }else
        {
            // $inventories = TmpInventories::where('deal_id',$authUser->id)->orderBy('id', 'desc')->paginate(12);
            $inventories = MainInventory::where('deal_id',$authUser->id)->orderBy('id', 'desc')->paginate(12);
        }

        // // marif ajax code start here
        // if ($id != null) {
        //     $data = Notification::find($id);
        //     $data->is_read = '1';
        //     $data->save();
        // }
        // if ($request->showTrashed == 'true') {
        //     $info = Contact::onlyTrashed()->orderBy('id', 'desc');
        // } else {
        //     $info = Contact::orderBy('created_at', 'desc');
        // }
        // $rowCount = Contact::count();
        // $trashedCount = Contact::onlyTrashed()->count();

        if ($request->ajax()) {
            return DataTables::of($inventories)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($row) {
                    return $row->id; // Use any unique identifier for your rows
                })

                ->addColumn('check', function ($row) {
                    $html = '<div class=" text-center">
                        <input type="checkbox" name="contact_id[]" value="' . $row->id . '" class="mt-2 check1 check-row">

                    </div>';
                    return $html;
                })
                ->addColumn('dealer_name', function ($row) {
                    $url = route('admin.inventory.list.v1', $row->deal_id);
                    $html = '<a href="' . $url . '" style="border-bottom: 1px solid #007bff;">' . $row->dealer->name . '</a>';
                    return $html;
                })
                ->addColumn('dealer_phone', function ($row) {
                    return $row->dealer->phone;
                })
                ->addColumn('dealer_email', function ($row) {
                    return $row->dealer->email;
                })
                ->addColumn('dealer_city', function ($row) {
                    return $row->dealer->city;
                })
                ->addColumn('dealer_state', function ($row) {
                    return $row->dealer->state;
                })
                ->addColumn('dealer_zip', function ($row) {
                    return $row->dealer->zip;
                })
                ->addColumn('inventory_num', function ($row) {
                    return $row->inventory_count;
                })

                ->addColumn('action', function ($row) {

                    if ($row->trashed()) {
                        $html = '<a href="' . route('admin.contact.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="' . route('admin.contact.permanent.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {
                        $html = '<a data-id="' . $row->id . '" style="margin-right:6px !important" class="btn btn-success btn-sm view-contact"><i  class="fa fa-eye"></i></a>' .
                            '<a data-id= "' . $row->id . '" class="btn btn-danger btn-sm delete-contact"><i  class="fa fa-trash"></i></a>';
                    }
                    return $html;
                })

                ->rawColumns(['action', 'message', 'check','dealer_name'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }
        // // marif ajax code end here

            return view('backend.admin.import.inventory_import_request', compact('users', 'inventories'));
    }

    public function inventoryRequestView(Request $request, $id=null)
    {
        $users = User::whereNotNull('name')->get();
        $authUser = Auth::user();
        if($authUser->hasAllaccess())
        {
            // $inventories = Inventory::orderBy('id', 'desc')->paginate(12);

            // dd($dealersWithInventoryCount);
            if ($request->showTrashed == 'true') {
                // $inventories = TmpInventories::with('dealer')->where('deal_id',$id);
                $inventories = MainInventory::with('dealer')->where('deal_id',$id);
                // ->get();
            }else{
                // $inventories = TmpInventories::with('dealer')->where('deal_id',$id);
                $inventories = MainInventory::with('dealer')->where('deal_id',$id);
            }
            // $rowCount = TmpInventories::count();
            // $trashedCount = TmpInventories::onlyTrashed()->count();
            $rowCount = MainInventory::where('deal_id',$id)->count();
            $trashedCount = MainInventory::where('deal_id',$id)->onlyTrashed()->count();
        }else
        {
            // $inventories = TmpInventories::where('deal_id',$authUser->id)->orderBy('id', 'desc')->paginate(12);
            $inventories = MainInventory::where('deal_id',$authUser->id)->orderBy('id', 'desc')->paginate(12);
        }

        // // marif ajax code start here
        // if ($id != null) {
        //     $data = Notification::find($id);
        //     $data->is_read = '1';
        //     $data->save();
        // }
        // if ($request->showTrashed == 'true') {
        //     $info = Contact::onlyTrashed()->orderBy('id', 'desc');
        // } else {
        //     $info = Contact::orderBy('created_at', 'desc');
        // }
        // $rowCount = Contact::count();
        // $trashedCount = Contact::onlyTrashed()->count();
        if ($request->ajax()) {
            return DataTables::of($inventories)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($row) {
                    return $row->id; // Use any unique identifier for your rows
                })

                ->addColumn('check', function ($row) {
                    $html = '<div class=" text-center">
                        <input type="checkbox" name="contact_id[]" value="' . $row->id . '" class="mt-2 check1 check-row">

                    </div>';
                    return $html;
                })

                ->addColumn('stock', function ($row) {
                    return '#'.$row->stock;
                })
                ->addColumn('price', function ($row) {
                    return '$'.number_format($row->price);
                })
                ->addColumn('miles', function ($row) {
                    return number_format($row->miles);
                })
                ->addColumn('miles', function ($row) {
                    return number_format($row->miles);
                })
                ->addColumn('body_description', function ($row) {
                    return ucfirst($row->body_description);
                })
                ->addColumn('action', function ($row) {

                    if ($row->trashed()) {
                        $html = '<a href="' . route('admin.contact.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="' . route('admin.inventory.list.delete.v1', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {
                        $html = '<a data-id="' . $row->id . '" style="margin-right:6px !important" class="btn btn-success btn-sm view-contact"><i  class="fa fa-eye"></i></a>' .
                            '<a data-id= "' . $row->id . '" class="btn btn-danger btn-sm delete-inventory"><i  class="fa fa-trash"></i></a>';
                    }
                    return $html;
                })

                ->rawColumns(['action', 'message', 'check'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }
        // // marif ajax code end here
        $dealer_name = $inventories->get()[0]->dealer->name;

            return view('backend.admin.import.inventory_request_list', compact('id','dealer_name'));
    }



    // public function storeInventory(Request $request)
    // {
    //     //// abort_if(! auth()->user()->can('hrm_bulk_attendance_import_store'), 403, 'Access forbidden');
    //     $request->validate([
    //         'user' => 'required',
    //         'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
    //     ], [
    //         'user.required' => 'User field is required',
    //         'import_file.required' => 'Import field is required',
    //         'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
    //     ]);

    //     // ***********data store start here
    //     $originalName = $request->file('import_file')->getClientOriginalName();


    //     $userId = $request->user;
    //     $directory = public_path('uploads/import');

    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     $originalFileName = $request->file('import_file')->getClientOriginalName();
    //     $modifiedFileName = $userId.'_'.date('mdY') . '_' . $originalFileName;
    //     $request->file('import_file')->move($directory, $modifiedFileName);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => "Non-Server CSV Stored Successfully",
    //     ], 200);
    // }

    // public function storeInventory(Request $request)
    // {
    //     $request->validate([
    //         'user' => 'required',
    //         'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
    //     ], [
    //         'user.required' => 'User field is required',
    //         'import_file.required' => 'Import field is required',
    //         'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
    //     ]);

    //     $userId = $request->user;
    //     $directory = public_path('uploads/import');

    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     $originalName = $request->file('import_file')->getClientOriginalName();
    //     $file = $request->file('import_file');
    //     $filePath = $file->getPathname();

    //     // Read the CSV file
    //     $data = array_map('str_getcsv', file($filePath));
    //     $headers = array_shift($data); // Extract headers from the first row

    //     $batchSize = $request->splitNumber; // Number of rows per batch
    //     $batchNumber = 101; // Starting batch number
    //     $totalRows = count($data);
    //     $totalBatches = ceil($totalRows / $batchSize); // Calculate total batches needed

    //     Log::info("Total Rows: " . $totalRows); // Log total rows
    //     Log::info("Total Batches: " . $totalBatches); // Log total batches

    //     // Iterate over data and create batches
    //     for ($i = 0; $i < $totalRows; $i += $batchSize) {
    //         $batchData = array_slice($data, $i, $batchSize);

    //         // If the batch data is empty, skip this iteration
    //         if (empty($batchData)) {
    //             continue;
    //         }

    //         // Add batch_no to each row
    //         foreach ($batchData as &$row) {
    //             $row[] = $batchNumber;
    //         }

    //         // Add the `batch_no` column to headers if it's not already present
    //         if (!in_array('batch_no', $headers)) {
    //             $headers[] = 'batch_no';
    //         }

    //         // // Check if the batch number exceeds total batches, and break out if necessary
    //         // if ($batchNumber > $totalBatches) {
    //         //     break;
    //         // }

    //         // Create a new CSV file for the batch
    //         $batchFileName = $directory . "/{$userId}_{$originalName}_{$batchNumber}.csv";
    //         // Check if the file already exists to avoid overwriting or duplication
    //         if (file_exists($batchFileName)) {
    //             Log::warning("File already exists: " . $batchFileName);
    //             continue; // Skip this iteration to avoid overwriting
    //         }

    //         $fileHandle = fopen($batchFileName, 'w');
    //         fputcsv($fileHandle, $headers);

    //         foreach ($batchData as $row) {
    //             fputcsv($fileHandle, $row);
    //         }

    //         fclose($fileHandle);

    //         Log::info("Batch {$batchNumber} created with " . count($batchData) . " rows.");

    //         // Increment batch number for next batch
    //         $batchNumber++;

    //         // Check if the last batch has fewer rows and log that
    //         if (count($batchData) < $batchSize) {
    //             Log::info("Last batch has fewer rows: " . count($batchData));
    //         }
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => "CSV processed and stored in batches successfully.",
    //     ], 200);
    // }


    public function storeInventory(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
        ], [
            'user.required' => 'User field is required',
            'import_file.required' => 'Import field is required',
            'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
        ]);

        $userId = $request->user;
        $directory = public_path('uploads/import');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $originalName = $request->file('import_file')->getClientOriginalName();
        $file = $request->file('import_file');
        $filePath = $file->getPathname();

        // Read the CSV file
        $data = array_map('str_getcsv', file($filePath));
        $headers = array_shift($data); // Extract headers from the first row

        $batchSize = $request->splitNumber; // Number of rows per batch
        $batchNumber = 101; // Starting batch number
        $totalRows = count($data);

        Log::info("Total Rows: " . $totalRows); // Log total rows

        // Iterate over data and create batches
        for ($i = 0; $i < $totalRows; $i += $batchSize) {
            $batchData = array_slice($data, $i, $batchSize);

            // If the batch data is empty, skip this iteration
            if (empty($batchData)) {
                continue;
            }

            // Add batch_no column to the headers if not present
            if (!in_array('batch_no', $headers)) {
                $headers[] = 'batch_no';
            }

            // Prepare data for writing to CSV
            foreach ($batchData as &$row) {
                // Append the batch number to each row
                $row[] = $batchNumber;

                // Check for multi-line values in specific columns (e.g., "features")
                foreach ($row as $key => $value) {
                    if (strpos($value, "\n") !== false) {
                        // Replace newlines with commas while keeping the data in a single cell
                        $row[$key] = '"' . str_replace("\n", ", ", $value) . '"';
                    }
                }
            }

            // Create a new CSV file for the batch
            $batchFileName = $directory . "/{$userId}_{$originalName}_{$batchNumber}.csv";

            // Check if the file already exists to avoid overwriting or duplication
            if (file_exists($batchFileName)) {
                Log::warning("File already exists: " . $batchFileName);
                continue; // Skip this iteration to avoid overwriting
            }

            $fileHandle = fopen($batchFileName, 'w');
            fputcsv($fileHandle, $headers);

            foreach ($batchData as $row) {
                fputcsv($fileHandle, $row);
            }

            fclose($fileHandle);

            Log::info("Batch {$batchNumber} created with " . count($batchData) . " rows.");

            // Increment batch number for the next batch
            $batchNumber++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "CSV processed and stored in batches successfully.",
        ], 200);
    }


    // public function storeInventoryv1(Request $request)
    // {
    //     $request->validate([
    //         'user' => 'required',
    //         'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
    //     ], [
    //         'user.required' => 'User field is required',
    //         'import_file.required' => 'Import field is required',
    //         'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
    //     ]);

    //     $userId = $request->user;
    //     $directory = public_path('uploads/import');

    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     $originalName = $request->file('import_file')->getClientOriginalName();
    //     $file = $request->file('import_file');
    //     $filePath = $file->getPathname();

    //     // Read the CSV file
    //     $data = array_map('str_getcsv', file($filePath));
    //     $headers = array_shift($data); // Extract headers from the first row

    //     $batchSize = $request->splitNumber; // Number of rows per batch
    //     $batchNumber = 101; // Starting batch number
    //     $totalRows = count($data);
    //     $totalBatches = ceil($totalRows / $batchSize); // Calculate total batches needed

    //     Log::info("Total Rows: " . $totalRows); // Log total rows
    //     Log::info("Total Batches: " . $totalBatches); // Log total batches

    //     // Iterate over data and create batches
    //     for ($i = 0; $i < $totalRows; $i += $batchSize) {
    //         $batchData = array_slice($data, $i, $batchSize);

    //         // If the batch data is empty, skip this iteration
    //         if (empty($batchData)) {
    //             continue;
    //         }

    //         // Add batch_no to each row
    //         foreach ($batchData as &$row) {
    //             $row[] = $batchNumber;
    //         }

    //         // Add the `batch_no` column to headers if it's not already present
    //         if (!in_array('batch_no', $headers)) {
    //             $headers[] = 'batch_no';
    //         }

    //         // // Check if the batch number exceeds total batches, and break out if necessary
    //         // if ($batchNumber > $totalBatches) {
    //         //     break;
    //         // }

    //         // Create a new CSV file for the batch
    //         $batchFileName = $directory . "/{$userId}_{$originalName}_{$batchNumber}.csv";
    //         // Check if the file already exists to avoid overwriting or duplication
    //         if (file_exists($batchFileName)) {
    //             Log::warning("File already exists: " . $batchFileName);
    //             continue; // Skip this iteration to avoid overwriting
    //         }

    //         $fileHandle = fopen($batchFileName, 'w');
    //         fputcsv($fileHandle, $headers);

    //         foreach ($batchData as $row) {
    //             fputcsv($fileHandle, $row);
    //         }

    //         fclose($fileHandle);

    //         Log::info("Batch {$batchNumber} created with " . count($batchData) . " rows.");

    //         // Increment batch number for next batch
    //         $batchNumber++;

    //         // Check if the last batch has fewer rows and log that
    //         if (count($batchData) < $batchSize) {
    //             Log::info("Last batch has fewer rows: " . count($batchData));
    //         }
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => "CSV processed and stored in batches successfully.",
    //     ], 200);
    // }


    public function storeInventoryv1(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
        ], [
            'user.required' => 'User field is required',
            'import_file.required' => 'Import field is required',
            'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
        ]);
        $userId = $request->user;
        $directory = public_path('uploads/import');
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $originalName = $request->file('import_file')->getClientOriginalName();
        $file = $request->file('import_file');
        $filePath = $file->getPathname();

        // Read the CSV file
        $data = array_map('str_getcsv', file($filePath));
        $headers = array_shift($data); // Extract headers from the first row

        $batchSize = $request->splitNumber ?? 0; // Default batchSize to 0 if not provided
        $batchNumber = $request->batchNumber ?? 101; // Default batch number to 101

        if ($batchSize == 0) {
            // If batchSize is 0, process the full CSV without batching
            $fullFileName = $directory . "/{$userId}_{$originalName}_full.csv";

            if (file_exists($fullFileName)) {
                Log::warning("File already exists: " . $fullFileName);
                return response()->json([
                    'status' => 'error',
                    'message' => "File already exists. Please try again.",
                ], 400);
            }

            $fileHandle = fopen($fullFileName, 'w');
            fputcsv($fileHandle, $headers);

            foreach ($data as $row) {
                fputcsv($fileHandle, $row);
            }

            fclose($fileHandle);

            Log::info("Full CSV file created: " . $fullFileName);

            return response()->json([
                'status' => 'success',
                'message' => "Full CSV processed and stored successfully.",
            ], 200);
        }

        $totalRows = count($data);
        $totalBatches = ceil($totalRows / $batchSize); // Calculate total batches needed

        Log::info("Total Rows: " . $totalRows); // Log total rows
        Log::info("Total Batches: " . $totalBatches); // Log total batches


        // Iterate over data and create batches
        for ($i = 0; $i < $totalRows; $i += $batchSize) {
            $batchData = array_slice($data, $i, $batchSize);

            foreach ($batchData as &$row) {
                $row[] = $batchNumber;
            }

            if (!in_array('batch_no', $headers)) {
                $headers[] = 'batch_no';
            }

            $batchFileName = $directory . "/{$userId}_{$originalName}_{$batchNumber}.csv";

            if (file_exists($batchFileName)) {
                Log::warning("File already exists: " . $batchFileName);
                continue;
            }

            $fileHandle = fopen($batchFileName, 'w');
            fputcsv($fileHandle, $headers);

            foreach ($batchData as $row) {
                fputcsv($fileHandle, $row);
            }

            fclose($fileHandle);

            Log::info("Batch {$batchNumber} created with " . count($batchData) . " rows.");

            $batchNumber++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "CSV processed and stored in batches successfully.",
        ], 200);
    }






    // // this work but not saved batch_no
    // public function storeInventory(Request $request)
    // {
    //     $request->validate([
    //         'user' => 'required',
    //         'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
    //     ], [
    //         'user.required' => 'User field is required',
    //         'import_file.required' => 'Import field is required',
    //         'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
    //     ]);

    //     // Store the file in the uploads directory
    //     $userId = $request->user;
    //     $directory = public_path('uploads/import');
    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     $originalFileName = $request->file('import_file')->getClientOriginalName();
    //     $modifiedFileName = $userId.'_'.date('mdY') . '_' . $originalFileName;
    //     $filePath = $request->file('import_file')->move($directory, $modifiedFileName);

    //     // Read the CSV file using League\Csv
    //     $csv = Reader::createFromPath($filePath, 'r');
    //     $csv->setHeaderOffset(0); // Assuming the first row contains headers

    //     $rows = $csv->getRecords(); // Get records as associative arrays
    //     $batchSize = 10; // Define the batch size
    //     $batchNumber = 1;
    //     $batches = [];
    //     $batchData = [];

    //     foreach ($rows as $row) {
    //         $batchData[] = $row;
    //         // Process the batch when it reaches the defined size
    //         if (count($batchData) === $batchSize) {
    //             $this->processBatch($batchData, $userId, $batchNumber);
    //             $batches[] = $batchData;
    //             $batchData = [];
    //             $batchNumber++;
    //         }
    //     }

    //     // Process any remaining rows
    //     if (!empty($batchData)) {
    //         $this->processBatch($batchData, $userId, $batchNumber);
    //         $batches[] = $batchData;
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => "CSV file processed in batches and saved successfully.",
    //     ], 200);
    // }

    // private function processBatch(array $batchData, $userId, $batchNumber)
    // {
    //     // // Save to database
    //     // foreach ($batchData as $row) {
    //     //     DB::table('your_table_name')->insert([
    //     //         'user_id' => $userId,
    //     //         'data_column_1' => $row['Column1'], // Replace 'Column1' with your actual column name
    //     //         'data_column_2' => $row['Column2'], // Add more columns as needed
    //     //         'created_at' => now(),
    //     //         'updated_at' => now(),
    //     //     ]);
    //     // }

    //     // Save batch as a CSV file
    //     $directory = public_path("uploads/import");
    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     $batchFilePath = $directory . "/batch_{$batchNumber}_user_{$userId}.csv";
    //     $csvWriter = Writer::createFromPath($batchFilePath, 'w+');
    //     $csvWriter->insertOne(array_keys($batchData[0])); // Insert header
    //     $csvWriter->insertAll($batchData); // Insert rows
    // }


    public function storeCSVInventory(Request $request)
    {

        //// abort_if(! auth()->user()->can('hrm_bulk_attendance_import_store'), 403, 'Access forbidden');
        $request->validate([
            'csvFileName' => 'required',
        ], [
            'csvFileName.required' => 'CSV File is required',
        ]);

        // move file in local path set return here
        $modifiedFileName = $request->csvFileName;
        $directory = public_path('uploads/import');
        $filePath = $directory . '/' . $modifiedFileName;
        
        $userId = Auth::user()->id;
        $fileRows = Excel::toArray([], $filePath)[0];
        
        if (empty($fileRows)) {
            return response()->json(['status' => 'error', 'message' => 'CSV file is empty'], 422);
        }

        $headers = array_map('strtolower', array_map('trim', $fileRows[0]));
        $file_rows = Excel::toArray([], $filePath)[0];

        $existingInventoryByDealer = MainInventory::select('deal_id', 'vin')
                                    ->whereNotNull('vin')
                                    ->get()
                                    ->groupBy('deal_id')
                                    ->map(fn ($inventory) => $inventory->pluck('vin')->toArray());
        
        $inventory_sold = [];
        $inventory_added = [];
        $csvVINsByDealer = [];

        $tmp_imported = [];
        $tmp_updated = [];
        $tmp_skipped = [];

        $imported = [];
        $updated = [];
        $skipped = [];

        $inventory = MainInventory::with('dealer')->select('*')->get();   //vin not mention yegt
        $latestBatchNo = MainInventory::latest('batch_no')->value('batch_no');
        $batch_no = $latestBatchNo ? $latestBatchNo + 1 : 101;

        if (!empty($file_rows)) {
            foreach (array_slice($file_rows, 1) as $index => $row) {
                $rowData = array_combine($headers, $row);

                $index = $index + 1;
                // Example: Access data by header names
                $dealer_id_data = $rowData['dealer id'] ?? $rowData['customer id'] ?? null ;
                $dealer_type_data = $rowData['dealer type'] ?? null;
                $dealer_name_data = $rowData['dealer name'] ?? null;
                $dealer_full_address_data = $rowData['dealer address'] ?? null;
                $dealer_address_data = $rowData['dealer street'] ?? null;
                $city_info = $rowData['dealer city'] ??  null;
                $state_info = $rowData['dealer region'] ?? null;
                $zip_info = $rowData['dealer zip code'] ?? null;

                $phone_digitsOnly = preg_replace('/\D/', '', $rowData['dealer sales phone']);
                $dealerPhone = $phone_digitsOnly ??  null;
                $dealer_rating= $rowData['dealer rating'] ?? null;
                $dealer_review = $rowData['dealer review'] ?? null;

                
                $dealer_website = $rowData['dealer website'] ?? null;
                $seller_note = $rowData['seller note'] ?? null;
                $brand_website= $rowData['brand website'] ?? null;
                $source_url = $rowData['source_url'] ?? null;
                $titles_data = $rowData['titles'] ?? null;
                $trim_data = $rowData['trim name'] ?? null;
                $make_data = $rowData['make'] ?? null;
                $model_data = $rowData['model'] ?? null;
                $exterior_color_data = $rowData['exterior color'] ?? null;
                $interior_color_data = $rowData['interior color'] ?? null;
                
                // Price
                $price_data = $rowData['price'] ?? 0;
                $price_digitsOnly = preg_replace('/\D/', '', $price_data);
                $price_rating = $rowData['price rating'] ?? null;

                // Mileage
                $mileage_data = $rowData['mileage'] ?? 0;
                $cus_mileage = preg_replace('/\D/', '', $mileage_data);
                $fuel_data = $rowData['fuel'] ?? null;

                $city_mpg_data = $rowData['city mpg'] ?? null;
                $hwy_mpg_data = $rowData['hwy mpg'] ?? null;
                $engine_data = $rowData['engine'] ?? null;

                $transmission_data = $rowData['transmission'] ?? null;
                $year_data = $rowData['year'] ?? null;
                $type_data = $rowData['type'] ?? null;
                $stock_num_data = $rowData['stock number'] ?? null;
                $vin = $rowData['vin'] ?? null;
                $body_type_data = $rowData['body type'] ?? null;
                $feature = $rowData['feature'] ?? null;
                $dealer_option_info = $rowData['option'] ?? null;
                $drive_train_data = $rowData['drive train'] ?? null;
                $price_history_data = $rowData['price history'] ?? null;
                $primary_image_data = $rowData['primary image'] ?? null;
                $all_images_data = $rowData['all images'] ?? $rowData['all image'] ?? null;
                $vin_image_data = $rowData['vin image']  ?? null;
                $batch_no_data = $rowData['batch_no'] ?? $batch_no;
                $inventory_status = $rowData['inventory_status'] ?? $rowData['inventory status'] ?? null;

                //no need last csv
                $avg_mpg_data = $rowData['avg_mpg'] ?? null;
                $salesstatus_data = $rowData['salesstatus'] ?? null;
                $days_on_market_data = $rowData['days on market'] ?? null;

                $dataCollection = collect([
                    'dealer_id' => $dealer_id_data ?? null,
                    'dealer_name' => $dealer_name_data ?? null,
                    'dealer_full_address' => $dealer_full_address_data ?? null,
                    'dealer_address' => $dealer_address_data ?? null,
                    'dealer_city' => $city_info ?? null,
                    'dealer_region' => $state_info ?? null,
                    'dealer_zip' => $zip_info ?? null,
                    'dealer_phone' => $dealerPhone ?? null,
                    'dealer_rating' => $dealer_rating ?? null,
                    'dealer_review' => $dealer_review ?? null,
                    'dealer_website' => $dealer_website ?? null,
                    'seller_note' => $seller_note ?? null,
                    'brand_website' => $brand_website ?? null,
                    'source_url' => $source_url ?? null,
                    'dealer_option' => $dealer_option_info ?? null,
                    'titles' => $titles_data ?? null,
                    'trim' => $trim_data ?? null,
                    'make' => $make_data ?? null,
                    'model' => $model_data ?? null,
                    'exterior_color' => $exterior_color_data ?? null,
                    'interior_color' => $interior_color_data ?? null,
                    'price' => isset($price_digitsOnly) ? (int)$price_digitsOnly : 0,
                    'price_rating' => $price_rating ?? null,
                    'milage' => isset($cus_mileage) ? (int)$cus_mileage : 0,
                    'fuel' => $fuel_data ?? null,
                    'city_mpg_data' => $city_mpg_data ?? null,
                    'hwy_mpg_data' => $hwy_mpg_data ?? null,
                    'engine' => $engine_data ?? null,
                    'transmission' => $transmission_data ?? null,
                    'year' => $year_data ?? null,
                    'type' => $type_data ?? null,
                    'stock_number' => $stock_num_data ?? null,
                    'vin' => $vin ?? null,
                    'body_type' => $body_type_data ?? null,
                    'feature' => $feature ?? null,
                    'drive_train' => $drive_train_data ?? null,
                    'price_history_data' => $price_history_data ?? null,
                    'primary_image' => $primary_image_data ?? null,
                    'all_images' => $all_images_data ?? null,
                    'vin_image_data' => $vin_image_data ?? null,
                    'batch_no_data' => $batch_no_data ?? null,

                    // 'dealer_no' => $rowData['dealer no'] ?? null,
                    'avg_mpg' => $avg_mpg_data ?? null,
                    'sales_status' => $rowData['salesstatus'] ?? null,
                    'days_on_market' => $rowData['days on market'] ?? null,
                    'inventory_status' => $inventory_status,
                ]);

                $latlongData = $this->getLatLong($dataCollection['dealer_zip']);
                
                $dealer = User::where('name', $dealer_name_data)->where('zip', $zip_info)->first();

                if ($dealer) {
                    $dealerId = $dealer->dealer_id;
                    $csvVINsByDealer[$dealerId][] = $vin;

                    if($dealer->rating == null && $dealer_rating){
                        $dealer->rating = $dealer_rating;
                        $dealer->review = $dealer_review;
                        $dealer->save();
                    }
                    $dealer_id = $dealer->dealer_id;
                    $user_id = $dealer->id;
                }else{

                    $custom_dealer_id = $dealer_id_data;

                    $user = new User();
                    $user->dealer_id           = $custom_dealer_id;
                    $user->name                = $dealer_name_data;
                    $user->phone               = $dealerPhone;
                    $user->dealer_full_address = $dealer_full_address_data;
                    $user->rating              = $dealer_rating;
                    $user->review              = $dealer_review;
                    $user->address             = $dealer_address_data;
                    $user->dealer_website      = $dealer_website;
                    $user->brand_website       = $brand_website;
                    $user->city                = $city_info;
                    $user->state               = $state_info;
                    $user->zip                 = $zip_info;
                    $user->status              = 3;
                    $user->dealer_iframe_map   =  null;
                    $user->save();

                    $user_id = $user->id;
                    $dealer_id = $user->dealer_id;

                    $role = 'dealer';
                    $role =  Role::where('name', $role)->first();
                    // $role = Role::find(2);
                    if (!$role) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "The specified role does not exist or is not for the 'web' guard.",
                        ], 422);
                    }
                    $user->assignRole($role);

                }

                $errors = [];
                $saveDir = public_path('listing/' . $dataCollection['vin']);
                $this->ensureDirectoryExists($saveDir);
        
                $fileNameCustom = strtolower($dataCollection['year'].'_'.$dataCollection['make'].'_'.$dataCollection['model'].'-pic-').$dataCollection['vin'];
                $localImagePaths = $this->processImages($dataCollection['all_images'], $saveDir, $dataCollection['vin'], $fileNameCustom);
                $localImagePathsString = implode(',', $localImagePaths);

                $formattedDate = now()->format('Y-m-d');
                $monthlyPayment = $this->calculateMonthlyPayment($dataCollection['price']);
                $carBody = $this->determineCarBody(strtolower($dataCollection['body_type']));
                
                // $vehicleMakeData = VehicleMake::where('make_name', $dataCollection['make'])->first();
                // $vehicleMakeDataID = $vehicleMakeData->id;
                $vehicleMakeData = VehicleMake::firstOrCreate(
                    ['make_name' => $dataCollection['make']], // Search criteria
                    ['status' => 1, 'is_read' => 0] // Default values if a new record is created
                );
                
                $vehicleMakeDataID = $vehicleMakeData->id;
                
                // // Check if VIN exists in main_inventories
                // $tmp_inventory = TmpInventories::where('vin', $rowData['vin'])->first();
                
                // if ($tmp_inventory) {
                //         // Update main_inventory
                //         $changes = $this->updateTmpInventory($tmp_inventory, $dataCollection, $localImagePathsString, $vehicleMakeDataID, $monthlyPayment, $carBody);
                //         if (!empty($changes)) {
                //             $tmp_updated[] = [
                //                 'vin' => $inventory->vin,
                //                 'title' => $inventory->title,
                //                 'changes' => $changes,
                //             ];
                //         }
                // } else {
                //         // Insert new inventory
                //         try {
                //             // $this->insertTmpInventory($rowData);
                //             $dato = $this->insertTmpInventory($row, $userId, $dealer_id, $user_id, $dataCollection, $filePath, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate,$monthlyPayment,$carBody);
                            
                //             $tmp_imported[] = [
                //                 'vin' => $rowData['Vin'],
                //                 'title' => $rowData['Title'],
                //             ];
                //         } catch (\Exception $e) {
                //             $failed[] = $row; // Log failed rows
                //         }
                // }


                $mainInventory = MainInventory::with('mainPriceHistory','additionalInventory')->where('vin', $rowData['vin'])->first();

                if ($mainInventory) {

                        // Update main_inventory
                        $changes = $this->updateInventory($mainInventory, $dataCollection, $localImagePathsString, $vehicleMakeDataID, $monthlyPayment, $carBody);

                        if (!empty($changes)) {
                            $updated[] = [
                                'vin' => $inventory->vin,
                                'title' => $inventory->title,
                                'changes' => $changes,
                            ];
                        }
                } else {
                        // Insert new inventory
                        try {
                            $dd = $this->insertInventory($row, $userId, $dealer_id, $user_id, $dataCollection, $filePath, $latlongData, $localImagePathsString, $vehicleMakeDataID, $formattedDate,$monthlyPayment,$carBody);

                            $inserted[] = [
                                'vin' => $rowData['vin'],
                                'title' => $rowData['title'],
                            ];
                        } catch (\Exception $e) {
                            $failed[] = $row; // Log failed rows
                        }
                }


            }
        }


    }
    
    public function storeCSVInventory2(Request $request)
    {
        //// abort_if(! auth()->user()->can('hrm_bulk_attendance_import_store'), 403, 'Access forbidden');
        $request->validate([
            'csvFileName' => 'required',
        ], [
            'csvFileName.required' => 'CSV File is required',
        ]);



        // move file in local path set return here
        $modifiedFileName = $request->csvFileName;
        $directory = public_path('uploads/import');
        $filePath = $directory . '/' . $modifiedFileName;

        $fileRows = Excel::toArray([], $filePath)[0];

        if (empty($fileRows)) {
            return response()->json(['status' => 'error', 'message' => 'CSV file is empty'], 422);
        }
        
        $headers = array_map('strtolower', array_map('trim', $fileRows[0]));
        
        $file_rows = Excel::toArray([], $filePath)[0];
        
        
        $existingInventoryByDealer = MainInventory::select('deal_id', 'vin')
                                                ->whereNotNull('vin')
                                                ->get()
                                                ->groupBy('dealer_id')
                                                ->map(fn ($inventory) => $inventory->pluck('vin')->toArray());
        


        $inventory_sold = [];
        $inventory_added = [];
        $csvVINsByDealer = [];

        $inventory = MainInventory::with('dealer')->select('*')->get();   //vin not mention yegt
        $latestBatchNo = MainInventory::latest('batch_no')->value('batch_no');
        $batch_no = $latestBatchNo ? $latestBatchNo + 1 : 101;
        // $headers = array_map('strtolower', array_map('trim', $file_rows[0]));

        if (!empty($file_rows)) {
            foreach (array_slice($file_rows, 1) as $index => $row) {
                $rowData = array_combine($headers, $row);

                $index = $index + 1;
                // Example: Access data by header names
                $dealer_id_data = $rowData['dealer id'] ?? $rowData['customer id'] ?? null ;
                $dealer_type_data = $rowData['dealer type'] ?? null;
                $dealer_name_data = $rowData['dealer name'] ?? null;
                $dealer_full_address_data = $rowData['dealer address'] ?? null;
                $dealer_address_data = $rowData['dealer street'] ?? null;

                // if ($dealer_address_data) {
                //     // Split the address into parts by commas
                //     $dealer_address_explode_data = explode(',', $dealer_address_data);

                //     // Ensure the second part exists before accessing it
                //     if (isset($dealer_address_explode_data[1])) {
                //         // Extract the last word (city) from the second part
                //         $lastSpacePosition = strrpos($dealer_address_explode_data[0], ' '); // Find the last space
                //         $city_info = trim(substr($dealer_address_explode_data[0], $lastSpacePosition + 1));
                //         // Split the second part by spaces for state and zip
                //         $dealer_city_explode_data = explode(' ', trim($dealer_address_explode_data[1]));

                //         $state_info = $dealer_city_explode_data[0] ?? null; // State
                //         $zip_info = $dealer_city_explode_data[1] ?? null;   // Zip
                //     } else {
                //         $city_info = $state_info = $zip_info = null; // Default if second part doesn't exist
                //     }
                // } else {
                //     $city_info = $state_info = $zip_info = null; // Default if address is null
                // }

                // // // Debugging Output
                // // dd([
                // //     'city' => $city_info,
                // //     'state' => $state_info,
                // //     'zip' => $zip_info,
                // // ]);
                // dd($rowData , ('Toyota of Cedar Park' == $dealer_name_data));
                // dd($city_info,$state_info,$zip_info,$dealer_city_explode_data[0],$dealer_city_explode_data[1], $dealer_address_explode_data);
                $city_info = $rowData['dealer city'] ??  null;
                $state_info = $rowData['dealer region'] ?? null;
                $zip_info = $rowData['dealer zip code'] ?? null;

                $phone_digitsOnly = preg_replace('/\D/', '', $rowData['dealer sales phone']);
                $dealerPhone = $phone_digitsOnly ??  null;
                $dealer_rating= $rowData['dealer rating'] ?? null;
                $dealer_review = $rowData['dealer review'] ?? null;
                
                $dealer_website = $rowData['dealer website'] ?? null;
                $seller_note = $rowData['seller note'] ?? null;
                $brand_website= $rowData['brand website'] ?? null;
                $source_url = $rowData['source_url'] ?? null;
                $titles_data = $rowData['titles'] ?? null;
                $trim_data = $rowData['trim name'] ?? null;
                $make_data = $rowData['make'] ?? null;
                $model_data = $rowData['model'] ?? null;
                $exterior_color_data = $rowData['exterior color'] ?? null;
                $interior_color_data = $rowData['interior color'] ?? null;
                // Price
                $price_data = $rowData['price'] ?? 0;
                $price_digitsOnly = preg_replace('/\D/', '', $price_data);
                $price_rating = $rowData['price rating'] ?? null;

                // Mileage
                $mileage_data = $rowData['mileage'] ?? 0;
                $cus_mileage = preg_replace('/\D/', '', $mileage_data);
                $fuel_data = $rowData['fuel'] ?? null;

                $city_mpg_data = $rowData['city mpg'] ?? null;
                $hwy_mpg_data = $rowData['hwy mpg'] ?? null;
                $engine_data = $rowData['engine'] ?? null;
                
                $transmission_data = $rowData['transmission'] ?? null;
                $year_data = $rowData['year'] ?? null;
                $type_data = $rowData['type'] ?? null;
                $stock_num_data = $rowData['stock number'] ?? null;
                $vin = $rowData['vin'] ?? null;
                $body_type_data = $rowData['body type'] ?? null;
                $feature = $rowData['feature'] ?? null;
                $dealer_option_info = $rowData['option'] ?? null;
                $drive_train_data = $rowData['drive train'] ?? null;
                $price_history_data = $rowData['price history'] ?? null;
                $primary_image_data = $rowData['primary image'] ?? null;
                $all_images_data = $rowData['all images'] ?? $rowData['all image'] ?? null;
                $vin_image_data = $rowData['vin image']  ?? null;
                $batch_no_data = $rowData['batch_no'] ?? $batch_no;
                $inventory_status = $rowData['inventory_status'] ?? $rowData['inventory status'] ?? null;
                
                //no need last csv
                $avg_mpg_data = $rowData['avg_mpg'] ?? null;
                $salesstatus_data = $rowData['salesstatus'] ?? null;
                $days_on_market_data = $rowData['days on market'] ?? null;
                
                $dataCollection = collect([
                    'dealer_id' => $dealer_id_data ?? null,
                    'dealer_name' => $dealer_name_data ?? null,
                    'dealer_full_address' => $dealer_full_address_data ?? null,
                    'dealer_address' => $dealer_address_data ?? null,
                    'dealer_city' => $city_info ?? null,
                    'dealer_region' => $state_info ?? null,
                    'dealer_zip' => $zip_info ?? null,
                    'dealer_phone' => $dealerPhone ?? null,
                    'dealer_rating' => $dealer_rating ?? null,
                    'dealer_review' => $dealer_review ?? null,
                    'dealer_website' => $dealer_website ?? null,
                    'seller_note' => $seller_note ?? null,
                    'brand_website' => $brand_website ?? null,
                    'source_url' => $source_url ?? null,
                    'dealer_option' => $dealer_option_info ?? null,
                    'titles' => $titles_data ?? null,
                    'trim' => $trim_data ?? null,
                    'make' => $make_data ?? null,
                    'model' => $model_data ?? null,
                    'exterior_color' => $exterior_color_data ?? null,
                    'interior_color' => $interior_color_data ?? null,
                    'price' => isset($price_digitsOnly) ? (int)$price_digitsOnly : 0,
                    'price_rating' => $price_rating ?? null,
                    'milage' => isset($cus_mileage) ? (int)$cus_mileage : 0,
                    'fuel' => $fuel_data ?? null,
                    'city_mpg_data' => $city_mpg_data ?? null,
                    'hwy_mpg_data' => $hwy_mpg_data ?? null,
                    'engine' => $engine_data ?? null,
                    'transmission' => $transmission_data ?? null,
                    'year' => $year_data ?? null,
                    'type' => $type_data ?? null,
                    'stock_number' => $stock_num_data ?? null,
                    'vin' => $vin ?? null,
                    'body_type' => $body_type_data ?? null,
                    'feature' => $feature ?? null,
                    'drive_train' => $drive_train_data ?? null,
                    'price_history_data' => $price_history_data ?? null,
                    'primary_image' => $primary_image_data ?? null,
                    'all_images' => $all_images_data ?? null,
                    'vin_image_data' => $vin_image_data ?? null,
                    'batch_no_data' => $batch_no_data ?? null,

                    // 'dealer_no' => $rowData['dealer no'] ?? null,
                    'avg_mpg' => $avg_mpg_data ?? null,
                    'sales_status' => $rowData['salesstatus'] ?? null,
                    'days_on_market' => $rowData['days on market'] ?? null,
                    'inventory_status' => $inventory_status,
                ]);

                $dealer = User::where('name', $dealer_name_data)->where('zip', $zip_info)->first();

                if ($dealer) {
                    $dealerId = $dealer->dealer_id;
                    $csvVINsByDealer[$dealerId][] = $vin;

                    if($dealer->rating == null && $dealer_rating){
                        $dealer->rating = $dealer_rating;
                        $dealer->review = $dealer_review;
                        $dealer->save();
                    }

                }

                $inventoryItemFound = false;
                $newInventoryItemFound = true;
// djkhg ghudgh uidgu uguig uieygue rtuyeurtyuery ueryuery uty ereuryueryyttyuertuet
                // foreach ($inventory as $inventoryDetails) {
                //     // Check if VIN matches
                //     if ($inventoryDetails['vin'] == $vin) {
                //         $inventoryItemFound = true;
                //     }

                //     // Check if stock matches
                //     if ($inventoryDetails['stock'] == $stock_num_data) {
                //         $newInventoryItemFound = false;
                //     }

                //     // If both conditions are determined, break the loop
                //     if ($inventoryItemFound && !$newInventoryItemFound) {
                //         break;
                //     }
                // }

                // Take action based on whether inventoryItemFound and newInventoryItemFound
                if (!$inventoryItemFound) {
                    // dd($inventoryDetails);
                    // Inventory::where('vin', $inventoryDetails['vin'])->update(['status' => 0]);
                }
//   spofjiowe iwriweiuiwu ri wuriu weirw iwer wieriwruwit wyrwyr wuryweuruwertuweru wrtuweyrt wuryweuirwuirw rweuy
                if ($newInventoryItemFound) {
                    $inventory_added[] = $vin;

                    // $dealer = User::where('phone', $dealerPhone)->first();
                    $dealer = User::where('name', $dealer_name_data)->where('zip', $zip_info)->first();


                    if ($dealer) {
                        $dealer_id = $dealer->dealer_id;
                        $user_id = $dealer->id;
                    } else {
                        $custom_dealer_id = $dealer_id_data;


                        if (!$dealer && !empty($dealerPhone)) {
                            $user = new User();
                            $user->dealer_id           = $custom_dealer_id;
                            $user->name                = $dealer_name_data;
                            $user->phone               = $dealerPhone;
                            $user->dealer_full_address = $dealer_full_address_data;
                            $user->rating              = $dealer_rating;
                            $user->review              = $dealer_review;
                            $user->address             = $dealer_address_data;
                            $user->dealer_website      = $dealer_website;
                            $user->brand_website       = $brand_website;
                            $user->city                = $city_info;
                            $user->state               = $state_info;
                            $user->zip                 = $zip_info;
                            $user->status              = 3;
                            $user->dealer_iframe_map   =  null;
                            $user->save();

                            $user_id = $user->id;
                            $dealer_id = $user->dealer_id;

                            $role = 'dealer';
                            $role =  Role::where('name', $role)->first();
                            // $role = Role::find(2);
                            if (!$role) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => "The specified role does not exist or is not for the 'web' guard.",
                                ], 422);
                            }
                            $user->assignRole($role);
                        }
                    }
                    $userId = (int) substr(explode('_', $request->csvFileName)[0], 0, 1);
                    $dato = $this->createTmpInventory($row, $userId, $dealer_id, $user_id, $dataCollection, $filePath);
                    // dd($dato);
                }
            }
        }
        foreach ($existingInventoryByDealer as $dealerId => $vinList) {
            if (isset($csvVINsByDealer[$dealerId])) {
                $missingVINs = array_diff($vinList, $csvVINsByDealer[$dealerId]);

                foreach ($missingVINs as $vin) {
                    Inventory::where('dealer_id', $dealerId)
                        ->where('vin', $vin)
                        ->update(['status' => 0]);

                    $inventory_sold[] = $vin;
                }
            }
        }

        $totalAdded = count($inventory_added);
        $totalSold = count($inventory_sold);
        return response()->json(['add' => $inventory_added, 'total_add' => $totalAdded, 'sold' => $inventory_sold, 'total_sold' => $totalSold]);
    }

    private function createTmpInventory(array $row, int $id, $dealer_id, int $user_id, $dataCollection, $filePath)
    {
        // if (empty($dataCollection['all_images']) || empty($dataCollection['all_image'])) {
        //     throw new \Exception('No images found in dataCollection (both "all_images" and "all_image" are empty).');
        // }

        // // Use the available key
        // $images = !empty($dataCollection['all_images']) ? $dataCollection['all_images'] : $dataCollection['all_image'];

        $errors = [];
        $saveDir = public_path('listing/' . $dataCollection['vin']);
        $this->ensureDirectoryExists($saveDir);

        $fileNameCustom = strtolower($dataCollection['year'].'_'.$dataCollection['make'].'_'.$dataCollection['model'].'-pic-').$dataCollection['vin'];
        $localImagePaths = $this->processImages($dataCollection['all_images'], $saveDir, $dataCollection['vin'], $fileNameCustom);
        $localImagePathsString = implode(',', $localImagePaths);

        $formattedDate = now()->format('Y-m-d');
        $monthlyPayment = $this->calculateMonthlyPayment($dataCollection['price']);

        $carBody = $this->determineCarBody(strtolower($dataCollection['body_type']));

        $vehicleMakeData = VehicleMake::where('make_name', $dataCollection['make'])->first();

        $inventory = MainInventory::where('vin', $dataCollection['vin'])->first();
        $tmpInventory = TmpInventories::where('vin', $dataCollection['vin'])->first();
        // if($inventory == null ){
        //     dd($dataCollection);
        // }
        if (!$inventory) {
            $makeId = $vehicleMakeData ? $vehicleMakeData->id : $this->createVehicleMake($dataCollection['make']);

            $latlongData = $this->getLatLong($dataCollection['dealer_zip']);

            $dealerId = $dealer_id ?? $dataCollection['dealer_id'];
            $mileage = $this->getCleanMileage($dataCollection['milage']);

            $inventoryData = $this->prepareInventoryData(
                $dataCollection,
                $dealerId,
                $user_id,
                $makeId,
                $latlongData,
                $localImagePathsString,
                $monthlyPayment,
                $carBody,
                $formattedDate,
                $mileage
            );

            $isSuccessful = $this->saveInventoryData($inventoryData, $tmpInventory, $dataCollection, $user_id);

            if ($isSuccessful) {
                $this->handleFileAfterProcessing($filePath);
            }
        }
    }


    // private function processImages($imageString, $saveDir, $vin, $fileNameCustom)
    // {
    //     $imageUrls = array_slice(explode(',', $imageString), 0, 5); // Limit to first 5 images
    //     $localImagePaths = [];

    //     foreach ($imageUrls as $index => $url) {
    //         // Create a consistent filename for each image
    //         $fileName = str_replace([' ', '/','+'], '-', $fileNameCustom) . '_' . ($index + 1) . '.jpg';
    //         $localPath = $saveDir . '/' . $fileName; // Determine where to save the image locally

    //         // Check if the file already exists
    //         if (File::exists($localPath)) {
    //             Log::info("Image already exists, skipping download: $localPath");
    //             $localImagePaths[] = 'listing/'. $vin. "/" . $fileName; // Add existing path to the array
    //             continue; // Skip downloading and move to the next image
    //         }

    //         // Attempt to download the file if it does not exist
    //         try {
    //             $response = Http::get($url);
    //             if ($response->successful()) {
    //                 File::put($localPath, $response->body()); // Save the file locally
    //                 Log::info("Successfully downloaded image: $url");
    //             } else {
    //                 Log::warning("Failed to download image: $url");
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Error downloading image $url: " . $e->getMessage());
    //         }

    //         sleep(rand(4, 6)); // Optional delay to avoid server overload

    //         // Add the downloaded file's local path to the array
    //         $localImagePaths[] = 'listing/'. $vin . "/" . $fileName;
    //     }

    //     return $localImagePaths; // Return all processed local image paths
    // }

   // // it also work as a num 1,2,3,4,5

    // private function processImages($imageString, $saveDir, $vin)
    // {
    //     $imageUrls = explode(',', $imageString); // All image URLs
    //     $localImagePaths = [];

    //     // Ensure the directory exists
    //     $vinDir = $saveDir . '/' . $vin;
    //     if (!File::exists($vinDir)) {
    //         File::makeDirectory($vinDir, 0755, true); // Create the directory if it doesn't exist
    //     }

    //     // Process exactly 10 images
    //     for ($index = 1; $index <= 10; $index++) {
    //         // Get the corresponding URL or set it to null if it doesn't exist
    //         $url = $imageUrls[$index - 1] ?? null;
    //         $fileName = $vin . '_' . $index . '.jpg'; // Fixed filename format
    //         $localPath = $vinDir . '/' . $fileName; // Full path to save the image

    //         // If the file already exists, skip downloading
    //         if (File::exists($localPath)) {
    //             Log::info("Image already exists, skipping download: $localPath");
    //             $localImagePaths[] = 'listing/' . $vin . '/' . $fileName; // Add existing path to the array
    //             continue; // Move to the next image
    //         }

    //         // // Download only if URL is valid
    //         // if ($url) {
    //         //     try {
    //         //         $response = Http::get($url);
    //         //         if ($response->successful()) {
    //         //             File::put($localPath, $response->body()); // Save the file locally
    //         //             Log::info("Successfully downloaded image: $url");
    //         //         } else {
    //         //             Log::warning("Failed to download image: $url");
    //         //         }
    //         //     } catch (\Exception $e) {
    //         //         Log::error("Error downloading image $url: " . $e->getMessage());
    //         //     }

    //         //     sleep(rand(4, 6)); // Optional delay to avoid server overload
    //         // } else {
    //         //     Log::warning("No URL provided for image index: $index");
    //         // }

    //         // Add the local path (even if not downloaded) to the array
    //         $localImagePaths[] = 'listing/' . $vin . '/' . $fileName;
    //     }

    //     return $localImagePaths; // Return all processed local image paths
    // }


    // // image store all time 10
    // private function processImages($imageString, $saveDir, $vin)
    // {
    //     $imageUrls = explode(',', $imageString); // All image URLs
    //     $localImagePaths = [];

    //     // Ensure the directory exists
    //     $vinDir = $saveDir . '/' . $vin;
    //     if (!File::exists($vinDir)) {
    //         File::makeDirectory($vinDir, 0755, true); // Create the directory if it doesn't exist
    //     }

    //     // Process exactly 10 images
    //     for ($index = 1; $index <= 10; $index++) {
    //         // Get the corresponding URL or set it to null if it doesn't exist
    //         $url = $imageUrls[$index - 1] ?? null;
    //         $fileName = sprintf('%s_%02d.jpg', $vin, $index); // Two-digit filename format
    //         $localPath = $vinDir . '/' . $fileName; // Full path to save the image

    //         // If the file already exists, skip downloading
    //         if (File::exists($localPath)) {
    //             Log::info("Image already exists, skipping download: $localPath");
    //             $localImagePaths[] = 'listing/' . $vin . '/' . $fileName; // Add existing path to the array
    //             continue; // Move to the next image
    //         }

    //         // Download only if URL is valid
    //         // if ($url) {
    //         //     try {
    //         //         $response = Http::get($url);
    //         //         if ($response->successful()) {
    //         //             File::put($localPath, $response->body()); // Save the file locally
    //         //             Log::info("Successfully downloaded image: $url");
    //         //         } else {
    //         //             Log::warning("Failed to download image: $url");
    //         //         }
    //         //     } catch (\Exception $e) {
    //         //         Log::error("Error downloading image $url: " . $e->getMessage());
    //         //     }

    //         //     sleep(rand(4, 6)); // Optional delay to avoid server overload
    //         // } else {
    //         //     Log::warning("No URL provided for image index: $index");
    //         // }

    //         // Add the local path (even if not downloaded) to the array
    //         $localImagePaths[] = 'listing/' . $vin . '/' . $fileName;
    //     }

    //     return $localImagePaths; // Return all processed local image paths
    // }






    // private function processImages($imageString, $saveDir, $vin)
    // {
    //     $imageUrls = array_slice(explode(',', $imageString), 0, 5);
    //     $localImagePaths = [];

    //     foreach ($imageUrls as $url) {
    //         $fileName = basename(trim($url));
    //         $localPath = $saveDir . '/' . $fileName;

    //         if (!File::exists($localPath)) {
    //             try {
    //                 $response = Http::get($url);
    //                 if ($response->successful()) {
    //                     File::put($localPath, $response->body());
    //                 } else {
    //                     Log::warning("Failed to download image: $url");
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error("Error downloading image $url: " . $e->getMessage());
    //             }
    //             sleep(rand(4, 6));
    //         }

    //         // $localImagePaths[] = asset("frontend/uploads/autotrader/$vin/$fileName");
    //         $localImagePaths[] = "uploads/".$vin."/".$fileName;
    //     }

    //     return $localImagePaths;
    // }



    private function createVehicleMake($makeName)
    {
        return VehicleMake::create(['make_name' => $makeName])->id;
    }


    private function getCleanMileage($mileage)
    {
        return trim(str_replace('mi', '', $mileage)) === 'N/A' ? 0 : $mileage;
    }

    private function prepareInventoryData($dataCollection, $dealerId, $userId, $makeId, $latlongData, $localImagePathsString, $monthlyPayment, $carBody, $formattedDate, $mileage)
    {

        return [
            'dealer_id' => $dealerId,//
            'deal_id' => $userId,//
            'zip_code' => $dataCollection['dealer_zip'],//
            'latitude' => $latlongData->latitude,//
            'longitude' => $latlongData->longitude,//
            'detail_url' => $dataCollection['source_url'],//
            'img_from_url' => $dataCollection['all_images'],//
            'vin_image' => $dataCollection['vin_image_data'],//
            'local_img_url' => $localImagePathsString,//
            'vehicle_make_id' => $makeId,//
            'title' => $dataCollection['titles'],//
            'year' => $dataCollection['year'],//
            'make' => $dataCollection['make'],//
            'model' => $dataCollection['model'],//
            'vin' => $dataCollection['vin'],//
            'price' => $dataCollection['price'],//
            'price_rating' => $dataCollection['price_rating'],//
            'miles' => $mileage,//
            'type' => $dataCollection['type'],//
            'trim' => $dataCollection['trim'],//
            'stock' => $dataCollection['stock_number'],//
            'engine_details' => $dataCollection['engine'],//
            'transmission' => $dataCollection['transmission'],//
            'body_description' => $carBody,//
            'vehicle_feature_description' => $dataCollection['dealer_option'],//
            'vehicle_additional_description' => $dataCollection['feature'],//
            'seller_note' => $dataCollection['seller_note'],//
            'fuel' => $dataCollection['fuel'],//
            'drive_info' => $dataCollection['drive_train'],//
            'mpg' => $dataCollection['avg_mpg'],//
            'mpg_city' => $dataCollection['city_mpg_data'],//
            'mpg_highway' => $dataCollection['hwy_mpg_data'],//
            'exterior_color' => $dataCollection['exterior_color'],//
            'interior_color' => $dataCollection['interior_color'],//
            'created_date' => $formattedDate,//
            'batch_no' => $dataCollection['batch_no_data'],//
            'stock_date_formated' => $formattedDate,//
            'user_id' => $userId,//
            'payment_price' => $monthlyPayment,//
            'body_formated' => $carBody,//
            'is_feature' => 0,//
            'status' => 1,//
            'inventory_status' => $dataCollection['inventory_status'],//
        ];
    }

    private function saveInventoryData($inventoryData, $tmpInventory, $dataCollection, $dealId)
    {

        // dd($dataCollection['price_history_data']);
        try {
            // $inventory = Inventory::create($inventoryData);
            // $inventoryId = $inventory->id;

            $mainInventoryData = $this->prepareMainInventoryData($inventoryData, $dealId);
            $mainInventory = MainInventory::create($mainInventoryData);
            $mainInventoryId = $mainInventory->id;
            
            // // Save data to the `additional_inventories` table
            $additionalInventoryData = $this->prepareAdditionalInventoryData($inventoryData, $mainInventoryId);
            AdditionalInventory::create($additionalInventoryData);

            // Extract and save price history
            if (!empty($dataCollection['price_history_data'])) {
                // $this->savePriceHistory($inventoryId, $dataCollection['price_history_data']);
                $this->savePriceHistory($mainInventoryId, $dataCollection['price_history_data']);
            }

            // price_history logic here
            if (!$tmpInventory) {
                TmpInventories::create($tmpInventory);
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error inserting inventory data: " . $e->getMessage());
            return false;
        }
    }

    private function handleFileAfterProcessing($filePath)
    {
        $newFilePath = public_path('uploads/taken/');
        $this->ensureDirectoryExists($newFilePath);

        try {
            $fileName = basename($filePath);
            $newFileFullPath = $newFilePath . $fileName;

            if (File::exists($filePath)) {
                File::move($filePath, $newFileFullPath);
                Log::info("File moved successfully to: $newFileFullPath");
            } else {
                Log::warning("Source file not found: $filePath");
            }
        } catch (\Exception $e) {
            Log::error("Error moving file $filePath: " . $e->getMessage());
        }
    }



    private function savePriceHistory($inventoryId, $priceHistoryRawData)
    {

        $entries = explode(',', $priceHistoryRawData);
        $priceHistoryData = [];

        foreach ($entries as $entry) {
            $parts = array_map('trim', explode(';', $entry));

            // Skip invalid entries
            if (in_array('----', $parts) || count($parts) !== 3) {
                continue;
            }

            // Parse date
            try {
                $date = Carbon::createFromFormat('m/d/y', $parts[0])->format('Y-m-d');
            } catch (\Exception $e) {
                Log::error("Invalid date format: " . $parts[0]);
                continue;
            }

            // Parse amount
            $amount = floatval(str_replace('$', '', $parts[2]));

            // Parse change_amount
            $changeAmount = $parts[1]; // Store as VARCHAR directly

            // Set status
            $status = 0; // Default is 0 (e.g., "Listed")
            if (strpos($parts[1], '+') !== false || strpos($parts[1], '-') !== false) {
                $status = 1; // Change detected, set to 1
            }
            // Add to bulk insert array
            $priceHistoryData[] = [
                'main_inventory_id' => $inventoryId,
                'change_date' => $date,
                'change_amount' => $changeAmount, // VARCHAR as-is
                'amount' => $amount, // DECIMAL(8,2)
                'status' => $status, // Primarily 0 unless conditionally updated
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        // dd((!empty($priceHistoryData)),$inventoryId,$date,$changeAmount,$amount,$status,$inventoryId, $priceHistoryRawData);

        // Bulk insert valid price history data
        if (!empty($priceHistoryData)) {
            // PriceHistory::insert($priceHistoryData);
            MainPriceHistory::insert($priceHistoryData);
        }
    }

    private function prepareMainInventoryData($inventoryData, $dealId)
    {
    // id`, `deal_id`, `zip_code`, `latitude`, `longitude`, `vehicle_make_id`, `title`, `year`, `make`, `model`, `vin`, 
    //`price`, `price_rating`, `miles`, `type`, `trim`, `stock`, `transmission`, `engine_details`, `fuel`, `drive_info`, 
    //`mpg`, `mpg_city`, `mpg_highway`, `exterior_color`, `interior_color`, `created_date`, `stock_date_formated`, 
    //`user_id`, `payment_price`,`body_formated`,  
    //`is_feature`, `is_lead_feature`, `package`, `payment_date`, `active_till`, `featured_till`, `is_visibility`, 
    //`batch_no`, `status`, `inventory_status`, 
    //`created_at`, `updated_at`, `deleted_at 

        return [
            'deal_id' => $dealId,
            'zip_code' => $inventoryData['zip_code'],
            'latitude' => $inventoryData['latitude'],
            'longitude' => $inventoryData['longitude'],
            'vehicle_make_id' => $inventoryData['vehicle_make_id'],

            'title' => $inventoryData['title'],
            'year' => $inventoryData['year'],
            'make' => $inventoryData['make'],
            'model' => $inventoryData['model'],
            'vin' => $inventoryData['vin'],
            'price' => $inventoryData['price'],
            'price_rating' => $inventoryData['price_rating'],
            'miles' => $inventoryData['miles'],
            'type' => $inventoryData['type'],
            'trim' => $inventoryData['trim'],
            'stock' => $inventoryData['stock'],
            'transmission' => $inventoryData['transmission'],
            'engine_details' => $inventoryData['engine_details'],
            'fuel' => $inventoryData['fuel'],
            'drive_info' => $inventoryData['drive_info'],
            'mpg' => $inventoryData['mpg'],
            'mpg_city' => $inventoryData['mpg_city'],
            'mpg_highway' => $inventoryData['mpg_highway'],
            'exterior_color' => $inventoryData['exterior_color'],
            'interior_color' => $inventoryData['interior_color'],
            'created_date' => $inventoryData['created_date'],
            'stock_date_formated' => $inventoryData['stock_date_formated'],
            'user_id' => $inventoryData['user_id'],
            'payment_price' => $inventoryData['payment_price'],
            'body_formated' => $inventoryData['body_formated'],
            'batch_no' => $inventoryData['batch_no'],
            'status' => $inventoryData['status'],
            'inventory_status' => $inventoryData['inventory_status'],
            // 'latitude' => $inventoryData['latitude'],
            // 'longitude' => $inventoryData['longitude'],
        ];
    }

    private function prepareAdditionalInventoryData($inventoryData, $mainInventoryId)
    {
        // ['main_inventory_id','detail_url','img_from_url','local_img_url','vehicle_feature_description','vehicle_additional_description','seller_note'];

        return [
            'main_inventory_id' => $mainInventoryId, // Link to the main inventory
            'detail_url' => $inventoryData['detail_url'],
            'img_from_url' => $inventoryData['img_from_url'],
            'local_img_url' => $inventoryData['local_img_url'],
            'vehicle_feature_description' => $inventoryData['vehicle_feature_description'],
            'vehicle_additional_description' => $inventoryData['vehicle_additional_description'],
            'seller_note' => $inventoryData['seller_note'],
        ];
    }


    // Insert inventory into multiple tables
    public function insertInventory(array $row, int $id, $dealer_id, int $user_id, $dataCollection, $filePath, $latlongData, $localImagePaths, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody)
    {

        try {
            // Insert into tmp_inventories
            $mainInventory = MainInventory::create([
                // 'dealer_id' => $dealer_id,
                'deal_id' => $user_id,
                'zip_code' => $dataCollection['dealer_zip'],
                'latitude' => $latlongData['latitude'],
                'longitude' => $latlongData['longitude'],
                'vehicle_make_id' => $vehicleMakeDataID,
                'title' => $dataCollection['titles'],
                'year' => $dataCollection['year'],
                'make' => $dataCollection['make'],
                'model' => $dataCollection['model'],
                'vin' => $dataCollection['vin'],
                'price' => $dataCollection['price'],
                'price_rating' => $dataCollection['price_rating'],
                'miles' => $dataCollection['milage'],
                'type' => $dataCollection['type'],
                'trim' => $dataCollection['trim'],
                'stock' => $dataCollection['stock_number'],
                'transmission' => $dataCollection['transmission'],
                'engine_details' => $dataCollection['engine'],
                'fuel' => $dataCollection['fuel'],
                'drive_info' => $dataCollection['drive_train'],
                'mpg' => $dataCollection['avg_mpg'],
                'mpg_city' => $dataCollection['city_mpg_data'],
                'mpg_highway' => $dataCollection['hwy_mpg_data'],
                'exterior_color' => $dataCollection['exterior_color'],
                'interior_color' => $dataCollection['interior_color'],
                'created_date' => $formattedDate,
                'stock_date_formated' => $formattedDate,
                'user_id' => $id,
                'payment_price' => $monthlyPayment,
                'body_formated' => $carBody,
                'is_feature' => 0,
                'batch_no' => $dataCollection['batch_no_data'],
                'status' => 1,

                // // 'detail_url' => $dataCollection['source_url'],
                // 'img_from_url' => $dataCollection['all_images'],
                // 'local_img_url' => $localImagePaths,
                // 'vehicle_feature_description' => $dataCollection['feature'],
                // 'vehicle_additional_description' => $dataCollection['dealer_option'],
                // 'seller_note' => $dataCollection['seller_note'],
                // 'price_history' => $dataCollection['price_history_data'],
                // 'inventory_status' => $dataCollection['inventory_status'],
            ]);

            $mainInventoryId = $mainInventory->id;

            $additionalInventory = AdditionalInventory::create([
                // 'dealer_id' => $dealer_id,
                'main_inventory_id' => $mainInventoryId, 
                'detail_url' => $dataCollection['source_url'],
                'img_from_url' => $dataCollection['all_images'],
                'local_img_url' => $localImagePaths,
                'vehicle_feature_description' => $dataCollection['feature'],
                'vehicle_additional_description' => $dataCollection['dealer_option'],
                'seller_note' => $dataCollection['seller_note'],
            ]);

            // ✅ Check if price history data exists
            if (!isset($dataCollection['price_history_data']) || empty($dataCollection['price_history_data'])) {
                // Log::warning('⚠️ No price history data found.');
                return;
            }

            $priceHistoryData = [];
            $entries = explode(',', $dataCollection['price_history_data']);

            foreach ($entries as $entry) {
                $parts = array_map('trim', explode(';', $entry));


                // 🚀 Skip invalid entries
                if (count($parts) !== 3 || in_array('----', $parts)) {
                    // Log::warning("⚠️ Skipping invalid price history entry", ['entry' => $entry]);
                    continue;
                }

                // ✅ Parse Date
                try {
                    $date = Carbon::createFromFormat('m/d/y', $parts[0])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Log::error("❌ Invalid date format: " . $parts[0]);
                    continue;
                }
                // ✅ Parse Change Amount
                $changeAmount = trim($parts[1]);

                if (!isset($parts[2])) {
                    // Log::error('❌ Price history parsing error: missing amount in entry', ['entry' => $entry]);
                    continue;
                }

                $rawAmount = trim($parts[2]); // Use parts[2] instead of parts[3]
                $cleanAmount = str_replace([',', '$', '+', '-'], '', $rawAmount);

                if (!is_numeric($cleanAmount)) {
                    // Log::error('❌ Invalid amount value', ['value' => $rawAmount]);
                    continue;
                }

                $amount = floatval($cleanAmount);
                // Log the parsed value to ensure it's correct
                // Log::info('Parsed amount: ' . $amount); // This should help debug

                // ✅ Determine status (1 = price changed, 0 = new listing)
                // $status = (strpos($changeAmount, '+') !== false || strpos($changeAmount, '-') !== false) ? 1 : 0;

                // ✅ Ensure `main_inventory_id` exists
                if (!isset($mainInventory) || empty($mainInventory)) {
                    // Log::error('❌ Missing main_inventory_id for price history.');
                    return;
                }

                // ✅ Add to bulk insert array
                $priceHistoryData[] = [
                    'main_inventory_id' => $mainInventoryId,
                    'change_date' => $date,
                    'change_amount' => $changeAmount,
                    'amount' => $amount,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // ✅ Bulk Insert Price History
            if (!empty($priceHistoryData)) {
                // DB::enableQueryLog();
                MainPriceHistory::insert($priceHistoryData);
                // Log::info('✅ Price history inserted successfully', ['query' => DB::getQueryLog()]);
            } else {
                // Log::warning('⚠️ No valid price history data to insert.');
            }

    
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error inserting tmp inventory: ' . $e->getMessage(), [
                'data' => $row,
                'error' => $e->getTraceAsString(),
            ]);
    
            // Return null or handle the error as per your application's requirement
            return null;
        }
    }


    // Insert inventory into multiple tables
    public function insertTmpInventory(array $row, int $id, $dealer_id, int $user_id, $dataCollection, $filePath, $latlongData, $localImagePaths, $vehicleMakeDataID, $formattedDate, $monthlyPayment, $carBody)
    {
        try {
            // Insert into tmp_inventories
            $tmpInventory = TmpInventories::create([
                'dealer_id' => $dealer_id,
                'deal_id' => $user_id,
                'zip_code' => $dataCollection['dealer_zip'],
                'latitude' => $latlongData['latitude'],
                'longitude' => $latlongData['longitude'],
                'detail_url' => $dataCollection['source_url'],
                'img_from_url' => $dataCollection['all_images'],
                'local_img_url' => $localImagePaths,
                'vehicle_make_id' => $vehicleMakeDataID,
                'title' => $dataCollection['titles'],
                'year' => $dataCollection['year'],
                'make' => $dataCollection['make'],
                'model' => $dataCollection['model'],
                'vin' => $dataCollection['vin'],
                'price' => $dataCollection['price'],
                'price_rating' => $dataCollection['price_rating'],
                'miles' => $dataCollection['milage'],
                'type' => $dataCollection['type'],
                'trim' => $dataCollection['trim'],
                'stock' => $dataCollection['stock_number'],
                'engine_details' => $dataCollection['engine'],
                'transmission' => $dataCollection['transmission'],
                'vehicle_feature_description' => $dataCollection['feature'],
                'vehicle_additional_description' => $dataCollection['dealer_option'],
                'seller_note' => $dataCollection['seller_note'],
                'price_history' => $dataCollection['price_history_data'],
                'fuel' => $dataCollection['fuel'],
                'drive_info' => $dataCollection['drive_train'],
                'mpg' => $dataCollection['avg_mpg'],
                'mpg_city' => $dataCollection['city_mpg_data'],
                'mpg_highway' => $dataCollection['hwy_mpg_data'],
                'exterior_color' => $dataCollection['exterior_color'],
                'interior_color' => $dataCollection['interior_color'],
                'created_date' => $formattedDate,
                'stock_date_formated' => $formattedDate,
                'user_id' => $id,
                'payment_price' => $monthlyPayment,
                'body_formated' => $carBody,
                'is_feature' => 0,
                'status' => 1,
                'inventory_status' => $dataCollection['inventory_status'],
                'batch_no' => $dataCollection['batch_no_data'],
            ]);
    
            // Return the inserted data to confirm success
            return $tmpInventory;
    
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error inserting tmp inventory: ' . $e->getMessage(), [
                'data' => $row,
                'error' => $e->getTraceAsString(),
            ]);
    
            // Return null or handle the error as per your application's requirement
            return null;
        }
    }



    // Update inventory and track changes
    private function updateTmpInventory($tmpinventory, $data, $localImagePathsString, $vehicleMakeDataID, $monthlyPayment, $carBody)
    {
        // dd(($tmpinventory->inventory_status == $data['inventory_status']),$tmpinventory->inventory_status, $data['inventory_status']);
        // dd(($tmpinventory->body_formated == $carBody),$tmpinventory->body_formated, $carBody);

        $changes = [];


        $fieldsToCheck = [
            'img_from_url' => $data['all_images'],
            'local_img_url' => $localImagePathsString,
            'vehicle_make_id' => $vehicleMakeDataID,
            'title' => $data['titles'],
            'year' => $data['year'],
            'make' => $data['make'],
            'model' => $data['model'],
            'price' => $data['price'],
            'price_rating' => $data['price_rating'],
            'miles' => $data['milage'],
            'type' => $data['type'],
            'trim' => $data['trim'],
            'stock' => $data['stock_number'],
            'engine_details' => $data['engine'],
            'transmission' => $data['transmission'],
            'vehicle_feature_description' => $data['feature'],
            'vehicle_additional_description' => $data['dealer_option'],
            'seller_note' => $data['seller_note'],
            'price_history' => $data['price_history_data'],
            'fuel' => $data['fuel'],
            'drive_info' => $data['drive_train'],
            'mpg' => $data['avg_mpg'],
            'mpg_city' => $data['city_mpg_data'],
            'mpg_highway' => $data['hwy_mpg_data'],
            'exterior_color' => $data['exterior_color'],
            'interior_color' => $data['interior_color'],
            'payment_price' => $monthlyPayment,
            'body_formated' => $carBody,
            'inventory_status' => $data['inventory_status'],
        ];
        
        foreach ($fieldsToCheck as $field => $newValue) {
            if ($tmpinventory->$field != $newValue) {
                $changes[$field] = [
                    'old' => $tmpinventory->$field,
                    'new' => $newValue,
                ];
                $tmpinventory->$field = $newValue;
            }
        }
        //// Example: Update price if changed
        // if ($tmpinventory->img_from_url != $data['all_images']) {
        //     $changes['img_from_url'] = [
        //         'old' => $tmpinventory->img_from_url,
        //         'new' => $data['all_images'],
        //     ];
        //     $tmpinventory->img_from_url = $data['all_images'];
        // }

        // if ($tmpinventory->local_img_url != $localImagePathsString) {
        //     $changes['local_img_url'] = [
        //         'old' => $tmpinventory->local_img_url,
        //         'new' => $localImagePathsString,
        //     ];
        //     $tmpinventory->local_img_url = $localImagePathsString;
        // }

        // if ($tmpinventory->vehicle_make_id != $vehicleMakeDataID) {
        //     $changes['vehicle_make_id'] = [
        //         'old' => $tmpinventory->vehicle_make_id,
        //         'new' => $vehicleMakeDataID,
        //     ];
        //     $tmpinventory->vehicle_make_id = $vehicleMakeDataID;
        // }

        // if ($tmpinventory->title != $data['titles']) {
        //     $changes['title'] = [
        //         'old' => $tmpinventory->title,
        //         'new' => $data['titles'],
        //     ];
        //     $tmpinventory->title = $data['titles'];
        // }

        // if ($tmpinventory->year != $data['year']) {
        //     $changes['year'] = [
        //         'old' => $tmpinventory->year,
        //         'new' => $data['year'],
        //     ];
        //     $tmpinventory->year = $data['year'];
        // }

        // if ($tmpinventory->make != $data['make']) {
        //     $changes['make'] = [
        //         'old' => $tmpinventory->make,
        //         'new' => $data['make'],
        //     ];
        //     $tmpinventory->make = $data['make'];
        // }

        // if ($tmpinventory->model != $data['model']) {
        //     $changes['model'] = [
        //         'old' => $tmpinventory->model,
        //         'new' => $data['model'],
        //     ];
        //     $tmpinventory->model = $data['model'];
        // }

        // if ($tmpinventory->price != $data['price']) {
        //     $changes['price'] = [
        //         'old' => $tmpinventory->price,
        //         'new' => $data['Price'],
        //     ];
        //     $tmpinventory->price = $data['Price'];
        // }

        // if ($tmpinventory->price_rating != $data['price_rating']) {
        //     $changes['price_rating'] = [
        //         'old' => $tmpinventory->price_rating,
        //         'new' => $data['price_rating'],
        //     ];
        //     $tmpinventory->price_rating = $data['price_rating'];
        // }

        // if ($tmpinventory->miles != $data['milage']) {
        //     $changes['miles'] = [
        //         'old' => $tmpinventory->miles,
        //         'new' => $data['milage'],
        //     ];
        //     $tmpinventory->miles = $data['milage'];
        // }

        // if ($tmpinventory->type != $data['type']) {
        //     $changes['type'] = [
        //         'old' => $tmpinventory->type,
        //         'new' => $data['type'],
        //     ];
        //     $tmpinventory->type = $data['type'];
        // }

        // if ($tmpinventory->trim != $data['trim']) {
        //     $changes['trim'] = [
        //         'old' => $tmpinventory->trim,
        //         'new' => $data['trim'],
        //     ];
        //     $tmpinventory->trim = $data['trim'];
        // }

        // if ($tmpinventory->stock != $data['stock_number']) {
        //     $changes['stock'] = [
        //         'old' => $tmpinventory->stock,
        //         'new' => $data['stock_number'],
        //     ];
        //     $tmpinventory->stock = $data['stock_number'];
        // }

        // if ($tmpinventory->engine_details != $data['engine']) {
        //     $changes['engine_details'] = [
        //         'old' => $tmpinventory->engine_details,
        //         'new' => $data['engine'],
        //     ];
        //     $tmpinventory->engine_details = $data['engine'];
        // }

        // if ($tmpinventory->transmission != $data['transmission']) {
        //     $changes['transmission'] = [
        //         'old' => $tmpinventory->transmission,
        //         'new' => $data['transmission'],
        //     ];
        //     $tmpinventory->transmission = $data['transmission'];
        // }

        // if ($tmpinventory->vehicle_feature_description != $data['feature']) {
        //     $changes['vehicle_feature_description'] = [
        //         'old' => $tmpinventory->vehicle_feature_description,
        //         'new' => $data['feature'],
        //     ];
        //     $tmpinventory->vehicle_feature_description = $data['feature'];
        // }

        // if ($tmpinventory->vehicle_additional_description != $data['dealer_option']) {
        //     $changes['vehicle_additional_description'] = [
        //         'old' => $tmpinventory->vehicle_additional_description,
        //         'new' => $data['dealer_option'],
        //     ];
        //     $tmpinventory->vehicle_additional_description = $data['dealer_option'];
        // }

        // if ($tmpinventory->seller_note != $data['seller_note']) {
        //     $changes['seller_note'] = [
        //         'old' => $tmpinventory->seller_note,
        //         'new' => $data['seller_note'],
        //     ];
        //     $tmpinventory->seller_note = $data['seller_note'];
        // }

        // if ($tmpinventory->price_history != $data['price_history_data']) {
        //     $changes['price_history'] = [
        //         'old' => $tmpinventory->price_history,
        //         'new' => $data['price_history_data'],
        //     ];
        //     $tmpinventory->price_history = $data['price_history_data'];
        // }

        // if ($tmpinventory->fuel != $data['fuel']) {
        //     $changes['fuel'] = [
        //         'old' => $tmpinventory->fuel,
        //         'new' => $data['fuel'],
        //     ];
        //     $tmpinventory->fuel = $data['fuel'];
        // }

        // if ($tmpinventory->drive_info != $data['drive_train']) {
        //     $changes['drive_info'] = [
        //         'old' => $tmpinventory->drive_info,
        //         'new' => $data['drive_train'],
        //     ];
        //     $tmpinventory->drive_info = $data['drive_train'];
        // }

        // if ($tmpinventory->mpg != $data['avg_mpg']) {
        //     $changes['mpg'] = [
        //         'old' => $tmpinventory->mpg,
        //         'new' => $data['avg_mpg'],
        //     ];
        //     $tmpinventory->mpg = $data['avg_mpg'];
        // }

        // if ($tmpinventory->mpg_city != $data['city_mpg_data']) {
        //     $changes['mpg_city'] = [
        //         'old' => $tmpinventory->mpg_city,
        //         'new' => $data['city_mpg_data'],
        //     ];
        //     $tmpinventory->mpg_city = $data['city_mpg_data'];
        // }

        // if ($tmpinventory->mpg_highway != $data['hwy_mpg_data']) {
        //     $changes['mpg_highway'] = [
        //         'old' => $tmpinventory->mpg_highway,
        //         'new' => $data['hwy_mpg_data'],
        //     ];
        //     $tmpinventory->mpg_highway = $data['hwy_mpg_data'];
        // }

        // if ($tmpinventory->exterior_color != $data['exterior_color']) {
        //     $changes['exterior_color'] = [
        //         'old' => $tmpinventory->exterior_color,
        //         'new' => $data['exterior_color'],
        //     ];
        //     $tmpinventory->exterior_color = $data['exterior_color'];
        // }

        // if ($tmpinventory->interior_color != $data['interior_color']) {
        //     $changes['interior_color'] = [
        //         'old' => $tmpinventory->interior_color,
        //         'new' => $data['interior_color'],
        //     ];
        //     $tmpinventory->interior_color = $data['interior_color'];
        // }

        // if ($tmpinventory->payment_price != $monthlyPayment) {
        //     $changes['payment_price'] = [
        //         'old' => $tmpinventory->payment_price,
        //         'new' => $monthlyPayment,
        //     ];
        //     $tmpinventory->payment_price = $monthlyPayment;
        // }

        // if ($tmpinventory->body_formated != $carBody) {
        //     $changes['body_formated'] = [
        //         'old' => $tmpinventory->body_formated,
        //         'new' => $carBody,
        //     ];
        //     $tmpinventory->body_formated = $carBody;
        // }

        // if ($tmpinventory->inventory_status != $data['inventory_status']) {
        //     $changes['inventory_status'] = [
        //         'old' => $tmpinventory->inventory_status,
        //         'new' => $data['inventory_status'],
        //     ];
        //     $tmpinventory->inventory_status = $data['inventory_status'];
        // }

        // Save changes
        $tmpinventory->save();

        return $changes;
    }


        // Update inventory and track changes
        private function updateInventory($mainInventory, $rowData, $localImagePathsString, $vehicleMakeDataID, $monthlyPayment, $carBody)
        {
            // dd($rowData['price_history_data'], $mainInventory->mainPriceHistory, $rowData);
            // dd($mainInventory,$rowData);
            // dd(($tmpinventory->inventory_status == $data['inventory_status']),$tmpinventory->inventory_status, $data['inventory_status']);
            // dd(($tmpinventory->body_formated == $carBody),$tmpinventory->body_formated, $carBody);
            DB::beginTransaction();

            try {
            
                $changes = []; // Track changes for logging/debugging
            
                // Update MainInventory fields
                $mainFields = [
                    // 'deal_id' => $rowData['deal_id'],
                    // 'zip_code' => $rowData['zip_code'],
                    // 'latitude' => $rowData['latitude'],
                    // 'longitude' => $rowData['longitude'],
                    'vehicle_make_id' => $vehicleMakeDataID,
                    'title' => $rowData['titles'],
                    'year' => $rowData['year'],
                    'make' => $rowData['make'],
                    'model' => $rowData['model'],
                    'price' => $rowData['price'],
                    'price_rating' => $rowData['price_rating'],
                    'miles' => $rowData['milage'],
                    'type' => $rowData['type'],
                    'trim' => $rowData['trim'],
                    'stock' => $rowData['stock_number'],
                    'transmission' => $rowData['transmission'],
                    'engine_details' => $rowData['engine'],
                    'fuel' => $rowData['fuel'],
                    'drive_info' => $rowData['drive_train'],
                    'mpg' => $rowData['avg_mpg'],
                    'mpg_city' => $rowData['city_mpg_data'],
                    'mpg_highway' => $rowData['hwy_mpg_data'],
                    'exterior_color' => $rowData['exterior_color'],
                    'interior_color' => $rowData['interior_color'],
                    'payment_price' => $monthlyPayment,
                    'body_formated' => $carBody,
                    'inventory_status' => $rowData['inventory_status'],
                ];
            
                foreach ($mainFields as $field => $value) {
                    if ($mainInventory->$field != $value) {
                        $changes['mainInventory'][$field] = [
                            'old' => $mainInventory->$field,
                            'new' => $value,
                        ];
                        $mainInventory->$field = $value;
                    }
                }
            
                // Save changes to MainInventory
                $mainInventory->save();

            
                // Update PriceHistory relation
                $priceHistory = $mainInventory->mainPriceHistory;


                // khfjkhw ejfhwjf wejufghuiwhfui wghfyui yeyuif yuiegfyuegf yuergfyu eyufgwerugyuiwegywery yueryuiwegy er

                $csvPriceHistoryRaw = $rowData['price_history_data'];
                    $csvRecords = explode(',', $csvPriceHistoryRaw);
                    $csvParsed = [];

                    foreach ($csvRecords as $record) {
                        $parts = explode(';', trim($record));
                        if (count($parts) === 3) {
                            $csvParsed[] = [
                                'change_date' => Carbon::createFromFormat('m/d/y', trim($parts[0]))->format('Y-m-d'),
                                'change_amount' => trim($parts[1]),
                                'amount' => (float)str_replace(['$', ','], '', trim($parts[2])),
                            ];
                        }
                    }

                    // Iterate over parsed CSV records and compare with database
                    foreach ($csvParsed as $csvData) {
                        $existingRecord = $priceHistory->where('change_date', $csvData['change_date'])->first();

                        if ($existingRecord) {
                            foreach (['change_date', 'change_amount', 'amount'] as $field) {
                                if ($existingRecord->$field != $csvData[$field]) {
                                    $changes[$field] = [
                                        'old' => $existingRecord->$field,
                                        'new' => $csvData[$field],
                                    ];
                                    $existingRecord->$field = $csvData[$field]; // Update field with new value
                                }
                            }

                            // Save the record if changes were made
                                $existingRecord->save();
                                Log::info("Updated PriceHistory record for date {$csvData['change_date']}.", $changes);
                        }
                    }
                // khfjkhw ejfhwjf wejufghuiwhfui wghfyui yeyuif yuiegfyuegf yuergfyu eyufgwerugyuiwegywery yueryuiwegy er
            
            
                // Update AdditionalInventory relation
                $additionalInventory = $mainInventory->additionalInventory;
            
                if ($additionalInventory) {
                    $additionalInventoryFields = [
                        'detail_url' => $rowData['source_url'],
                        'img_from_url' => $rowData['all_images'],
                        'local_img_url' => $localImagePathsString,
                        'vehicle_feature_description' => $rowData['feature'],
                        'vehicle_additional_description' => $rowData['dealer_option'],
                        'seller_note' => $rowData['seller_note'],
                    ];
            
                    foreach ($additionalInventoryFields as $field => $value) {
                        if ($additionalInventory->$field != $value) {
                            $changes['additionalInventory'][$field] = [
                                'old' => $additionalInventory->$field,
                                'new' => $value,
                            ];
                            $additionalInventory->$field = $value;
                        }
                    }
            

                    $additionalInventory->save();
                }
            
                // Log changes
                if (!empty($changes)) {
                    Log::info('Inventory and relations updated successfully.', $changes);
                }
            
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating inventory: ' . $e->getMessage());
                throw $e;
            }
    
            return $changes;
        }


    
    private function getLatLong($zipCode)
    {
        
        $latlongData = Latlongs::where('zip_code', $zipCode)->select('zip_code','latitude','longitude')->first();

        if ($latlongData) {
            // Return the existing data
            return [
                'zip_code' => $latlongData->zip_code,
                'latitude' => $latlongData->latitude,
                'longitude' => $latlongData->longitude
            ];
        }

            $apiKey = '4b84ff4ad9a74c79ad4a1a945a4e5be1';
            $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},us&key={$apiKey}";

            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['results'][0]['geometry'])) {
                    $geometry = $data['results'][0]['geometry'];
                    // Save the data in the database
                    $newLatlongData = Latlongs::create([
                        'zip_code' => $zipCode,
                        'latitude' => $geometry['lat'],
                        'longitude' => $geometry['lng']
                    ]);

                    // Return the newly created data
                    return [
                        'zip_code' => $newLatlongData->zip_code,
                        'latitude' => $newLatlongData->latitude,
                        'longitude' => $newLatlongData->longitude
                    ];
                }
            }
        // Log a warning if no results were found and return null values
        // Log::warning("No results found for ZIP code $zipCode");
        return [
            'zip_code' => $zipCode,
            'latitude' => null,
            'longitude' => null
        ];
    }

    private function ensureDirectoryExists($directory)
    {
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
        }
    }

    private function processImages($imageString, $saveDir, $vin)
    {
        $imageUrls = array_filter(explode(',', $imageString)); // Filter out empty values
        $imageCount = min(count($imageUrls), 5); // Limit to a maximum of 5 images
        $localImagePaths = [];

        // Ensure the directory exists
        $vinDir = $saveDir . '/' . $vin;
        if (!File::exists($vinDir)) {
            File::makeDirectory($vinDir, 0755, true); // Create the directory if it doesn't exist
        }

        // Process up to 5 images
        for ($index = 1; $index <= $imageCount; $index++) {
            $url = $imageUrls[$index - 1]; // Get the corresponding URL
            $fileName = sprintf('%s_%02d.jpg', $vin, $index); // Two-digit filename format
            $localPath = $vinDir . '/' . $fileName; // Full path to save the image

            // If the file already exists, skip downloading
            if (File::exists($localPath)) {
                Log::info("Image already exists, skipping download: $localPath");
                $localImagePaths[] = 'listing/' . $vin . '/' . $fileName; // Add existing path to the array
                continue; // Move to the next image
            }

            // // Download only if URL is valid
            // if ($url) {
            //     try {
            //         $response = Http::get($url);
            //         if ($response->successful()) {
            //             File::put($localPath, $response->body()); // Save the file locally
            //             Log::info("Successfully downloaded image: $url");
            //         } else {
            //             Log::warning("Failed to download image: $url");
            //         }
            //     } catch (\Exception $e) {
            //         Log::error("Error downloading image $url: " . $e->getMessage());
            //     }

            //     sleep(rand(4, 6)); // Optional delay to avoid server overload
            // } else {
            //     Log::warning("No URL provided for image index: $index");
            // }

            // Add the local path (even if not downloaded) to the array
            $localImagePaths[] = 'listing/' . $vin . '/' . $fileName;
        }

        return $localImagePaths; // Return all processed local image paths
    }

    private function calculateMonthlyPayment($price)
    {
        try {
            $salesTaxRate = 0.08;
            $interestRate = 0.09;
            $loanTermMonths = 72;

            $salesTax = $price * $salesTaxRate;
            $loanAmount = $price + $salesTax;
            $monthlyInterestRate = $interestRate / 12;

            return ceil(
                ($loanAmount * $monthlyInterestRate) /
                (1 - pow(1 + $monthlyInterestRate, -$loanTermMonths))
            );
        } catch (\Throwable $th) {
            Log::error("Error calculating monthly payment: " . $th->getMessage());
            return 0;
        }
    }

    private function determineCarBody($lowerString)
    {
        if (strpos($lowerString, 'coupe') !== false || strpos($lowerString, '2dr') !== false) {
            return 'Coupe';
        } elseif (strpos($lowerString, 'hetchback') !== false || strpos($lowerString, '3dr') !== false) {
            return 'Hatchback';
        } elseif (strpos($lowerString, 'sedun') !== false || strpos($lowerString, '4dr') !== false) {
            return 'Sedun';
        } elseif (strpos($lowerString, 'pickup') !== false) {
            return 'Truck';
        } elseif (strpos($lowerString, 'cargo') !== false) {
            return 'Cargo Van';
        } elseif (strpos($lowerString, 'passenger') !== false) {
            return 'Passenger Van';
        } elseif (strpos($lowerString, 'Mini-van') !== false) {
            return 'Minivan';
        } elseif (strpos($lowerString, 'sport') !== false) {
            return 'SUV';
        } else {
            return $lowerString; // default to the original string if no match is found
        }
    }


    public function inventoryRequestDelete(array $row, int $id, $dealer_id, int $user_id, $dataCollection, $filePath)
    {
        dd($row,$id,$dealer_id,$user_id,$dataCollection,$filePath,);
    }
}
