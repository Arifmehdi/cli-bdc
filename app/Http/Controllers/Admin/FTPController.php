<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestInventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class FTPController extends Controller
{
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ftp_user_by' => 'required',
            'ftp_server' => 'required',
            'ftp_user' => 'required',
            'ftp_pass' => 'required',
            'ftp_file' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ftp_user_by = $request->input('ftp_user_by');
        $ftp_server = $request->input('ftp_server');
        $ftp_user = $request->input('ftp_user');
        $ftp_pass = $request->input('ftp_pass');
        $ftp_file = $request->input('ftp_file');

        // Read .env file
        $envPath = base_path('.env');
        $envContents = File::get($envPath);

        // Replace values in .env file
        $envContents = preg_replace('/^FTP_SERVER=.*/m', 'FTP_SERVER=' . $ftp_server, $envContents);
        $envContents = preg_replace('/^FTP_USER=.*/m', 'FTP_USER=' . $ftp_user, $envContents);
        $envContents = preg_replace('/^FTP_PASS=.*/m', 'FTP_PASS=' . $ftp_pass, $envContents);
        $envContents = preg_replace('/^FTP_FILE=.*/m', 'FTP_FILE=' . $ftp_file, $envContents);

        // Write back to .env file
        File::put($envPath, $envContents);

        $exitCode = Artisan::call('download:file');
        $filepath = Artisan::output();
        dd($exitCode, $filepath);
        if ($exitCode === 0) {
            return response()->json(['success' => 'ENV file updated & download file successfully'], 200);
        }
        return response()->json(['success' => 'ENV file updated successfully, but download failed'], 200);

    }


    public function csvFileManager()
    {
        /*file management code start here*/ 
        $folderPath = public_path('uploads/import');
        if (is_dir($folderPath)) {
            $files = scandir($folderPath);
            $files = array_diff($files, array('.', '..'));

            $serverFiles = array_filter($files, function($file) {
                return strpos($file, 'server_') === 0; // Files starting with 'server_'
            });

            $nonServerFiles = array_filter($files, function($file) {
                return strpos($file, 'server_') !== 0; // Files not starting with 'server_'
            });

            // Get the last modified time for each file
            $serverFilesWithTime = array_map(function($file) use ($folderPath) {
                return [
                    'file' => $file,
                    'modified_time' => filemtime($folderPath . '/' . $file), // Get last modified time
                ];
            }, $serverFiles);

            $nonServerFilesWithTime = array_map(function($file) use ($folderPath) {
                return [
                    'file' => $file,
                    'modified_time' => filemtime($folderPath . '/' . $file), // Get last modified time
                ];
            }, $nonServerFiles);

            // Sort both arrays by modified time (in descending order, most recent first)
            usort($serverFilesWithTime, function($a, $b) {
                return $b['modified_time'] - $a['modified_time'];
            });

            usort($nonServerFilesWithTime, function($a, $b) {
                return $b['modified_time'] - $a['modified_time'];
            });

            // Get the most recent file from each category
            $latestServerFile = $serverFilesWithTime[0] ?? null;
            $latestNonServerFile = $nonServerFilesWithTime[0] ?? null;

            // Prepare the response with the latest file from each category
            return response()->json([
                'message' => 'Files found.',
                'latest_server_file' => $latestServerFile,
                'latest_non_server_file' => $latestNonServerFile,
            ]);
        } else {
            return response()->json([
                'message' => 'Directory not found.',
            ]);
        }
    }

    public function csvFileDelete($filename)
    {
        $filePath = public_path('uploads/import/' . $filename);
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }
    
        // Delete the file using PHP's unlink()
        if (unlink($filePath)) {
            return response()->json(['message' => 'File deleted successfully']);
        } else {
            return response()->json(['message' => 'Error deleting file'], 500);
        }

    }


    public function listing_save(Request $request)
    {

        $exitCode = Artisan::call('download:file');

        // Handle the response or log it
        if ($exitCode === 0) {
            return response()->json(['message' => 'File download command executed successfully.']);
        } else {
            return response()->json(['message' => 'File download command failed.'], 500);
        }

        dd('import asche ');
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required',
            'year' => 'required|string',
            'make' => 'required|string',
            'model' => 'required|string',
            'vin' => 'required|string|unique:request_inventories,vin',
            'price' => 'required|numeric|min:0',
            'exterior_color' => 'required|string',
            'transmission' => 'required|string',
            'miles' => 'required|integer|min:0',
            'fuel' => 'required|string',
            'drive_info' => 'required|string',
            'img_from_url.*' => 'image|mimes:jpeg,png,jpg|max:2048', // Validate each file
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone is required.',
            'year.required' => 'The year is required.',
            'make.required' => 'The make is required.',
            'model.required' => 'The model is required.',
            'vin.required' => 'The VIN is required.',
            'vin.unique' => 'The VIN must be unique.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'exterior_color.required' => 'The exterior color is required.',
            'transmission.required' => 'The transmission is required.',
            'miles.required' => 'The mileage is required.',
            'miles.integer' => 'The mileage must be a valid number.',
            'fuel.required' => 'The fuel type is required.',
            'drive_info.required' => 'The drivetrain information is required.',
            'img_from_url.*.image' => 'Each file must be an image.',
            'img_from_url.*.mimes' => 'Only jpeg, png, and jpg formats are allowed.',
            'img_from_url.*.max' => 'Each image must not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find or create a user
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->first_name . ' ' . $request->last_name,
                'phone' => $request->phone,
            ]
        );

        // $userInfo = [
        //     'name' => $user->name,
        //     'id' => $user->id,
        //     'email' => $user->email,
        // ];

        // Create a new listing
        $listing = new RequestInventory();

        // Handle image uploads
        $imageNames = [];
        if ($request->hasFile('img_from_url')) {
            foreach ($request->file('img_from_url') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('frontend/assets/images/listings/'), $imageName);
                $imageNames[] = $imageName;
            }
            $listing->img_from_url = json_encode($imageNames);
        }

        // Assign listing fields
        $listing->year = $request->year;
        $listing->make = $request->make;
        $listing->model = $request->model;
        $listing->vin = $request->vin;
        $listing->price = $request->price;
        $listing->exterior_color = $request->exterior_color;
        $listing->transmission = $request->transmission;
        $listing->miles = $request->miles;
        $listing->type = 'used';
        $listing->user_id = $user->id;
        $listing->fuel = $request->fuel;
        $listing->drive_info = $request->drive_info;
        $listing->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Listing added successfully!',
        ]);
    }

}
