<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Blog::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })
                ->addColumn('Image', function ($row) {
                    $html = '<img width="20%" src="' . asset("frontend/assets/images/blog/" . $row->img) . '" />';
                    return $html;
                })
                ->addColumn('blog_type', function ($row) {
                    $data = '';
                    if($row->type == 1){
                        $data = "Tools & Expert Device";
                    }
                    if($row->type == 2){
                        $data = "Latest Car Buying Advice";
                    }
                    if($row->type == 3){
                        $data = "Beyond Cars";
                    }
                    return $data;
                })

                ->addColumn('status', function ($row) {
                    // $html = '<p>' .($row->status==1 ? 'Active' : 'Inactive'). '</p>';
                    // return  $html;
                    $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<a
          data-id="' . $row->id . '"
          data-title="' . $row->title . '"
          data-sub_title="' . $row->sub_title . '"
          data-type="' . $row->type . '"
          data-image="' . $row->img . '"
          data-description="' . htmlspecialchars($row->description, ENT_QUOTES, 'UTF-8') . '"
          data-status="' . $row->status . '"
          style="margin-right:3px"
          href="javascript:void(0);"
          class="btn btn-info btn-sm editBtn">
          <i class="fa fa-edit"></i>
           </a>' .
                        '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
           id="news_delete"><i class="fa fa-trash"></i></a>';
                    return $html;
                })->rawColumns(['action', 'Image', 'status'])
                ->make(true);
        }

        return view('backend.admin.blog.index');
    }

    public function add(Request $request)
    {

        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'type' => 'required|string',
                'description' => 'required|string',
                'image' => 'image|mimes:jpeg,png,jpg,gif,Avif,webp|max:2048',
            ], [
                'title.required' => 'Title is required',
                'type.required' => 'Type title is required',
                'description.required' => 'Description is required',
                'image.image' => 'Invalid image format',
                'image.max' => 'Image size should not exceed 2MB',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $blog = new Blog();

            if ($request->hasFile('image')) {
                $path = 'frontend/assets/images/blog/';
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path($path), $imageName);
                $blog->img = $imageName;
            }


            $blog->title = $request->title;
            // $blog->slug = strtolower(str_replace(' ', '-', trim($request->title)));
            $blog->slug = Str::slug($request->title);
            $blog->type = $request->type;
            $blog->status = $request->status;
            $blog->description = $request->description;
            $blog->save();

            return response()->json(['status' => 'success', 'message' => 'Blog added successfully']);
        } else {
            return 'hi';
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'up_title' => 'required|string',
            'up_description' => 'required|string',

        ], [
            'up_description.required' => 'The description field is required.',
            'up_title.required' => 'The title field is required.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $news = Blog::find($request->blog_id);

        // if ($request->hasFile('up_img')) {
        //     $path = 'frontend/assets/images/news/';
        //     $image = $request->file('up_img');
        //     $imageName = time() . '.' . $image->getClientOriginalExtension();

        //     if ($news->img != null) {

        //         unlink(public_path($path) . $news->img);
        //     }

        //     $image->move(public_path($path), $imageName);

        //     $news->img = $imageName;
        // } else {

        //     $news->img = $news->img;
        // }

        if ($request->hasFile('up_img') && isset($request->up_img)) {
            $path = 'frontend/assets/images/blog/';
            $image = $request->file('up_img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            // Delete the old image if it exists
            if ($news->image != null) {
                $oldImagePath = public_path($path) . $news->image;
                if (file_exists($oldImagePath)) {
                    try {
                        unlink($oldImagePath);
                    } catch (\Exception $e) {
                        error_log('Error deleting old image: ' . $e->getMessage());
                    }
                }
            }

            // Move the new image to the specified path
            $image->move(public_path($path), $imageName);
            // Update the link's image attribute with the new image name
            $news->img = $imageName;
        } else {
            // If no new image is uploaded, keep the existing image name
            if ($news->image != null) {
                $news->img = $news->image;  // Keep the existing image
            }
        }
        $news->type = $request->up_type;
        $news->title = $request->up_title;
        // $news->slug = strtolower(str_replace(' ', '-', trim($request->up_title)));
        $news->slug = Str::slug($request->up_title);
        $news->sub_title = $request->up_sub_title;
        $news->description = $request->up_description;
        $news->status = $request->status;
        $news->save();


        return response()->json([
            'status' => 'success',
            'message' => 'Blog update successfully'
        ]);
    }

    public function delete(Request $request)
    {
        $data = Blog::find($request->id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Blog Deleted Successfully'
        ]);
    }


    public function status(Request $request)
    {
        $data = Blog::find($request->id);
        if ($data->status == 1) {
            $data->status = '0';
        } else {
            $data->status = '1';
        }
        $data->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Blog status update successfully'
        ]);
    }
}
