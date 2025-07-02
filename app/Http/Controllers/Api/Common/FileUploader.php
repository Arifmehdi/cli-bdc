<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUploader extends Controller
{
    public function bannerUpload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $img_path = 'dashboard/images/banners/';
        $isUpdate = $request->input('is_update', false);
        $oldImage = $request->input('old_image');

        try {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path($img_path);

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // If updating, delete the old image first
            if ($isUpdate && $oldImage) {
                $oldImagePath = public_path($img_path . $oldImage);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete old image
                }
            }

            // Move the new uploaded file
            $image->move($destinationPath, $imageName);

            return response()->json([
                'status' => 'success',
                'message' => 'Image uploaded successfully',
                'image_path' => $img_path . $imageName,
                'image_url' => asset($img_path . $imageName),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function blogUpload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $img_path = 'frontend/assets/images/blog/';
        $isUpdate = $request->input('is_update', false);
        $oldImage = $request->input('old_image');

        try {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path($img_path);

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // If updating, delete the old image first
            if ($isUpdate && $oldImage) {
                $oldImagePath = public_path($img_path . $oldImage);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete old image
                }
            }

            // Move the new uploaded file
            $image->move($destinationPath, $imageName);

            return response()->json([
                'status' => 'success',
                'message' => 'Image uploaded successfully',
                'image_path' => $img_path . $imageName,
                'image_url' => asset($img_path . $imageName),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image: ' . $e->getMessage(),
            ], 500);
        }
    }


public function settingUpload(Request $request)
{
    Log::info("Image upload request received", $request->all());

    $validated = $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'type' => 'sometimes|in:logo,favicon,slider',
    ]);

    $img_path = 'frontend/assets/images/logos/';
    $isUpdate = $request->input('is_update', false);
    $oldImage = $request->input('old_image');
    $type = $request->input('type', 'logo');

    try {
        $image = $request->file('image');
        $imageName = $type . '_' . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path($img_path);

        // Create directory if it doesn't exist
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // If updating, delete the old image first
        if ($isUpdate && $oldImage) {
            $oldImagePath = public_path($img_path . $oldImage);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Move the new uploaded file
        $image->move($destinationPath, $imageName);

        Log::info("Image uploaded successfully: {$imageName}");

        return response()->json([
            'status' => 'success',
            'image_name' => $imageName,
            'image_url' => asset($img_path . $imageName),
        ]);
    } catch (\Exception $e) {
        Log::error("Image upload failed: " . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload image: ' . $e->getMessage(),
        ], 500);
    }
}

    // public function settingUpload(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //     ]);

    //     $img_path = 'frontend/assets/images/logos/';
    //     $isUpdate = $request->input('is_update', false);
    //     $oldImage = $request->input('old_image');

    //     try {
    //         $image = $request->file('image');
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();
    //         $destinationPath = public_path($img_path);

    //         // Create directory if it doesn't exist
    //         if (!file_exists($destinationPath)) {
    //             mkdir($destinationPath, 0755, true);
    //         }

    //         // If updating, delete the old image first
    //         if ($isUpdate && $oldImage) {
    //             $oldImagePath = public_path($img_path . $oldImage);
    //             if (file_exists($oldImagePath)) {
    //                 unlink($oldImagePath); // Delete old image
    //             }
    //         }

    //         // Move the new uploaded file
    //         $image->move($destinationPath, $imageName);

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Image uploaded successfully',
    //             'image_path' => $img_path . $imageName,
    //             'image_url' => asset($img_path . $imageName),
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to upload image: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    //new blog code
    // public function blogUpload(Request $request)
    // {
    //     // return $request->all();
    //     // Validate the incoming image
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    //     ]);

    //     // Store the image
    //     // $path = $request->file('image')->store('public/blog_images');

    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');

    //         // Generate a unique filename
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();

    //         // Define the destination path (relative to public folder)
    //         $destinationPath = public_path('frontend/assets/images/blog');

    //         // Move the file to destination
    //         $image->move($destinationPath, $imageName);

    //         // Save the path for database storage
    //         $data['image_path'] = 'frontend/assets/images/blog/' . $imageName;
    //     }


    //     return response()->json([
    //         'success' => true,
    //         'data' => $data
    //         // 'url' => Storage::url($path),
    //         // 'path' => $path
    //     ]);
    // }
    //old blog code

    // public function blogUpload(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //     ]);
    //      $img_path = 'frontend/assets/images/blog/';
    //     try {
    //         $image = $request->file('image');
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();

    //         // Define where to store the image (public folder)
    //         $destinationPath = public_path($img_path);

    //         // Create directory if it doesn't exist
    //         if (!file_exists($destinationPath)) {
    //             mkdir($destinationPath, 0755, true);
    //         }

    //         // Move the uploaded file
    //         $image->move($destinationPath, $imageName);

    //         // Return the stored image path (accessible via URL)
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Image uploaded successfully',
    //             'image_path' =>  $img_path . $imageName,
    //             'image_url' => asset( $img_path. $imageName), // Full URL
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to upload image: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
