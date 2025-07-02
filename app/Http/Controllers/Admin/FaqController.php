<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    public function faq_show(Request $request)
    {
        if ($request->ajax()) {
            $data = Faq::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($user) {
                    return $user->id; // Use any unique identifier for your rows
                })
                ->addColumn('description', function ($row) {
                    return '<p>' . $row->description . '</p>';
                })
                ->addColumn('description', function ($row) {
                    // Strip HTML tags from the CKEditor content
                    $plainTextDescription = strip_tags($row->description);

                    // Limit the text to 150 words
                    $truncatedDescription = Str::words($plainTextDescription, 20, '...');

                    return $truncatedDescription;
                })
                ->addColumn('status', function ($row) {
                    $html = "<select class='action-select " . ($row->status == 1 ? 'bg-success' : '') . " form-control' style='font-size:10px; font-weight:bold; opacity:97%' data-id='$row->id'>
                                    <option " . ($row->status == 1 ? 'selected' : '') . " value='1'>Active</option>
                                    <option " . ($row->status == 0 ? 'selected' : '') . " value='0'>Inactive</option>
                                </select>";
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    // Edit Button
                    $editButton = '<a 
                               data-id="' . htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8') . '"
                               data-title="' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '"
                               data-type="' . $row->type . '"
                               data-description="' . htmlspecialchars($row->description, ENT_QUOTES, 'UTF-8') . '"
                               data-status="' . htmlspecialchars($row->status, ENT_QUOTES, 'UTF-8') . '"
                               style="margin-right:3px"
                               href="javascript:void(0);"
                               class="btn btn-info btn-sm editFaq">
                               <i class="fa fa-edit"></i>
                           </a>';

                    // Delete Button
                    $deleteButton = '<a 
                                data-id="' . htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8') . '"
                                style="margin-right:3px" 
                                href="javascript:void(0);" 
                                class="btn btn-danger btn-sm"
                                id="faq_delete">
                                <i class="fa fa-trash"></i>
                            </a>';

                    return $editButton . $deleteButton;
                })

                ->rawColumns(['action', 'description', 'status'])
                ->make(true);
        }

        return view('backend/admin/faq/faq-show');
    }


    public function add(Request $request)

    {


        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'type' => 'required|string',
                'description' => 'required|string',

            ], [
                'title.required' => 'Title is required',
                'type.required' => 'Title is required',
                'description.required' => 'Description is required',

            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
            }

            $faq = new Faq();



            $faq->title = $request->title;
            $faq->type = $request->type;
            $faq->status = $request->status;

            $faq->description = $request->description;
            $faq->save();

            return response()->json(['status' => 'success', 'message' => 'Faq added successfully']);
        } else {
            return 'hi';
        }
    }


    public function delete(Request $request)
    {
        $data = Faq::find($request->id);
        $data->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Faq Deleted Successfully'
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



        $faq = Faq::find($request->faq_id);


        $faq->title = $request->up_title;
        $faq->type = $request->up_type;
        $faq->description = $request->up_description;
        $faq->status = $request->status;

        $faq->save();



        return response()->json([
            'status' => 'success',
            'message' => 'faq updated successfully'
        ]);
    }


    public function changeStatus(Request $request)
    {
        $data = Faq::find($request->id);
        if ($data->status == 1) {
            $data->status = '0';
        } else {
            $data->status = '1';
        }
        $data->save();
        return response()->json([
            'status' => 'success',
            'message' => 'faqs status update successfully'
        ]);
    }
}
