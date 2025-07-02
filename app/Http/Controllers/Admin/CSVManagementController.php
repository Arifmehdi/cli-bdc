<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use ZipArchive;

class CSVManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereNotNull('name')->get();
        $authUser = Auth::user();
        if($authUser->hasAllaccess())
        {
            $dealerData = Asset::orderBy('id', 'desc')->where('file_type', 1);
        }else
        {
            $dealerData = Asset::orderBy('id', 'desc')->where('file_type', 1);

        }
        // $rowCount = User::where('status', 1)->where('import_type', 1)->count();
        // $trashedCount = User::onlyTrashed()->where('status', 1)->where('import_type', 1)->count();

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
                ->addColumn('file_name', function ($row) {
                    return $row->file_name;
                })
                ->addColumn('file_type', function ($row) {
                    $data = $row->file_name;
                    $extension = strtoupper(pathinfo($data, PATHINFO_EXTENSION));
                    return $extension;
                })
                ->addColumn('file_path', function ($row) {
                    $data = $row->file_path;
                    $extension = pathinfo($data, PATHINFO_EXTENSION);
                    // Define a mapping of file extensions to Font Awesome icons
                    $icons = [
                        'pdf' => 'fa-file-pdf', // PDF icon
                        'doc' => 'fa-file-word', // Word document icon
                        'docx' => 'fa-file-word', // Word document icon
                        'xls' => 'fa-file-excel', // Excel icon
                        'xlsx' => 'fa-file-excel', // Excel icon
                        'png' => 'fa-file-image', // Image icon
                        'jpg' => 'fa-file-image', // Image icon
                        'jpeg' => 'fa-file-image', // Image icon
                        'zip' => 'fa-file-archive', // Archive icon
                        'rar' => 'fa-file-archive', // Archive icon
                        'txt' => 'fa-file-alt', // Text file icon
                        'csv' => 'fa-file-csv', // CSV file icon
                        'mp4' => 'fa-file-video', // Video file icon
                        'mp3' => 'fa-file-audio', // Audio file icon
                        // Add more mappings as needed
                    ];

                    // Default icon if extension is not in the mapping
                    $iconClass = $icons[$extension] ?? 'fa-file';

                    // Return the HTML for the icon
                    return '<i style="font-size:24px" class="fa ' . $iconClass . '"></i>';
                })
                ->addColumn('zip_status', function ($row) {
                    return ($row->zip_status == 1) ? 'Unzipped' : 'Waiting';
                })

                ->addColumn('action', function ($row) {

                    if ($row->trashed()) {
                        $html = '<a href="' . route('admin.contact.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
                            '<a href="' . route('admin.dealer.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
                    } else {
                        $data = $row->file_path;
                        $extension = pathinfo($data, PATHINFO_EXTENSION);
                        // $html = '<a data-id="' . $row->id . '" style="margin-right:6px !important" class="btn btn-success btn-sm view-contact"><i  class="fa fa-eye"></i></a>' .
                        $html = '<a href="' . route('admin.dealer.delete', $row->id) . '" data-id= "' . $row->id . '" class="btn btn-danger btn-sm delete-inventory" title="Delete"><i  class="fa fa-trash"></i></a>';

                        // if($extension == 'zip'){
                        //     $html .= '&nbsp; <a href="' . route('admin.file-management.extract', $row->id) . '" data-id= "' . $row->id . '" class="btn btn-dark btn-sm extract-inventory" title="Extract"><i  class="fa fa-file-export"></i></a>';
                        // }
                    }
                    return $html;
                })

                ->rawColumns(['action', 'message', 'check','file_path'])
                // ->with([
                //     'allRow' => $rowCount,
                //     'trashedRow' => $trashedCount,
                // ])
                ->smart(true)
                ->make(true);
        }

        return view('backend.admin.import.csv_import', compact('users', 'dealerData'));
    }


    public function upload(Request $request)
    {
        $fileName = $request->input('fileName');
        $filePath = public_path('uploads/import/' . $fileName);

        // Check if a file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Check if the file is valid (no errors)
            if ($file->isValid()) {
                // Define the destination path (public path in Laravel)
                $destinationPath = public_path('uploads/import');

                // Ensure the directory exists (create it if not)
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true); // Create directory if it doesn't exist
                }

                // Get the original file name
                $fileName = $file->getClientOriginalName();
                $sanitizedFileName = '1_'.preg_replace('/\s+/', '_', $fileName);
                $cusFilePath = 'uploads/import/' . $sanitizedFileName;
                // Move the file to the destination directory
                try {
                    // Move the file
                    $file->move($destinationPath, $cusFilePath);

                    // Save file info in the database
                    $savedFile = Asset::create([
                        'file_name' => $sanitizedFileName,
                        'file_path' => $cusFilePath,
                        'file_type' => 1, //1 means csv
                        'zip_status' => 0,
                        'status' => 0,
                    ]);

                    return response()->json([
                        'success' => 'File has been uploaded',
                        'fileName' => $savedFile->file_name,
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Error moving the file: ' . $e->getMessage()], 500);
                }
            } else {
                return response()->json(['error' => 'Uploaded file is not valid'], 400);
            }
        } else {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
        // //dd($fileName, $filePath);

        // Check if file exists
        // if (file_exists($filePath)) {
        //     $zip = new ZipArchive;

        //     // Check if file is a valid zip file
        //     if ($zip->open($filePath) === true) {
        //         // Extract the contents
        //         $zip->extractTo(public_path('uploads/listing/'));
        //         $zip->close();

        //         return response()->json(['message' => 'CSV has been successfully uploaded!'], 200);
        //     } else {
        //         return response()->json(['error' => 'Failed to open zip file.'], 400);
        //     }
        // }

        // return response()->json(['error' => 'File not found.'], 404);
    }

    public function delete(Request $request)
    {
        // Find the record by ID
        $asset = Asset::findOrFail($request->id);
        // Path to the file
        $filePath = public_path('uploads/import/' . $asset->file_name);
        // Check if the file exists, and delete it
        if (file_exists($filePath)) {
            unlink($filePath); // Deletes the file
        }
        // Delete the record from the database
        $asset->forceDelete();

        return response()->json(['success' => 'CSV  file deleted successfully.']);

    }

}
