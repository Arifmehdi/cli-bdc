<?php

namespace App\Http\Controllers\Api\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $fileds = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password'=> 'required|confirmed',
        ]);

        $dealer = Dealer::create($fileds);
        $token = $dealer->createToken($request->name);
        return [
            'dealer' => $dealer,
            'token' => $token->plainTextToken
        ];

    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:dealers',
            'password' => 'required'
        ]);

        $dealer = Dealer::where('email', $request->email)->first();

        if(!$dealer || !Hash::check($request->password, $dealer->password)){
            return [
                'the provided credentials are incorrect'
            ];
        }
        $token = $dealer->createToken($dealer->name);
        return [
            'dealer' => $dealer,
            'token' => $token->plainTextToken
        ];
    }



    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return [
            'message' => "You are logged out"
        ];
    }
}
