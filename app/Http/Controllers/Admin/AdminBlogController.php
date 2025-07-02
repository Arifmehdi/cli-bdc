<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class AdminBlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $data = Blog::orderByDesc('id')->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($data) {
                    return $data->id; // Use any unique identifier for your rows
                })
                ->addColumn('Image', function ($row) {
                    $html = '<img width="20%" src="' . asset("/frontend/assets/images/blog/" . $row->img) . '" />';
                    return $html;
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
          data-category_id="' . $row->category_id . '"
          data-sub_category_id="' . $row->sub_category_id . '"
          data-id="' . $row->id . '"
          data-title="' . $row->title . '"
          data-sub_title="' . $row->sub_title . '"
          data-image="' . $row->img . '"
          data-description="' . htmlspecialchars($row->description, ENT_QUOTES, 'UTF-8') . '"
          data-status="' . $row->status . '"
          data-seo_description="' . $row->seo_description . '"
          data-seo_keyword="' . $row->seo_keyword . '"
          style="margin-right:3px"
          href="javascript:void(0);"
          class="btn btn-info btn-sm editBtn">
          <i class="fa fa-edit"></i>
           </a>' .
        '<a href="javascript:void(0);" data-id="' . $row->id . '" style="margin-right:3px" href="" class="btn btn-primary btn-sm single-news-show"><i class="fa fa-eye"></i></a>' .
        '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm"
           id="news_delete"><i class="fa fa-trash"></i></a>';
                    return $html;
                })->rawColumns(['action', 'Image', 'status'])
                ->make(true);
        }
        $categories = DB::table('blog_categories')->select('id', 'name', 'slug', 'status', 'img')->get();
        $sub_categories = DB::table('blog_sub_categories')->select('id', 'name', 'slug', 'status', 'img')->get();
        return view('backend.admin.blog.blogs', compact('categories', 'sub_categories'));
    }


    public function researchAutoNews(Request $request)
    {
        $routeName = request()->route()->getName(); // "beyond.car.news"
        return $this->getBlogsBySubCategory($request, 1, $routeName); // Assuming 8 is for "Auto News"
    }

    public function researchReviews(Request $request)
    {
        $routeName = request()->route()->getName(); // "beyond.car.news"
        return $this->getBlogsBySubCategory($request, 2, $routeName); // Assuming 8 is for "Auto News"
    }

    public function researchToolsAdvice(Request $request)
    {
        $routeName = request()->route()->getName(); // "beyond.car.news"
        return $this->getBlogsBySubCategory($request, 3, $routeName); // Assuming 8 is for "Auto News"
    }


    public function researchCarBuyingAdvice(Request $request)
    {
        $routeName = request()->route()->getName(); // "beyond.car.news"
        return $this->getBlogsBySubCategory($request, 4, $routeName); // Assuming 8 is for "Auto News"
    }

    
    public function researchCartips(Request $request)
    {
        $routeName = request()->route()->getName(); // "beyond.car.news"
        return $this->getBlogsBySubCategory($request, 5, $routeName); // Assuming 8 is for "Auto News"
    }


    public function beyondcarNews(Request $request)
    { 
        $routeName = request()->route()->getName();
        return $this->getBlogsBySubCategory($request, 8, $routeName); // Assuming 9 is for "Beyond Car News"
    }

    public function beyondcarInnovation(Request $request)
    { 
        $routeName = request()->route()->getName();
        return $this->getBlogsBySubCategory($request, 9, $routeName); // Assuming 9 is for "Beyond Car News"
    }

    public function beyondcarOpinion(Request $request)
    { 
        $routeName = request()->route()->getName();
        return $this->getBlogsBySubCategory($request, 10, $routeName); // Assuming 9 is for "Beyond Car News"
    }

    public function beyondcarFinancial(Request $request)
    { 
        $routeName = request()->route()->getName();
        return $this->getBlogsBySubCategory($request, 11, $routeName); // Assuming 9 is for "Beyond Car News"
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'required|string',
                'image' => 'image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            ], [
                'title.required' => 'Title is required',
                'description.required' => 'Description is required',
                'image.image' => 'Invalid image format',
                'image.max' => 'Image size should not exceed 2MB',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $new = new Blog();
            $new->user_id = Auth::id();
            $new->category_id = $request->category_id;
            $new->sub_category_id = $request->sub_category_id;
            $new->type = $request->sub_category_id;
            $new->title = $request->title;
            $new->slug = strtolower(str_replace(' ', '-', trim($request->title)));
            $new->sub_title = $request->sub_title;
            $new->description = $request->description;

            if ($request->hasFile('image')) {
                $path = 'frontend/assets/images/blog/';
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path($path), $imageName);
                $new->img = $imageName;
            }

            $new->status = $request->status;
            $new->blog_status = 1;
            $new->seo_description = $request->seo_description;
            $keywords = implode(',', str_replace('×', '', $request->keywords));
            $new->seo_keyword = $keywords;
            $new->hash_keyword = $request->hashKeyword;
            $new->save();

            return response()->json(['status' => 'success', 'message' => 'Blog added successfully']);
        } 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'up_title' => 'required|string',
            'up_description' => 'required|string',

        ], [
            'up_description.required' => 'The description field is required.',
            'up_title.required' => 'The title field is required.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }


        $news = Blog::find($request->news_id);
        $news->user_id = Auth::id();
        $news->category_id = $request->category_id;
        $news->sub_category_id = $request->sub_category_id;
        $news->type = $request->sub_category_id;
        $news->title = $request->up_title;
        $news->slug = strtolower(str_replace(' ', '-', trim($request->up_title)));
        $news->sub_title = $request->up_sub_title;
        $news->description = $request->up_description;

        if ($request->hasFile('up_img') && isset($request->up_img)) {
            $path = 'frontend/assets/images/blog/';
            $image = $request->file('up_img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            // Delete the old image if it exists
            if ($news->img != null) {
                $oldImagePath = public_path($path) . $news->img;
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
            if ($news->img != null) {
                $news->img = $news->img;  // Keep the existing image
            }
        }
        
        $news->status = $request->status;
        $news->blog_status = 1;
        $news->seo_description = $request->seo_description;
        $keywords = implode(',', str_replace('×', '', $request->up_keywords));
        $news->seo_keyword = $keywords;
        $news->hash_keyword = $request->up_hashKeyword;
        $news->save();


        return response()->json([
            'status' => 'success',
            'message' => 'Blog update successfully'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function delete(Request $request)
    {
        $data = Blog::find($request->id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'News Deleted Successfully'
        ]);
    }



    protected function getBlogsBySubCategory(Request $request, $subCategoryId, $route)
    {

        $data = Blog::where('sub_category_id', $subCategoryId)->orderByDesc('id')->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($data) {
                    return $data->id;
                })
                ->addColumn('Image', function ($row) {
                    $html = '<img width="20%" src="' . asset("/frontend/assets/images/blog/" . $row->img) . '" />';
                    return $html;
                })
                ->addColumn('status', function ($row) {
                    $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                            </select>";
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $html = '<a
                        data-category_id="' . $row->category_id . '"
                        data-sub_category_id="' . $row->sub_category_id . '"
                        data-id="' . $row->id . '"
                        data-title="' . $row->title . '"
                        data-sub_title="' . $row->sub_title . '"
                        data-image="' . $row->img . '"
                        data-description="' . htmlspecialchars($row->description, ENT_QUOTES, 'UTF-8') . '"
                        data-status="' . $row->status . '"
                        data-seo_description="' . $row->seo_description . '"
                        data-seo_keyword="' . $row->seo_keyword . '"
                        data-hash_keyword="' . $row->hash_keyword . '"
                        style="margin-right:3px"
                        href="javascript:void(0);"
                        class="btn btn-info btn-sm editBtn">
                        <i class="fa fa-edit"></i>
                    </a>' .
                    '<a href="javascript:void(0);" data-id="' . $row->id . '" style="margin-right:3px" href="" class="btn btn-primary btn-sm single-news-show"><i class="fa fa-eye"></i></a>' .
                    '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm" id="news_delete"><i class="fa fa-trash"></i></a>';
                    return $html;
                })
                ->rawColumns(['action', 'Image', 'status'])
                ->make(true);
        }

        $categories = DB::table('blog_categories')->select('id', 'name', 'slug', 'status', 'img')->get();
        $sub_categories = DB::table('blog_sub_categories')->select('id', 'name', 'slug', 'status', 'img')->get();
        
        return view('backend.admin.blog.dynamic_blogs', compact('categories', 'sub_categories', 'route'));
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
