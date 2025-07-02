<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required|confirmed|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $admin = Admin::create($validated);

        // use it  send email for user verication
        // $admin->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Successfully registered. Please check your email for verification.',
            'admin' => $admin
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins',
            'password' => 'required|string',
        ]);

        // Find the admin by email
        $admin = Admin::where('email', $request->email)->first();

        // Check password
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }

        // Create token
        $token = $admin->createToken('admin-token')->plainTextToken;
        $name = $admin->name;
        return response()->json([
            'message' => 'Successfully logged in as admin',
            'admin' => $admin,
            'name' => $name,
            'token' => $token
        ]);
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (Auth::attempt($credentials)) {
    //         return response()->json(['message' => 'Authenticated']);
    //     }

    //     return response()->json(['message' => 'Invalid credentials'], 401);
    // }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete(); // Sanctum token revocation
        // return response()->json(['message' => 'Logged out']);
        return response()->json([
            'message' => 'Logged out',
            'status' => 204
        ]);
    }


    // just simple refgister
    // public function register(Request $request)
    // {
    //     $admin = Admin::create($request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:admins',
    //         'password'=> 'required|confirmed|min:8',
    //     ]));

    //     $admin->sendEmailVerificationNotification();

    //     return response()->json([
    //         'message' => 'Successfully Registered',
    //         'admins' => $admin
    //     ]);

    // }


    // register with email verification





    // old login
    //     public function login(Request $request)
    // {
    //     $fields = $request->validate([
    //         'email' => 'required|email|exists:admins',
    //         'password' => 'required|string',
    //     ]);

    //     $admin = Admin::where('email', $fields['email'])->first();

    //     if (!Auth::guard('admin')->attempt(['email' => $fields['email'], 'password' => $fields['password']])) {
    //         throw ValidationException::withMessages([
    //             'email' => ['The provided credentials are incorrect']
    //         ]);
    //     }

    //     $token = $admin->createToken('admin-token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Successfully logged in as admin',
    //         'user' => $admin,
    //         'token' => $token
    //     ]);
    // }



    // public function login(Request $request)
    // {
    //     $fields = $request->validate([
    //         'email' => 'required|email|exists:admins', // Check email exists in admins table
    //         'password' => 'required|string',
    //         'remember' => 'boolean'
    //     ]);

    //     $credentials = [
    //         'email' => $fields['email'],
    //         'password' => $fields['password']
    //     ];

    //     // Use the 'admin' guard for authentication
    //     if(!Auth::guard('admin')->attempt($credentials, $fields['remember'])){
    //         throw ValidationException::withMessages([
    //             'email' => ['The provided credentials are incorrect']
    //         ]);
    //     }

    //     session()->regenerate();
    //     return response()->json([
    //         'message' => 'Successfully logged in as admin',
    //         'user' => Auth::guard('admin')->user()
    //     ]);
    // }


    // public function logout(Request $request)
    // {
    //     Auth::guard('web')->logout();
    //     return response(status:204);
    // }


}
