<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function add()
    {
        return view('backend.admin.page.page_add');
    }

    public function uploadImage(Request $request)
    {
        if($request->hasFile('upload'))
        {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' .$extension;

            $request->file('upload')->move(public_path('media'),$fileName);
            $url = asset('media/' .$fileName);
            return response()->json(['fileName'=>$fileName, 'uploaded' => 1, 'url' =>$url]);

        }
    }


    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'page_title' => 'required|max:255|unique:pages,title',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $keywords = implode(',', str_replace('×', '', $request->keywords));
        $page =  new Page();
        $page->title = $request->page_title;
        $page->slug = $request->permalink;
        $page->description = $request->description;
        $page->seo_title = $request->seo_title;
        $page->seo_description = $request->seo_description;
        $page->seo_keyword =  $keywords;
        $page->created_by = Auth::id();
        $page->save();
        return redirect()->back()->with('message','Page Created Successfully');
    }


    public function DynamicView(Request $request)
    {
        $content = Page::where('slug',$request->slug)->first();
        if ($content && $content->status == '1') {
            return view('frontend.dynamic.page-view', compact('content'));
        } else {

            abort(403);
        }

    }


    public function show()
    {

        $pages = Page::orderBy('id','desc')->get();
        return view('backend.admin.page.show-page',compact('pages'));
    }


    public function edit($id)
    {
        $page_content = Page::find($id);
        $page_content['header_title'] = 'Edit Page';
        // return $page_content;
        return view('backend.admin.page.page_add',compact('page_content'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'page_title' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        $keywords = implode(',', str_replace('×', '', $request->keywords));
        $update_page = Page::find($id);
        $update_page->title = $request->page_title;
        $update_page->slug = $request->permalink;
        $update_page->description = $request->description;
        $update_page->seo_title = $request->seo_title;
        $update_page->seo_description = $request->seo_description;
        $update_page->seo_keyword =  $keywords;
        $update_page->created_by = Auth::id();
        $update_page->save();
        return redirect()->back()->with('message','Page updated Successfully');
    }


    public function checkDuplicateTitle(Request $request)
    {
        $title = $request->input('title');
        $existingPage = Page::where('title', $title)->first();
        if ($existingPage) {
            return response()->json(['exists' => true]);
        } else {
            return response()->json(['exists' => false]);
        }
    }


    public function delete(Request $request)
    {
        $page = Page::find($request->id);
        $page->delete();
        return response()->json(['status'=>'success','message' => 'Page deleted successfully']);
    }


    public function status(Request $request){
        $data = Page::find($request->id);
        if($data->status==1){
            $data->status ='0';
        }else{
            $data->status ='1';
        }
        $data->save();
        return response()->json([
            'status'=>'success',
            'message'=>'page status update successfully'
        ]);
    }


    public function showStaticPage()
    {
        $pages = StaticPage::get();
        return view('backend.admin.page.static-page',compact('pages'));
    }

    public function updateStaticPage(Request $request)
    {
       $page = StaticPage::find($request->id);
       $page->title = $request->title;
       $page->description = $request->description;
       $page->keyword = $request->keyword;
       $page->save();
       return response()->json(['status'=>'success','message'=>'page updated successfully']);
    }

    public function updateStaticPageStatus(Request $request)
    {
        $page = StaticPage::find($request->id);
        if($page->status==1){
            $page->status ='0';
        }else{
            $page->status ='1';
        }
        $page->save();
        return response()->json([
            'status'=>'success',
            'message'=>'page status updated successfully'
        ]);
    }

}
