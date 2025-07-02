<?php

namespace App\Http\Controllers\Admin;


use App\Models\LocationCity;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class GeneralSettingController extends Controller
{
    // public function index(){
    //     return view('backend.admin.setting.general-setting-update');
    // }

    public function index(Request $request)
    {
        if($request->ajax()){
            // $data = GeneralSetting::first();
            $data = collect([GeneralSetting::first()]);;

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('DT_RowIndex', function ($user) {
                return $user->id;
            })
            ->addColumn('logo', function($row) {
                $html = '<img width="50%" src="' . asset("frontend/assets/images/logos/" . $row->image) . '" />';
                return $html;
            })
            ->addColumn('slider', function($row) {
                $html = '<img width="50%" src="' . asset("frontend/assets/images/logos/" . $row->slider_image) . '" />';
                return $html;
            })
            ->addColumn('favicon', function($row) {
                $html = '<img width="40%" src="' . asset("frontend/assets/images/logos/" . $row->fav_image) . '" />';
                return $html;
            })
            ->addColumn('upload_by', function($row) {
                return $row->userName->name ?? 'N/A';
            })

            ->addColumn('action', function($row){
                $html = '<a
                data-id="' . $row->id . '"
                data-site_title = "' . $row->site_title . '"
                data-email = "' . $row->email . '"
                data-slider_title = "' . $row->slider_title . '"
                data-slider_subtitle = "' . $row->slider_subtitle . '"
                data-phone = "' . $row->phone . '"
                data-language = "' . $row->language . '"
                data-pagination = "' . $row->pagination . '"
                data-separator = "' . $row->separator . '"
                data-timezone = "' . $row->timezone . '"
                data-date_formate = "' . $row->date_formate . '"
                data-time_formate = "' . $row->time_formate . '"
                data-site_map = "' . $row->site_map . '"
                data-image ="' . $row->image . '"
                data-fav_image ="' . $row->fav_image . '"
                data-slider_image ="' . $row->slider_image . '"

                style="margin-right:3px"
                href="javascript:void(0);"

                class="btn btn-info btn-sm editLogo">
                <i class="fa fa-edit"></i>
                </a>';
                return $html;
            })
            ->rawColumns(['action', 'logo','favicon', 'slider'])
            ->make(true);
       }
        return view('backend.admin.setting.index');
    }


    public function update(Request $request){
        if ($request->ajax()) {
            $data = GeneralSetting::find($request->setting_id);

            if ($request->hasFile('image') && isset($request->image)) {
                $path = 'frontend/assets/images/logos/';
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();

                // Delete the old image if it exists
                if (!empty($data->image) && file_exists(public_path($path) . $data->image)) {
                    unlink(public_path($path) . $data->image);
                }

                // Move the new image to the specified path
                $image->move(public_path($path), $imageName);

                // Update the link's image attribute with the new image name
                $data->image = $imageName;
            }

            if ($request->hasFile('fav_image') && isset($request->fav_image)) {
                $path = 'frontend/assets/images/logos/';
                $fav_image = $request->file('fav_image');
                $imageName = time() . '.' . $fav_image->getClientOriginalExtension(); // Use $fav_image here

                // Delete the old image if it exists
                if (!empty($data->fav_image) && file_exists(public_path($path) . $data->fav_image)) {
                    unlink(public_path($path) . $data->fav_image);
                }

                // Move the new image to the specified path
                $fav_image->move(public_path($path), $imageName);

                // Update the link's image attribute with the new image name
                $data->fav_image = $imageName;
            }
            if ($request->hasFile('slider_image') && isset($request->slider_image)) {
                $path = 'frontend/assets/images/logos/';
                $slider_image = $request->file('slider_image');
                $imageName = time() . '.' . $slider_image->getClientOriginalExtension(); // Use $fav_image here

                // Delete the old image if it exists
                if (!empty($data->slider_image) && file_exists(public_path($path) . $data->slider_image)) {
                    unlink(public_path($path) . $data->slider_image);
                }

                // Move the new image to the specified path
                $slider_image->move(public_path($path), $imageName);

                // Update the link's image attribute with the new image name
                $data->slider_image = $imageName;
            }
             $data->site_title = $request->site_title;
             $data->email = $request->email;
             $data->slider_title = $request->slider_title;
             $data->slider_subtitle = $request->slider_subtitle;
             $data->phone = $request->phone;
             $data->pagination = $request->pagination;
             $data->separator = $request->separator;
             $data->timezone = $request->timezone;
             $data->language = $request->language;
             $data->date_formate = $request->date_formate;
             $data->time_formate = $request->time_formate;
             $data->apr_rate = $request->apr;
             $data->save();

            return response()->json(['status' => 'success', 'message' => 'General Setting update successfully']);
        } else {
            return 'hi';
        }
    }

    public function state_search(string $id)
    {
        $cities_obj = LocationCity::where('location_state_id',$id)->orderBy('city_name')->pluck('city_name','id')->toArray();
        asort($cities_obj);
        $cities = [];
        foreach ($cities_obj as $id => $city) {
            $cities[] = [
                'id' => $id,
                'city' => $city
            ];
        }


        return response()->json($cities);
    }

    // public function identify(Request $request){
    //     if($request->ajax()){
    //     $data = GeneralSetting::find($request->setting_id);
    //     return response()->json([
    //         'status'=>'success',
    //         'data'=> $data
    //     ]);
    //     }

    // }
}
