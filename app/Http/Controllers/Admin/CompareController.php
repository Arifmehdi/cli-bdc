<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compare;
use Illuminate\Http\Request;

class CompareController extends Controller
{

    public function index(){
        $ip = request()->ip();
        $items = Compare::with('mainInventory')->where('ip',$ip)->get();
        return view('frontend.compare', compact('items'));
    }
    public function add(Request $request){


        $existingItem = Compare::where('inventory_id',$request->id)->get();

        if($existingItem->isEmpty()){

            $limit = Compare::where('ip',$request->ip())->count();

            if( $limit < 3){
                $com = new Compare();
                $com->inventory_id = $request->id;
                $com->ip = $request->ip();
                $com->save();
            }else{
                return response()->json([

                    'status' => 'error',
                    'message' => 'You have reached 3 vehicle max. Remove a vehicle to add another.',


                ]);
            }



            return response()->json([
                'status' => 'success',
                'message'=>'Add comparision successfully',
                'limit'=> $limit,

            ]);

        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'This listing already exists in comparison',

            ]);
        }

}



public function collect(Request $request) {
    $coms = Compare::with('mainInventory')->where('ip', $request->ip())->get();
    $coms_array = [];

    // Function to truncate title to a specified number of words
    // Define this as a private method if it's in a class
    function truncateTitle($title, $wordLimit) {
        $words = explode(' ', $title);

        if (count($words) > $wordLimit) {
            return implode(' ', array_slice($words, 0, $wordLimit)) . '...';
        }
        return $title;
    }

    foreach ($coms as $com) {
        $image_obj = $com->mainInventory->additionalInventory->local_img_url;

        $image_splice = explode(',', $image_obj);

        // Clean up the image string and ensure it includes the filename and extension
        $image = str_replace(["[", "]", "'"], "", $image_splice[0]);

        $image_path = asset($image);

        $coms_array[] = [
            'id' => $com->id,
            'title' => truncateTitle($com->mainInventory->title, 3), // Truncate title to 15 words
            'image_path' => $image_path,
            'price' => $com->mainInventory->price_formate // Include price
        ];
    }

    return response()->json([
        'status' => 'success',
        'coms' => $coms_array,
    ]);
}


public function deleteItem($id) {
    try {
        $inventory = Compare::findOrFail($id);
        $inventory->delete(); // Delete the item from the database

        return response()->json(['status' => 'success', 'message' => 'Item deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Failed to delete item']);
    }
}




}
