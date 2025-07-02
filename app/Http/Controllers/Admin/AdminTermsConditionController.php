<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class AdminTermsConditionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TermsCondition::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; 
                })
                ->addColumn('description', function ($row) {
                    // Strip HTML tags from the CKEditor content
                    $plainTextDescription = strip_tags($row->description);

                    // Limit the text to 150 words
                    $truncatedDescription = Str::words($plainTextDescription, 20, '...');

                    return $truncatedDescription;
                })
                ->addColumn('action', function ($row) {
                    $editButton = '<a data-id="' . htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8') . '" 
                   data-title="' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '" 
                   data-description="' . htmlspecialchars($row->description, ENT_QUOTES, 'UTF-8') . '" 
                   style="margin-right:3px" 
                   href="javascript:void(0);" 
                   class="btn btn-info btn-sm editTermsCondition">
                   <i class="fa fa-edit"></i>
                   </a>';

                    $deleteButton = '<a data-id="' . htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8') . '" 
                     style="margin-right:3px" 
                     href="javascript:void(0);" 
                     class="btn btn-danger btn-sm" 
                     id="termsCondition_delete">
                     <i class="fa fa-trash"></i>
                    </a>';

                    return $editButton . $deleteButton;
                })

                ->rawColumns(['action', 'description'])
                ->make(true);
        }
        return view('backend.admin.terms-condition.index');
    }

    public function store(Request $request)
    {

        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'required|string',

            ], [
                'title.required' => 'Title is required',
                'description.required' => 'Description is required',

            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $faq = new TermsCondition();
            $faq->title = $request->title;
            $faq->description = $request->description;
            $faq->save();

            return response()->json(['status' => 'success', 'message' => 'Terms Condition added successfully']);
        } else {
            return 'hi';
        }
    }


    public function delete(Request $request)
    {
        $data = TermsCondition::find($request->id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Terms Condition Deleted Successfully'
        ]);
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

        $faq = TermsCondition::find($request->faq_id);
        $faq->title = $request->up_title;
        $faq->description = $request->up_description;
        $faq->save();

        return response()->json([
            'status' => 'success',
            'message' => 'terms & condition updated successfully'
        ]);
    }
}
