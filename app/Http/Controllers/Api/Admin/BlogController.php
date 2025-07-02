<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index(Request $request, $id)
    {
        $query = DB::table('blogs')
            ->where('blog_status', $id)
            ->select([
                'id',
                'user_id',
                'category_id',
                'sub_category_id',
                'owner_name',
                'type',
                'title',
                'slug',
                'sub_title',
                'description',
                'img',
                'status',
                'blog_status',
                'created_at'
            ])
            ->orderBy('created_at', 'desc'); // Added ordering

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


    public function updateStatus(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|boolean',
        ]);

        try {
            $blog = Blog::findOrFail($id); // Use findOrFail() to throw 404 if not found
            $blog->status = $request->status; // Fixed typo (was 'staus')
            $blog->save();

            return response()->json([
                'message' => 'Blog status updated successfully',
                'new_status' => $blog->status, // Optionally return the new status
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Blog not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }
}
