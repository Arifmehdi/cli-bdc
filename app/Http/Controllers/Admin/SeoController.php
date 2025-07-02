<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class SeoController extends Controller
{
    public function add()
    {

        $seo_obj =  Seo::query();
        $seos =  $seo_obj->orderBy('id','desc')->get();
        $seo_first = $seo_obj->first();
        $seo_id = $seo_first->id;
        return view('backend.admin.seo.show-seo',compact('seos','seo_first','seo_id'));
    }

    public function store(Request $request)
    {

        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:seos,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        $keywords = implode(',', str_replace('×', '', $request->keywords));
        $seo_insert = new Seo();
        $seo_insert->name = $request->name;
        $seo_insert->description = $request->description;
        $seo_insert->keyword = $keywords;
        $seo_insert->og_title = $request->og_title;
        $seo_insert->og_description = $request->og_description;

        if($request->hasFile('og_img'))
        {
           
            $seo_insert->og_img = $this->OgimageUpload($request->file('og_img'));
        }

        $seo_insert->og_url = $request->og_url;
        $seo_insert->og_type = $request->og_type;
        $seo_insert->og_site_name = $request->og_site_name;
        $seo_insert->og_locale = $request->og_locale;
        $seo_insert->twitter_card = $request->twitter_card;
        $seo_insert->twitter_title = $request->twitter_title;
        $seo_insert->twitter_description = $request->twitter_description;
        if($request->hasFile('twitter_img'))
        {

            $seo_insert->twitter_img = $this->TwitterimageUpload($request->file('twitter_img'));
        }
        $seo_insert->twitter_site = $request->twitter_site;
        $seo_insert->twitter_creator = $request->twitter_creator;
        $seo_insert->gtm = $request->gtm;
        $seo_insert->app_id = $request->app_id;
        $seo_insert->save();
        return redirect()->back()->with('message','seo tag added successfully');
    }

    public function edit($id)
    {
        $seo = Seo::find($id);

        return view('backend.admin.seo.edit-seo',compact('seo'));
    }

    public function update(Request $request, $id)
    {
        // return $request->all();
        $seo = Seo::find($id);
        $keywords = implode(',', str_replace('×', '', $request->keywords));
        $seo->name = $request->name;
        $seo->keyword = $keywords;
        $seo->description = $request->description;
        $seo->og_title = $request->og_title;
        $seo->og_description = $request->og_description;

        if($request->hasFile('og_img'))
        {

            if ($seo->og_img && file_exists(public_path('seo/' . $seo->og_img))) {
                unlink(public_path('seo/' . $seo->og_img));
            }

            // dd($request->file('og_img'));
            // Upload and save the new image
            $seo->og_img = $this->OgimageUpload($request->file('og_img'));
        } else {
            // No new file uploaded, retain the old image
            $seo->og_img = $seo->og_img;
        }

        $seo->og_url = $request->og_url;
        $seo->og_type = $request->og_type;
        $seo->og_site_name = $request->og_site_name;
        $seo->og_locale = $request->og_locale;
        $seo->twitter_card = $request->twitter_card;
        $seo->twitter_title = $request->twitter_title;
        $seo->twitter_description = $request->twitter_description;
        if($request->hasFile('twitter_img'))
        {

            if ($seo->twitter_img && file_exists(public_path('seo/' . $seo->twitter_img))) {
                unlink(public_path('seo/' . $seo->twitter_img));
            }
            // Upload and save the new image
            $seo->twitter_img = $this->TwitterimageUpload($request->file('twitter_img'));
        }else {
            // No new file uploaded, retain the old image
            $seo->twitter_img = $seo->twitter_img;
        }
        $seo->twitter_site = $request->twitter_site;
        $seo->twitter_creator = $request->twitter_creator;
        $seo->gtm = $request->gtm;
        $seo->app_id = $request->app_id;
        $seo->save();
        return redirect()->route('admin.frontend.add.seo')->with('message','Seo Tag Updated Successfully');
    }

    public function delete(Request $request)
    {
        Seo::find($request->id)->delete();
        return response()->json(['status'=>'success','message' => 'Seo deleted successfully']);
    }

    public function globalSEO()
    {
        $seo = Seo::orderBy('id','desc')->where('status',1)->first();
        return $seo;
    }


    public function OgimageUpload($image)
    {
        if ($image) {
            $imageName = Str::uuid() . '.' . $image->extension();
            $image->move(public_path('seo'), $imageName);
            return $imageName;
        } else {
            return null;
        }
    }

    public function TwitterimageUpload($image)
    {
        if ($image) {
            $imageName = Str::uuid() . '.' . $image->extension();
            $image->move(public_path('seo'), $imageName);
            return $imageName;
        } else {
            return null;
        }
    }

}
