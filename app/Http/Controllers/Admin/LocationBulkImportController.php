<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\LocationZip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class LocationBulkImportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereNotNull('name')->get();
        $authUser = Auth::user();
        if($authUser->hasAllaccess())
        {
            // $dealerData = User::orderBy('id', 'desc')->where('status',1)->where('import_type',1);
            $dealerData = LocationZip::with('city')->orderBy('id', 'desc')->where('status',1);
        }else
        {
            // $dealerData = User::orderBy('id', 'desc')->where('status',1)->where('import_type',1);
            $dealerData = LocationZip::with('city')->orderBy('id', 'desc')->where('status',1);

        }

        // $rowCount = User::where('status', 1)->where('import_type', 1)->count();
        // $trashedCount = User::onlyTrashed()->where('status', 1)->where('import_type', 1)->count();
        $rowCount = LocationZip::with('city')->where('status', 1)->count();
        $trashedCount = LocationZip::with('city')->onlyTrashed()->where('status', 1)->count();

        // dd($dealerData->get()[0]);
        if ($request->ajax()) {
            return DataTables::of($dealerData)
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

                ->addColumn('location_city', function ($row) {
                    return $row->city->city_name;
                })
                ->addColumn('location_state', function ($row) {
                    return $row->city->state->state_name;
                })
                ->addColumn('location_state_short', function ($row) {
                    return $row->city->state->short_name;
                })


                ->addColumn('action', function ($row) {

                    if ($row->trashed()) {
                        $html = '<a href="' . route('admin.contact.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="' . route('admin.dealer.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {
                        // $html = '<a data-id="' . $row->id . '" style="margin-right:6px !important" class="btn btn-success btn-sm view-contact"><i  class="fa fa-eye"></i></a>' .
                        $html = '<a href="' . route('admin.dealer.delete', $row->id) . '" data-id= "' . $row->id . '" class="btn btn-danger btn-sm delete"><i  class="fa fa-trash"></i></a>';
                    }
                    return $html;
                })

                ->rawColumns(['action', 'check'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }

        return view('backend.admin.import.location_import', compact('users', 'dealerData'));
    }

    public function storeCSVLocation(Request $request)
    {
        // Validate the input file
        $request->validate([
            'import_file' => 'required|mimes:csv,txt',
        ], [
            'import_file.required' => 'Please upload a CSV file.',
            'import_file.mimes' => 'Only CSV files are allowed.',
        ]);
    
        $fileName = 'location_data.csv';
        $directory = public_path('uploads/location');
    
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save the uploaded file
        $filePath = $directory . '/' . $fileName;
        $request->file('import_file')->move($directory, $fileName);
    
        // Parse and save data to the database
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = []; // To store CSV headers
            $isHeaderRow = true;
            $errorRows = []; // To store rows with errors
            $maxSalesTax = 0; 
            $stateSalesTaxes = [];
    
            $latestBatchNo = LocationState::latest('batch_no')->value('batch_no');
            $batchNo = $latestBatchNo ? $latestBatchNo + 1 : 1;
    
            while (($data = fgetcsv($handle, 100000, ',')) !== false) {
                // Skip header row
                if ($isHeaderRow) {
                    // Read headers and convert them to lowercase
                    $headers = array_map(fn($header) => strtolower(trim($header)), $data);
                    $isHeaderRow = false;
                    continue;
                }
    
                // Map CSV rows to headers
                $row = array_combine($headers, $data);
                // Extract data from the row
                $src_url = $row['source url'];
                $city = $row['city'];
                $zip = $row['zip code'] ?? $row['zip'];
                $county = $row['county'] ?? null;
                $state = $row['state'];
                $short_state = $row['short name'] ?? 'Undefined';
                $latitude = $row['latitude'];
                $longitude = $row['longitude'];
                $combine_tax = isset($row['combibe tax']) ? str_replace('%', '', $row['combibe tax']) : 
                            (isset($row['combine tax']) ? str_replace('%', '', $row['combine tax']) : '');
                $sales_tax = isset($row['tax']) ? (float) $row['tax'] : ((isset($row['sales tax']) ? (float) $row['sales tax'] : (float) $combine_tax));
                

                // Skip rows with missing state or invalid sales tax
                if (!$state || is_null($sales_tax)) {
                    $errorRows[] = $row;
                    continue;
                }
    
                // Group sales tax by state
                if (!isset($stateSalesTaxes[$state])) {
                    $stateSalesTaxes[$state] = [];
                }
                $stateSalesTaxes[$state][] = $sales_tax;
    
                // Find the maximum sales tax for the current state
                $maxSalesTax = max($stateSalesTaxes[$state]);
    
                // Check if location state exists in the database
                $location_state = LocationState::where('state_name', $state)->first();

                // dd($location_state, $src_url, $city, $zip, $county, $state, $short_state, $latitude, $longitude, $combine_tax, $sales_tax);
                // dd($sales_tax, $combine_tax, str_replace('%','',$row['combibe tax']),$row['combibe tax'], $row, $request->all());

                if ($location_state) {
                    // Update sales tax if the current max tax is greater than the existing one
                    if ($location_state->sales_tax < $maxSalesTax) {
                        $location_state->update(['sales_tax' => $maxSalesTax]);
                    }
                    $stateId = $location_state->id;
                } else {
                    // Create a new location state if it doesn't exist
                    $new_state = LocationState::create([
                        'state_name' => $state,
                        'short_name' => $short_state,
                        'sales_tax' => $maxSalesTax,
                        'batch_no' => $batchNo,
                        'status' => 1,
                    ]);
                    $stateId = $new_state->id;
                }
    
                // Handle location city creation or update
                $location_city = LocationCity::where('city_name', $city)->first();
                if ($location_city) {
                    $cityId = $location_city->id;
                } else {
                    $new_city = LocationCity::create([
                        'location_state_id' => $stateId,
                        'city_name' => $city,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'status' => 1,
                    ]);
                    $cityId = $new_city->id;
                }
    
                // Handle location zip creation or update
                $location_zip = LocationZip::where('zip_code', $zip)->first();
                if ($location_zip) {
                    $zipId = $location_zip->id;
                } else {
                    $new_zip = LocationZip::create([
                        'location_city_id' => $cityId,
                        'county' => $county,
                        'latitude' => $latitude ?? 0,
                        'longitude' => $longitude ?? 0,
                        'zip_code' => $zip,
                        'sales_tax' => $sales_tax,
                        'src_url' => $src_url,
                        'status' => 1,
                    ]);
                    $zipId = $new_zip->id;
                }
            }
    
            fclose($handle);
    
            // Return error response if there are invalid rows
            if (!empty($errorRows)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some rows are missing mandatory fields (Name, Zip Code).',
                    'error_rows' => $errorRows, // Optional: Send the invalid rows for debugging
                ], 422);
            }
        }
    
        return response()->json([
            'status' => 'success',
            'message' => "CSV file processed successfully! Data saved.",
        ]);
    }
    
    // public function storeCSVLocation(Request $request)
    // {

    //     // Validate the input file
    //     $request->validate([
    //         'import_file' => 'required|mimes:csv,txt',
    //     ], [
    //         'import_file.required' => 'Please upload a CSV file.',
    //         'import_file.mimes' => 'Only CSV files are allowed.',
    //     ]);

    //     $fileName = 'location_data.csv';
    //     $directory = public_path('uploads/location');

    //     if (!is_dir($directory)) {
    //         mkdir($directory, 0755, true);
    //     }

    //     // Save the uploaded file
    //     $filePath = $directory . '/' . $fileName;
    //     $request->file('import_file')->move($directory, $fileName);

    //     // Parse and save data to the database
    //     if (($handle = fopen($filePath, 'r')) !== false) {
    //         $headers = []; // To store CSV headers
    //         $isHeaderRow = true;
    //         $errorRows = []; // To store rows with errors
    //         $maxSalesTax = 0; 
    //         $stateSalesTaxes = [];

    //         $latestBatchNo = LocationState::latest('batch_no')->value('batch_no');
    //         $batchNo = $latestBatchNo ? $latestBatchNo + 1 : 1;

    //         while (($data = fgetcsv($handle, 100000, ',')) !== false) {

    //         // $latestDealerNo = User::latest('dealer_id')->value('dealer_id');
    //         // $dealerNo = $latestDealerNo ? $latestDealerNo + 1 : 1001;

    //             if ($isHeaderRow) {
    //                 // Read headers and convert them to lowercase
    //                 $headers = array_map(fn($header) => strtolower(trim($header)), $data);
    //                 $isHeaderRow = false;
    //                 continue;
    //             }

    //         // Map CSV rows to headers
    //         $row = array_combine($headers, $data);


    //             // }
    //         // dd( isset($stateSalesTaxes[$state]) ,max($stateSalesTaxes[$state]));
    //         $zip  = $row['zip'];
    //         $state  = $row['state'];
    //         $city  = $row['city'];
    //         $short_state  = $row['short name'];
    //         $latitude  = $row['latitude'];
    //         $longitude  = $row['longitude'];
    //         $sales_tax  = (float)$row['tax'] ?? $row['sales  tax'];
                
            
    //         if (!$state || is_null($sales_tax)) {
    //             // Skip rows with missing state or invalid tax
    //             $errorRows[] = $row;
    //             continue;
    //         }
    
    //         // Group sales tax by state
    //         if (!isset($stateSalesTaxes[$state])) {
    //             $stateSalesTaxes[$state] = [];
    //         }
    //         $stateSalesTaxes[$state][] = $sales_tax;
    
    //         // Find the maximum sales tax for the current state
    //         $maxSalesTax = max($stateSalesTaxes[$state]);
    //             // Validate required fields
    //             if (empty($zip)) {
    //                 $errorRows[] = $row;
    //                 continue; // Skip rows with missing mandatory fields
    //             }
    //             $location_state = LocationState::where('state_name',$state)->first();

    //             if($location_state){
    //                 if ($location_state->sales_tax < $maxSalesTax) {
    //                     $location_state->update(['sales_tax' => $maxSalesTax]);
    //                 }
    //                 $stateId = $location_state->id;
    //             }else{
    //                 // $maxSalesTaxForState = isset($stateSalesTaxes[$state]) ? max($stateSalesTaxes[$state]) : 0;
    //                 $new_state = LocationState::create([
    //                     'state_name' => $state,
    //                     'short_name' => $short_state,
    //                     'sales_tax' => $maxSalesTax,
    //                     'batch_no' => $batchNo,
    //                 ]);

    //                 $stateId = $new_state->id;
    //             }

    //             $location_city = LocationCity::where('city_name',$city)->first();

    //             if($location_city){
    //                 $cityId = $location_city->id;
    //             }else{

    //                 $new_city = LocationCity::create([
    //                     'location_state_id' => $stateId,
    //                     'city_name' => $city,
    //                     'latitude' => $latitude,
    //                     'longitude' => $longitude,
    //                     'status' => 1,
    //                 ]);

    //                 $cityId = $new_city->id;
    //             }

    //             $location_zip = LocationZip::where('zip_code',$zip)->first();

    //             if($location_zip){
    //                 $zipId = $location_zip->id;
    //             }else{
    //                 $new_zip = LocationZip::create([
    //                     'location_city_id' => $cityId,
    //                     'latitude' => $latitude ?? 0,
    //                     'longitude' => $longitude ?? 0,
    //                     'zip_code' => $zip,
    //                     'sales_tax' => $sales_tax,
    //                     'status' => 1,
    //                 ]);

    //                 $zipId = $new_zip->id;
    //             }

    //         }

    //         fclose($handle);

    //         // Return error response if there are invalid rows
    //         if (!empty($errorRows)) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Some rows are missing mandatory fields (Name, Zip Code).',
    //                 'error_rows' => $errorRows, // Optional: Send the invalid rows for debugging
    //             ], 422);
    //         }
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => "Zip Code uploaded and data saved successfully!",
    //     ], 200);
    // }
}
