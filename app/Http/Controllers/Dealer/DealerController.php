<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;

class DealerController extends Controller
{
    public function index()
    {


        $user = Auth::user();
        return view('backend.dealer.profile.index',compact('user'));
    }


    public function profileUpdate(Request $request)
    {
       $user = Auth::user();
       if ($request->hasFile('image') && isset($request->image)) {
        $path = 'frontend/assets/images/';
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        // Delete the old image if it exists
        if ($user->image != null) {
            $oldImagePath = public_path($path) . $user->image;
            if (file_exists($oldImagePath)) {
                try {
                    unlink($oldImagePath);
                } catch (\Exception $e) {
                    // Handle the unlinking error
                    // You can log the error or perform other actions as needed
                    // For now, just log the error message
                    error_log('Error deleting old image: ' . $e->getMessage());
                }
            }
        }

        // Move the new image to the specified path
        $image->move(public_path($path), $imageName);

        // Update the link's image attribute with the new image name
        $user->image = $imageName;
    } else {
        // If no new image is uploaded, keep the existing image name
        $user->image = $user->image;
    }
       $user->name = $request->name;
       $user->email = $request->email;
       $user->phone = $request->phone;
       $user->address = $request->address;
       $user->gender = $request->gender;
       $user->city = $request->city;
       $user->zip = $request->zip;
       $user->country = $request->country;

       $user->save();
    //    return response()->json([
    //     'status'=>'success',
    //     'message'=>'Dealer update successfully'
    //    ]);
        return redirect()->back()->with('message','Profile Update Successfully');
    }



    public function dealerLogin()
    {
        return view('frontend.dealer.login');
    }

    public function dealerLoginSubmit(Request $request)
    {
        $request->validate([
            'secret_key' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',    // Must contain at least one uppercase letter
                'regex:/[a-z]/',    // Must contain at least one lowercase letter
                'regex:/[0-9]/',    // Must contain at least one digit
                'regex:/[@$!%*#?&]/', // Must contain at least one special character
            ],
            'confirm_password' => 'required|same:password',
        ],
        [
            'password.required' => 'The :attribute field is required.',
            'password.min' => 'The :attribute must be at least :min characters.',
            'password.regex' => 'The :attribute must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',

        ]);


        $user_check = User::where('hashkey',$request->secret_key)->first();
       if($user_check)
       {

        $user_check->password =  Hash::make($request->password);
        $user_check->save();
            if(Auth::attempt(['name' => $user_check->name, 'password' => $request->password]))
            {
                // $redirectUrl = (auth()->check() && auth()->user()->hasAnyRole(['admin', 'editor', 'dealer'])) ? '/admin/dashboard' : '/profile';

                return redirect('/admin/dashboard');

            }
       }else
       {
        return redirect()->back()->with('message','Secret key wrong');
       }
    }

    public function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity for validation errors
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect.'
            ], 400); // 400 Bad Request for incorrect current password
        }

        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'New password cannot be the same as the current password.'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.'
        ]);
    }


}
