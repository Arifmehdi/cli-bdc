<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Inventory;
use App\Models\MainInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class InventoryController extends Controller
{
    // public function index()
    // {
    //     $inventories = Inventory::latest()->get();
    //     return view('')
    // }


    // public function updateWishList(Request $request)
    // {


    //     if ($request->ajax()) {
    //         // session()->flush();
    //         if(auth()->id())
    //         {
    //             $favourites = session()->get('favourite');
    //             $inventory = Inventory::find($request->inventory_id);
    //             // Check if $favourites is not an array or is empty
    //             if (!is_array($favourites) || empty($favourites)) {
    //                 $favourites = [];
    //             }

    //             $favouriteExists = false;
    //             foreach ($favourites as $key => $fav) {
    //                 if (isset($fav['id']) && $fav['id'] === $inventory->id) {
    //                     $favouriteExists = true;
    //                     unset($favourites[$key]); // Remove the item from the array
    //                     // Favourite::find($inventory->id)->delete();
    //                     Favourite::where('inventory_id',$request->inventory_id)->forceDelete();
    //                     break;
    //                 }
    //             }

    //             if ($favouriteExists) {
    //                 // Update the session with the modified favourites array
    //                 session()->put('favourite', $favourites);
    //                 return response()->json([
    //                     'action' => 'remove',
    //                     'message' => 'Removed from favorites',
    //                 ]);
    //             } else {

    //                 $images = explode(',',$inventory->local_img_url);
    //                 $dato_formate = \Carbon\Carbon::parse($inventory->created_date);
    //                 $newFavourite = [
    //                     'id' => $inventory->id,
    //                     'title' => $inventory->title,
    //                     'date_in_stock' => $dato_formate->diffForHumans(),
    //                     'fuel' => $inventory->fuel,
    //                     'miles_formate' => $inventory->miles,
    //                     'desc' => substr($inventory->dealer_comment ,0,180),
    //                     'engine_description_formate' => $inventory->engine_details,
    //                     'dealer_state' => $inventory->dealer_city,
    //                     'dealer_state' => $inventory->dealer_state,
    //                     'year' => $inventory->year,
    //                     'model' => $inventory->model,
    //                     'make' => $inventory->make,
    //                     'img'=>$images[0],
    //                     'img_count'=>count($images),
    //                     'price_formate' => $inventory->price,
    //                     'transmission' => $inventory->transmission,
    //                 ];

    //                 $favourites[] = $newFavourite;
    //                 $favourite_save = new Favourite();
    //                 $favourite_save->inventory_id = $inventory->id;
    //                 $favourite_save->user_id = Auth::id();
    //                 $favourite_save->ip_address = $request->ip();
    //                 $favourite_save->save();
    //                 session()->put('favourite', $favourites);

    //                 return response()->json([
    //                     'action' => 'add',
    //                     'message' => 'Added to favorites',
    //                     'favourite' => $newFavourite,
    //                 ]);
    //             }



    //         }
    //         else
    //         {


    //             $favourites = session()->get('favourite');
    //             $inventory = Inventory::find($request->inventory_id);
    //             // Check if $favourites is not an array or is empty
    //             if (!is_array($favourites) || empty($favourites)) {
    //                 $favourites = [];
    //             }

    //             $favouriteExists = false;
    //             foreach ($favourites as $key => $fav) {
    //                 if (isset($fav['id']) && $fav['id'] === $inventory->id) {
    //                     $favouriteExists = true;
    //                     unset($favourites[$key]); // Remove the item from the array
    //                     break;
    //                 }
    //             }

    //             if ($favouriteExists) {
    //                 // Update the session with the modified favourites array
    //                 session()->put('favourite', $favourites);

    //                 return response()->json([
    //                     'action' => 'remove',
    //                     'message' => 'Removed from favorites',
    //                 ]);
    //             } else {
    //                 $images = explode(',',$inventory->local_img_url);
    //                 $dato_formate = \Carbon\Carbon::parse($inventory->created_date);
    //                 $newFavourite = [
    //                     'id' => $inventory->id,
    //                     'title' => $inventory->title,
    //                     'date_in_stock' => $dato_formate->diffForHumans(),
    //                     'fuel' => $inventory->fuel,
    //                     'miles_formate' => $inventory->miles,
    //                     'desc' => substr($inventory->dealer_comment ,0,180),
    //                     'engine_description_formate' => $inventory->engine_details,
    //                     'dealer_state' => $inventory->dealer_city,
    //                     'dealer_state' => $inventory->dealer_state,
    //                     'year' => $inventory->year,
    //                     'model' => $inventory->model,
    //                     'make' => $inventory->make,
    //                     'img'=>$images[0],
    //                     'img_count'=>count($images),
    //                     'price_formate' => $inventory->price,
    //                     'transmission' => $inventory->transmission,
    //                 ];

    //                 $favourites[] = $newFavourite;
    //                 session()->put('favourite', $favourites);

    //                 return response()->json([
    //                     'action' => 'add',
    //                     'message' => 'Added to favorites',
    //                     'favourite' => $newFavourite,
    //                 ]);
    //             }
    //         }

    //         }


    // }




// public function updateWishList(Request $request)
// {
//     if ($request->ajax()) {
//         // dd($request->all());
//         // Get the favorites from the cookie or initialize an empty array
//         $favourites = json_decode(Cookie::get('favourite', '[]'), true);
//         $inventory = Inventory::find($request->inventory_id);

//         if (!$inventory) {
//             return response()->json(['message' => 'Inventory not found'], 404);
//         }

//         // Check if user is authenticated
//         $isAuthenticated = auth()->check();
//         $favouriteExists = false;

//         // Check if inventory exists in the cookie favorites
//         foreach ($favourites as $key => $fav) {
//             if (isset($fav['id']) && $fav['id'] === $inventory->id) {
//                 $favouriteExists = true;
//                 unset($favourites[$key]); // Remove the item from the array

//                 if ($isAuthenticated) {
//                     Favourite::where('inventory_id', $request->inventory_id)->forceDelete();
//                 }
//                 break;
//             }
//         }

//         if ($favouriteExists) {
//             // Update the cookie with the modified favourites array
//             Cookie::queue('favourite', json_encode($favourites), 43200); // Cookie lasts 30 days

//             return response()->json([
//                 'action' => 'remove',
//                 'message' => 'Removed from favorites',
//             ]);
//         } else {

//             $newFavourite = [
//                 'id' => $inventory->id,
//             ];

//             $favourites[] = $newFavourite;

//             // Update the cookie with the new favourite
//             Cookie::queue('favourite', json_encode($favourites), 43200); // Cookie lasts 30 days

//             // Save to DB if user is authenticated
//             if ($isAuthenticated) {
//                 $favourite = new Favourite();
//                 $favourite->inventory_id = $inventory->id;
//                 $favourite->user_id = Auth::id();
//                 $favourite->ip_address = $request->ip();
//                 $favourite->save();
//             }

//             return response()->json([
//                 'action' => 'add',
//                 'message' => 'Added to favorites',
//             ]);
//         }
//     }
// }


public function updateWishList(Request $request)
{
    if ($request->ajax()) {
        // Get the favorites from the session or initialize an empty array
        $favourites = session()->get('favourite', []);
        // $inventory = Inventory::find($request->inventory_id);
        $inventory = MainInventory::find($request->inventory_id);

        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        // Check if user is authenticated
        $isAuthenticated = auth()->check();
        $favouriteExists = false;

        // Check if inventory exists in the session favorites
        foreach ($favourites as $key => $fav) {
            if (isset($fav['id']) && $fav['id'] === $inventory->id) {
                $favouriteExists = true;
                unset($favourites[$key]); // Remove the item from the array

                if ($isAuthenticated) {
                    Favourite::where('inventory_id', $request->inventory_id)->forceDelete();
                }
                break;
            }
        }

        if ($favouriteExists) {
            // Update the session with the modified favourites array
            session()->put('favourite', $favourites);

            return response()->json([
                'action' => 'remove',
                'message' => 'Removed from favorites',
            ]);
        } else {
            $newFavourite = [
                'id' => $inventory->id,
            ];

            $favourites[] = $newFavourite;

            // Update the session with the new favourite
            session()->put('favourite', $favourites);

            // Save to DB if user is authenticated
            if ($isAuthenticated) {
                $favourite = new Favourite();
                $favourite->inventory_id = $inventory->id;
                $favourite->user_id = Auth::id();
                $favourite->ip_address = $request->ip();
                $favourite->save();
            }

            return response()->json([
                'action' => 'add',
                'message' => 'Added to favorites',
            ]);
        }
    }
}




}
