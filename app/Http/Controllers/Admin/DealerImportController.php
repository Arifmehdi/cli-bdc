<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Inventory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class DealerImportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereNotNull('name')->get();
        $latestBatchNo = User::where('status', 1)->where('import_type', 1)->max('batch_no');
        $authUser = Auth::user();
        if($authUser->hasAllaccess())
        {
            $dealerData = User::orderBy('id', 'desc')->where('status',1)->where('batch_no', $latestBatchNo)->where('import_type',1);
        }else
        {
            $dealerData = User::orderBy('id', 'desc')->where('status',1)->where('batch_no', $latestBatchNo)->where('import_type',1);

        }

        $rowCount = User::where('status', 1)->where('import_type', 1)->where('batch_no', $latestBatchNo)->count();
        $trashedCount = User::onlyTrashed()->where('status', 1)->where('import_type', 1)->where('batch_no', $latestBatchNo)->count();

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
                ->addColumn('dealer_name', function ($row) {
                    $url = route('admin.inventory.list.v1', $row->deal_id);
                    $url = '#';
                    $html = '<a href="' . $url . '" style="border-bottom: 1px solid #007bff;">' . $row->name . '</a>';
                    return $html;
                })
                ->addColumn('dealer_phone', function ($row) {
                    return $row->phone;
                })
                ->addColumn('dealer_email', function ($row) {
                    return $row->email;
                })
                ->addColumn('dealer_city', function ($row) {
                    return $row->city;
                })
                ->addColumn('dealer_state', function ($row) {
                    return $row->state;
                })
                ->addColumn('dealer_zip', function ($row) {
                    return $row->zip;
                })
                ->addColumn('inventory_num', function ($row) {
                    return 00;
                })
                ->addColumn('role', function ($row) {
                    $roleName = Role::findOrFail($row->role_id)->name;
                    return ucfirst($roleName);
                })
                ->addColumn('import_type', function ($row) {
                    return ($row->import_type == 1) ? 'Bulk Import' : ' Manual Import';
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

                ->rawColumns(['action', 'message', 'check','dealer_name'])
                ->with([
                    'allRow' => $rowCount,
                    'trashedRow' => $trashedCount,
                ])
                ->smart(true)
                ->make(true);
        }

        return view('backend.admin.import.dealer_import', compact('users', 'dealerData'));
    }

        public function storeInventory(Request $request)
        {
            // dd($request->all());
            //// abort_if(! auth()->user()->can('hrm_bulk_attendance_import_store'), 403, 'Access forbidden');
            $request->validate([
                'user' => 'required',
                'import_file' => 'required|mimes:csv,xlx,xlsx,xls',
            ], [
                'user.required' => 'User field is required',
                'import_file.required' => 'Import field is required',
                'import_file.mimes' => 'Please upload a valid CSV or Excel file (xls, xlsx, xlsm).'
            ]);

            // dd($request->all());
            // ***********data store start here
            $originalName = $request->file('import_file')->getClientOriginalName();


            $userId = $request->user;
            $directory = public_path('uploads/import');

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $originalFileName = $request->file('import_file')->getClientOriginalName();
            $modifiedFileName = $userId.'_'.date('mdY') . '_' . $originalFileName;
            $request->file('import_file')->move($directory, $modifiedFileName);

            return response()->json([
                'status' => 'success',
                'message' => "Non-Server CSV Stored Successfully",
            ], 200);
        }


                // csv import but array_combine isseu 
        // public function storeCSVDealer(Request $request)
        // {
        //     // Validate the input file
        //     $request->validate([
        //         'import_file' => 'required|mimes:csv,txt',
        //     ], [
        //         'import_file.required' => 'Please upload a CSV file.',
        //         'import_file.mimes' => 'Only CSV files are allowed.',
        //     ]);

        //     $fileName = 'dealer_data.csv';
        //     $directory = public_path('uploads/dealer');

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

        //         $latestBatchNo = User::latest('batch_no')->value('batch_no');
        //         $batchNo = $latestBatchNo ? $latestBatchNo + 1 : 10;

        //         while (($data = fgetcsv($handle, 1000, ',')) !== false) {

        //         $latestDealerNo = User::latest('dealer_id')->value('dealer_id');
        //         $dealerNo = $latestDealerNo ? $latestDealerNo + 1 : 1001;

        //             if ($isHeaderRow) {
        //                 // Read headers and convert them to lowercase
        //                 $headers = array_map(fn($header) => strtolower(trim($header)), $data);
        //                 $isHeaderRow = false;
        //                 continue;
        //             }
        //             // dd($headers, $data);
        //             // Map CSV rows to headers
        //             $row = array_combine($headers, $data);
        //             $name  = $row['name'];
        //             $zipCode = $row['zip code'] ?? $row['zip'];

        //             // Validate required fields
        //             if (empty($name) || empty($zipCode)) {
        //                 $errorRows[] = $row;
        //                 continue; // Skip rows with missing mandatory fields
        //             }

        //             // if($row['email'] == ""){ $row['email'] = null; }

        //             // Generate a random password for the user
        //             // $password = Str::random(8);
        //             $password = 'Dealer@#12345';

        //             $userInfo = User::where('name',$name)->first();
        //             if(!$userInfo){
        //             // Save to database
        //             $userInfo =  User::create([
        //                 // 'fname' => $row['name'],
        //                 // 'lname' => null,
        //                 'name' => $name,
        //                 'address' => $row['address'] ?? null,
        //                 'dealer_id' => $dealerNo ?? null,
        //                 'brand_website' => $row['brand website'] ?? $row['dealer_homepage'] ?? $row['dealer homepage'] ?? null,
        //                 'dealer_full_address' => $row['full address'] ?? null,
        //                 'phone' => $row['phone'] ?? null,
        //                 'state' => $row['state'] ?? null,
        //                 'city' => $row['city'] ?? null,
        //                 'zip' => $zipCode,
        //                 'email' => $row['email'] ?? Str::random(12) . '@bestdreamcar.com',
        //                 'password' => Hash::make($password),
        //                 'dealer_website' => $row['brand website'] ?? $row['dealer_homepage'] ?? $row['dealer homepage'] ?? null,
        //                 'rating' => $row['rating'] ?? null,
        //                 'review' => $row['review'] ?? null,
        //                 'import_type' => 1,
        //                 'batch_no' => $batchNo,
        //             ]);
        //             }

        //             $role = Role::where('name','dealer')->first();
        //             $userInfo->assignRole($role);
        //             // dd($userInfo);

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
        //         'message' => "Dealer file uploaded and data saved successfully!",
        //     ], 200);
        // }


        public function storeCSVDealer(Request $request)
        {
            // Validate the input file
            $request->validate([
                'import_file' => 'required|mimes:csv,txt',
            ], [
                'import_file.required' => 'Please upload a CSV file.',
                'import_file.mimes' => 'Only CSV files are allowed.',
            ]);
        
            $date_data = date('Ymd');
            $fileName = 'dealer_data_'.$date_data.'.csv';
            $directory = public_path('uploads/dealer');

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        
            // Save the uploaded file
            $filePath = $directory . '/' . $fileName;
            $request->file('import_file')->move($directory, $fileName);
        
            // $addedRows = [];
            // $errorRows = [];

            // Parse and save data to the database
            if (($handle = fopen($filePath, 'r')) !== false) {

                $headers = [];
                $isHeaderRow = true;

                $errorRows = [];
                $importedDealers = [];
                $notImportedDealers = [];
                $updatedDealers = []; // To track updates
        
                $latestBatchNo = User::latest('batch_no')->value('batch_no');
                $batchNo = $latestBatchNo ? $latestBatchNo + 1 : 10;

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


                    // // Ensure the number of columns matches the headers
                    // if (count($headers) !== count($data)) {
                    //     $errorRows[] = ['row' => $data, 'error' => 'Header and row column count mismatch'];
                    //     $notImportedDealers[] = implode(',', $data) . ' - Column count mismatch';
                    //     continue;
                    // }

                    // Ensure the number of columns matches the headers
                    if (count($headers) !== count($data)) {
                        // Fill missing values with null
                        $data = array_pad($data, count($headers), null);
                    }

                    // Map CSV rows to headers
                    $row = array_combine($headers, $data);
                    $name = $row['name'] ?? null;
                    $zipCode = $row['zip code'] ?? $row['zip'] ?? null;
        
                    // Validate required fields
                    if (empty($name) || empty($zipCode)) {
                        $errorRows[] = ['row' => $row, 'error' => 'Missing mandatory fields (Name, Zip Code)'];
                        $notImportedDealers[] = implode(',', $data) . ' - Missing mandatory fields';
                        continue;
                    }

                    // $userInfo = User::where('name', $name)->first();

                    // Check if the user already exists
                    // $userInfo = User::where('name', $name)->orWhere('phone', $row['phone'] ?? '')->first();
                    $userInfo = User::where('name', $name)
                                ->where('zip', $zipCode)
                                ->first();


                    if ($userInfo) {
                        // Compare fields for changes
                        $changes = [];
                        $updatableFields = [
                            'address', 'brand_website', 'dealer_full_address', 'phone',
                            'state', 'city', 'zip', 'email', 'dealer_website', 'rating', 'review'
                        ];

                        foreach ($updatableFields as $field) {
                            if (($row[$field] ?? null) && $userInfo->$field !== $row[$field]) {
                                $changes[$field] = [
                                    'old' => $userInfo->$field,
                                    'new' => $row[$field]
                                ];
                                $userInfo->$field = $row[$field];
                            }
                        }

                        if (!empty($changes)) {
                            $userInfo->save();
                            $updatedDealers[] = "Dealer ID: {$userInfo->dealer_id} - Name: {$userInfo->name} - Changes: " . json_encode($changes);
                        } else {
                            $notImportedDealers[] = implode(',', $data) . " - No changes detected";
                        }
                        continue;

                        // $notImportedDealers[] = implode(',', $data) . " - Dealer already exists (ID: {$userInfo->dealer_id})";
                        // continue;
                    }

                    // $password = 'Dealer@#12345';
        
                    $password = Str::random(8);

                    $latestDealerData = User::max('dealer_id') ?? 1000; // Default to 1000 if no data exists
                    $latestDealerId = is_numeric($latestDealerData) ? (int)$latestDealerData + 1 : 1001;
                    $role = Role::where('name', 'dealer')->first();
                    
                    $newDealer = User::create([
                        'name' => $name,
                        'address' => $row['address'] ?? null,
                        'dealer_id' => $latestDealerId,
                        'brand_website' => $row['brand website'] ?? $row['dealer_homepage'] ?? $row['dealer homepage'] ?? null,
                        'dealer_full_address' => $row['full address'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'state' => $row['state'] ?? null,
                        'city' => $row['city'] ?? null,
                        'zip' => $zipCode,
                        'email' => $row['email'] ?? Str::random(12) . '@bestdreamcar.com',
                        'password' => Hash::make($password),
                        'dealer_website' => $row['dealer website'] ?? null,
                        'role_id' => $role->id ?? null,
                        'rating' => $row['rating'] ?? null,
                        'review' => $row['review'] ?? null,
                        'import_type' => 1,
                        'batch_no' => $batchNo,
                    ]);
        
                    $newDealer->assignRole($role);
                    $importedDealers[] = $newDealer->dealer_id . ' - ' . $name;
                
                }
        
                fclose($handle);

                // Save reports
                file_put_contents($directory . '/imported_dealers.txt', implode("\n", $importedDealers));
                file_put_contents($directory . '/not_imported_dealers.txt', implode("\n", $notImportedDealers));
                file_put_contents($directory . '/updated_dealers.txt', implode("\n", $updatedDealers));

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
                'message' => "Dealer file uploaded and data saved successfully!",
                'imported_count' => count($importedDealers),
                'not_imported_count' => count($notImportedDealers),
                'updated_count' => count($updatedDealers),
            ], 200);
        }
        



        // public function storeCSVDealer(Request $request)
        // {
        //     // $request->validate([
        //     //     'user' => 'required',
        //     //     'import_file' => 'required|mimes:csv,xls,xlsx',
        //     // ], [
        //     //     'user.required' => 'User field is required.',
        //     //     'import_file.required' => 'Import field is required.',
        //     //     'import_file.mimes' => 'Please upload a valid CSV or Excel file (csv, xls, xlsx).'
        //     // ]);


        //     $userId = $request->user;
        //     $fileName = 'dealer_data.csv';
        //     $directory = public_path('uploads/dealer');

        //     if (!is_dir($directory)) {
        //         mkdir($directory, 0755, true);
        //     }

        //     // Save the file
        //     $filePath = $directory . '/' . $fileName;
        //     $request->file('import_file')->move($directory, $fileName);

        //     // Parse and save data to the database
        //     if (($handle = fopen($filePath, 'r')) !== false) {
        //         $header = true; // Skip the header row
        //         while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        //             if ($header) {
        //                 $header = false;
        //                 continue;
        //             }

        //             // Check if the row has enough columns
        //             if (count($data) < 14) {
        //                 continue; // Skip rows with missing data
        //             }

        //             $password = Str::random(8);
        //             // dd($data, $request->all());
        //             // Map the CSV data to your User model fields
        //             User::create([
        //                 'fname' => $data[0],
        //                 'lname' => $data[1],
        //                 'name' => $data[2],
        //                 'address' => $data[3],
        //                 'dealer_full_address' => $data[4],
        //                 'phone' => $data[5],
        //                 'state' => $data[6],
        //                 'city' => $data[7],
        //                 'zip' => $data[8],
        //                 'email' => Str::random(12) . '@gmail.com',
        //                 'password' => Hash::make($password),
        //                 'dealer_website' => $data[10],
        //                 'rating' => $data[12],
        //                 'review' => $data[13],
        //                 'import_type' => 1,
        //                 // 'user_id' => $userId, // Associate with the user if needed
        //             ]);
        //         }
        //         fclose($handle);
        //     }

        //     return response()->json([
        //         'status' => 'success',
        //         'message' => "Dealer file uploaded and data saved successfully!",
        //     ], 200);
        // }

        public function destroy($id)
        {
            // dd($id);
            $dealer = User::findOrFail($id);
            $dealer->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Dealer deleted successfully!',
            ], 200);
        }

}
