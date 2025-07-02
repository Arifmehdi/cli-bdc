<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use ZipArchive;

class FileManagerController extends Controller
{

    public function index(){
        return view('backend.FileManagement.index');
    }


    public function getAllFilesAndFolders(Request $request)
    {
        // Default path to the public folder
        $path = public_path();
    
        // If a specific path is provided, update the path
        if ($request->path != '') {
            $path = public_path($request->path);
        }
    
        // Get directories and files using the native PHP methods
        $directories = array_map('basename', glob($path . '/*', GLOB_ONLYDIR));
        $files = array_map('basename', glob($path . '/*.*'));
    
        return response()->json([
            'directories' => $directories,
            'files' => $files,
            'path' => $path,
        ], 200);
    }



    public function createFile(Request $request)
    {
        // Validate inputs
        $validator = Validator::make($request->all(), [
            'path' => 'required|string|max:255',
            'fileName' => 'required|string|max:255|regex:/^[a-zA-Z0-9_-]+$/',
            'fileContent' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Normalize the path
        $absolutePath = str_replace('\\', '/', $request->path); // Replace backslashes with forward slashes
        $basePath = str_replace('\\', '/', public_path()); // Normalize public_path
    
        if (str_starts_with($absolutePath, $basePath)) {
            // Extract the relative path if it's inside the public folder
            $relativePath = str_replace($basePath, '', $absolutePath);
            $relativePath = ltrim($relativePath, '/'); // Remove leading slash
        } else {
            return response()->json(['errors' => ['path' => ['The path is outside the public directory.']]], 422);
        }
    
        // Construct full paths
        $directory = public_path($relativePath); // Absolute directory path
        $filePath = $directory . '/' . $request->fileName . '.txt'; // Full file path
    
        // Ensure directory exists
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                return response()->json(['message' => 'Failed to create directory'], 500);
            }
        }
    
        // Write file
        if (file_put_contents($filePath, $request->fileContent) === false) {
            return response()->json(['message' => 'Failed to write file'], 500);
        }
    
        return response()->json(['message' => 'File Created Successfully!'], 200);
        return response()->json(['message' => 'File Created Successfully!', 'filePath' => $filePath], 200);
    }

    public function createFolder(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'folderName' => 'required|string|max:255|regex:/^[a-zA-Z0-9_\-]+$/', // Valid folder name
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Get the folder name from the request
        $folderName = $request->folderName;
    
        // Define the folder path inside the public directory
        $folderPath = public_path($folderName);
    
        // Check if the folder already exists
        if (!is_dir($folderPath)) {
            // Create the folder with 0755 permissions and make it recursive
            if (!File::makeDirectory($folderPath, 0755, true)) {
                return response()->json(['message' => 'Failed to create folder'], 500);
            }
        } else {
            return response()->json(['message' => 'Folder already exists'], 400);
        }
    
        return response()->json(['message' => 'Folder created successfully!'], 200);
    }
    
    public function rename(Request $request)
    {
        // Get the old and new file names from the request
        $oldName = $request->input('oldName');
        $newName = $request->input('newName');
    
        // Ensure the old file exists
        $oldFilePath = public_path($oldName); // Get the full path of the old file
    
        if (!file_exists($oldFilePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }
    

        // Get the file extension of the old file
        $extension = pathinfo($oldFilePath, PATHINFO_EXTENSION);

        // If a new file name is provided without extension, retain the old extension
        if (!empty($newName) && !strpos($newName, '.')) {
            $newName .= '.' . $extension; // Add extension if not provided
        }
    
        // Define the new file path by appending the new name to the public directory
        // $newFilePath = public_path($request->path . '/' . $newName);
        $newFilePath = $request->path . '/' . $newName;

        if (rename($oldFilePath, $newFilePath)) {
            return response()->json(['message' => 'Renamed Successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to rename the file.'], 500);
        }
    }
    
    



    public function paste(Request $request)
    {
        $source = $request->input('source');
        $destination = $request->input('destination');

        // Get the filename or directory name from the source path
        $filename = basename($source);

        // Append the filename to the destination path
        $destinationPath = $destination . '/' . $filename;

        // Move the file or directory
        if ($request->isCopy == 1) {
            Storage::copy($source, $destinationPath);
        } else {
            Storage::move($source, $destinationPath);
        }

        return response()->json(['message' => 'File/Folder Moved Successfully'], 200);
    }



    public function zipFolder(Request $request)
    {
        $folderToZip = $request->input('folderToZip');
        $zipFileName = basename($folderToZip) . '.zip'; // Get the folder name and append .zip extension
        $zipFilePath = storage_path('app/' . $zipFileName); // Specify the path to the zip file in the app folder
        $zip = new ZipArchive;

        // Create the zip file
        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
            return "Failed to create zip file.";
        }

        // Add the folder and its contents to the zip file
        $files = Storage::allFiles($folderToZip);
        foreach ($files as $file) {
            $relativePath = str_replace($folderToZip . '/', '', $file);
            $zip->addFile(storage_path('app/' . $file), $relativePath);
        }

        // Close the zip file
        $zip->close();

        // Move the zip file back into the same folder
        Storage::move($zipFileName, $folderToZip . '/' . $zipFileName);

        return "Folder zipped successfully!";
    }


    public function download(Request $request)
    {
        $encoded_file_name = $request->query('encoded_file_name');
        $file_name = urldecode($encoded_file_name);

        try {
            return Storage::download($file_name);
        } catch (\Exception $e) {
            return abort(404);
        }
    }



    // public function delete(Request $request)
    // {
    //     $mime = Storage::mimeType($request->name);
    //     if ($mime) {
    //         Storage::delete($request->name);
    //     } else {
    //         Storage::deleteDirectory($request->name);
    //     }

    //     return response()->json(['message' => 'File/Folder Deleted Successfully'], 200);
    // }




    public function fileupload(Request $request)
    {
        // 'files.*' => 'required|mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx|max:2048'

        // $validator = Validator::make($request->all(), [
        //     'files.*' => 'required|mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx|max:2048', // Adjust max file size as needed
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()->first()], 400);
        // }
        $request->validate([
            'files' => 'array',
            'files.*' => 'required|mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx|max:2048'
        ]);

        // dd($request->all());
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = [];

            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                $file->storeAs($request->path, $fileName);
                $uploadedFiles[] = $fileName;
            }

            return response()->json(['message' => 'Files uploaded successfully', 'files' => $uploadedFiles]);
        }

        return response()->json(['error' => 'No files were uploaded'], 400);
    }

    // public function index(Request $request)
    // {
    //     $users = User::whereNotNull('name')->get();
    //     $authUser = Auth::user();
    //     if($authUser->hasAllaccess())
    //     {
    //         $dealerData = Asset::orderBy('id', 'desc')->where('file_type', 2);
    //     }else
    //     {
    //         $dealerData = Asset::orderBy('id', 'desc')->where('file_type', 2);

    //     }

    //     // $rowCount = User::where('status', 1)->where('import_type', 1)->count();
    //     // $trashedCount = User::onlyTrashed()->where('status', 1)->where('import_type', 1)->count();

    //     if ($request->ajax()) {
    //         return DataTables::of($dealerData)
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
    //             ->addColumn('file_name', function ($row) {
    //                 return $row->file_name;
    //             })
    //             ->addColumn('file_type', function ($row) {
    //                 $data = $row->file_name;
    //                 $extension = strtoupper(pathinfo($data, PATHINFO_EXTENSION));
    //                 return $extension;
    //             })
    //             ->addColumn('file_path', function ($row) {
    //                 $data = $row->file_path;
    //                 $extension = pathinfo($data, PATHINFO_EXTENSION);
    //                 // Define a mapping of file extensions to Font Awesome icons
    //                 $icons = [
    //                     'pdf' => 'fa-file-pdf', // PDF icon
    //                     'doc' => 'fa-file-word', // Word document icon
    //                     'docx' => 'fa-file-word', // Word document icon
    //                     'xls' => 'fa-file-excel', // Excel icon
    //                     'xlsx' => 'fa-file-excel', // Excel icon
    //                     'png' => 'fa-file-image', // Image icon
    //                     'jpg' => 'fa-file-image', // Image icon
    //                     'jpeg' => 'fa-file-image', // Image icon
    //                     'zip' => 'fa-file-archive', // Archive icon
    //                     'rar' => 'fa-file-archive', // Archive icon
    //                     'txt' => 'fa-file-alt', // Text file icon
    //                     'csv' => 'fa-file-csv', // CSV file icon
    //                     'mp4' => 'fa-file-video', // Video file icon
    //                     'mp3' => 'fa-file-audio', // Audio file icon
    //                     // Add more mappings as needed
    //                 ];

    //                 // Default icon if extension is not in the mapping
    //                 $iconClass = $icons[$extension] ?? 'fa-file';

    //                 // Return the HTML for the icon
    //                 return '<i style="font-size:24px" class="fa ' . $iconClass . '"></i>';
    //             })
    //             ->addColumn('zip_status', function ($row) {
    //                 return ($row->zip_status == 1) ? 'Unzipped' : 'Waiting';
    //             })

    //             ->addColumn('action', function ($row) {

    //                 if ($row->trashed()) {
    //                     $html = '<a href="' . route('admin.contact.restore', $row->id) . '" class="btn btn-info btn-sm restore" data-id="' . $row->id . '"><i class="fa fa-recycle"></i></a> ' .
    //                         '<a href="' . route('admin.dealer.delete', $row->id) . '" class="btn btn-danger btn-sm c-delete" data-id="' . $row->id . '"><i class="fa fa-exclamation-triangle"></i></a>';
    //                 } else {
    //                     $data = $row->file_path;
    //                     $extension = pathinfo($data, PATHINFO_EXTENSION);
    //                     // $html = '<a data-id="' . $row->id . '" style="margin-right:6px !important" class="btn btn-success btn-sm view-contact"><i  class="fa fa-eye"></i></a>' .
    //                     $html = '<a href="' . route('admin.dealer.delete', $row->id) . '" data-id= "' . $row->id . '" class="btn btn-danger btn-sm delete-inventory" title="Delete"><i  class="fa fa-trash"></i></a>';

    //                     if($extension == 'zip'){
    //                         $html .= '&nbsp; <a href="' . route('admin.file-management.extract', $row->id) . '" data-id= "' . $row->id . '" class="btn btn-dark btn-sm extract-inventory" title="Extract"><i  class="fa fa-file-export"></i></a>';
    //                     }
    //                 }
    //                 return $html;
    //             })

    //             ->rawColumns(['action', 'message', 'check','file_path'])
    //             // ->with([
    //             //     'allRow' => $rowCount,
    //             //     'trashedRow' => $trashedCount,
    //             // ])
    //             ->smart(true)
    //             ->make(true);
    //     }

    //     return view('backend.admin.import.dealer_import', compact('users', 'dealerData'));
    // }

    public function fileManagerIndex()
    {
        dd('i love you ');
    }


    public function upload(Request $request)
    {
        $fileName = $request->input('fileName');
        $filePath = public_path('listing/' . $fileName);

        // Check if a file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Check if the file is valid (no errors)
            if ($file->isValid()) {
                // Define the destination path (public path in Laravel)
                $destinationPath = public_path('listing');

                // Ensure the directory exists (create it if not)
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true); // Create directory if it doesn't exist
                }

                // Get the original file name
                $fileName = $file->getClientOriginalName();
                $sanitizedFileName = preg_replace('/\s+/', '_', $fileName);
                $cusFilePath = 'listing/' . $sanitizedFileName;
                // Move the file to the destination directory
                try {
                    // Move the file
                    $file->move($destinationPath, $cusFilePath);

                    // Save file info in the database
                    $savedFile = Asset::create([
                        'file_name' => $sanitizedFileName,
                        'file_path' => $cusFilePath,
                        'file_type' => 2, //2 means zip
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
        dd($fileName, $filePath);
        // Check if file exists
        if (file_exists($filePath)) {
            $zip = new ZipArchive;

            // Check if file is a valid zip file
            if ($zip->open($filePath) === true) {
                // Extract the contents
                $zip->extractTo(public_path('uploads/listing/'));
                $zip->close();

                return response()->json(['message' => 'File has been successfully unzipped!'], 200);
            } else {
                return response()->json(['error' => 'Failed to open zip file.'], 400);
            }
        }

        return response()->json(['error' => 'File not found.'], 404);
    }

    public function delete(Request $request)
    {
        // Find the record by ID
        $asset = Asset::findOrFail($request->id);
        // Path to the file
        $filePath = public_path('listing/' . $asset->file_name);
        // Check if the file exists, and delete it
        if (file_exists($filePath)) {
            unlink($filePath); // Deletes the file
        }
        // Delete the record from the database
        $asset->forceDelete();

        return response()->json(['success' => 'Asset and file have been deleted.']);

    }


    public function extract(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'file' => 'required|string', // Ensure the file path is provided
        ]);

        // Get the file path from the request
        $filePath = $request->input('file');

        // Ensure the file exists
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        // Ensure the file is a ZIP archive
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'zip') {
            return response()->json(['error' => 'The selected file is not a ZIP archive.'], 400);
        }

        // Define the extraction path (same directory as the ZIP file)
        // $extractTo = dirname($filePath) . '/' . pathinfo($filePath, PATHINFO_FILENAME);
        $extractTo = dirname($filePath);

        // Create the extraction directory if it doesn't exist
        if (!File::exists($extractTo)) {
            File::makeDirectory($extractTo, 0755, true);
        }

        // Extract the ZIP file
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            $zip->extractTo($extractTo);
            $zip->close();

            return response()->json(['success' => 'File extracted successfully!', 'path' => $extractTo], 200);
        } else {
            return response()->json(['error' => 'Failed to extract the ZIP file.'], 500);
        }
    }

    // public function extract(Request $request)
    // {
    //     dd($request->all());
    //     // Find the asset by ID
    //     // $asset = Asset::findOrFail($request->id);
    //     dd($asset);
    //     // File path and extraction path (in the same folder as the ZIP file)
    //     $filePath = public_path('listing/' . $asset->file_name);
    //     $extractPath = public_path('listing'); // Extract directly into the "listing" folder

    //     // Check if the file exists at the given path
    //     if (file_exists($filePath)) {
    //         // Initialize the ZipArchive class
    //         $zip = new ZipArchive;

    //         // Try to open the ZIP file
    //         if ($zip->open($filePath) === TRUE) {
    //             // Extract the ZIP file to the "listing" folder
    //             if ($zip->extractTo($extractPath)) {
    //                 // Close the ZIP file
    //                 $zip->close();

    //                 // Get the list of files extracted from the "listing" folder
    //                 $extractedFiles = [];
    //                 $files = scandir($extractPath); // Get the list of files in the folder

    //                 // Filter out the . and .. entries
    //                 $extractedFiles = array_filter($files, function($file) {
    //                     return !in_array($file, ['.', '..']);
    //                 });

    //                 // Update the asset's zip_status in the database to indicate success
    //                 $asset->zip_status = 1; // 1 indicates the file is extracted
    //                 $asset->save();

    //                 return response()->json([
    //                     'success' => 'File has been extracted successfully.',
    //                     'extracted_files' => $extractedFiles // Send back the list of extracted files
    //                 ]);
    //             } else {
    //                 return response()->json(['error' => 'Failed to extract the ZIP file.'], 500);
    //             }
    //         } else {
    //             return response()->json(['error' => 'Unable to open the ZIP file.'], 500);
    //         }
    //     } else {
    //         return response()->json(['error' => 'ZIP file not found.'], 404);
    //     }
    // }

}
