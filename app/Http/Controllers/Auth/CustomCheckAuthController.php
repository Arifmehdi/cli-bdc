<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordForget;
use App\Mail\VerifyEmail;
use App\Mail\WelcomeEmail;
use App\Models\Favourite;
use App\Models\Inventory;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class CustomCheckAuthController extends Controller
{



    public function googleLogin()
    {

        return Socialite::driver('google')->redirect();
    }

    public function facebookLogin()
    {

        return Socialite::driver('facebook')->redirect();
    }

    public function googleHandle()
    {
        try {

            $user = Socialite::driver('google')->user();

            $findUser = User::where('email', $user->email)->first();
            if (!$findUser) {
                $createUser = new User();
                $createUser->name = $user->name;
                $createUser->email = $user->email;
                $createUser->image = $user->avatar;
                $createUser->password = Hash::make('Test@12345');
                $createUser->role_id = 3;
                $createUser->save();
                $findUser = $createUser;
            }

            Auth::login($findUser);
            return redirect('/profile');
        } catch (Exception $e) {
            // Log the exception for debugging purposes
            \Log::error('Google authentication error: ' . $e->getMessage());

            // Redirect the user to an error page or display a custom error message
            return redirect('/error')->with('error', 'An error occurred during Google authentication');
        }
    }

    public function facebookHandle()
    {
        try {

            $user = Socialite::driver('facebook')->user();

            $findUser = User::where('email', $user->email)->first();
            if (!$findUser) {
                $createUser = new User();
                $createUser->name = $user->name;
                $createUser->email = $user->email;
                $createUser->image = $user->avatar;
                $createUser->password = Hash::make('Test@12345');
                $createUser->role_id = 3;
                $createUser->save();
                $findUser = $createUser;
            }

            Auth::login($findUser);
            return redirect('/profile');
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
    public function checkMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',

        ], [
            'email.email' => 'The email field must contain a valid email address.',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $checkEmail = User::withTrashed()->where('email', $request->email)->first();
        Session::put('email', $request->email);
        if ($checkEmail) {
            if ($checkEmail->trashed()) {
                return response()->json([
                    'status' => 2,
                    'message' => 'Your account is inactive. Please contact the administrator.',
                ], 200); // Ensure HTTP 200 response
            }
            return response()->json([
                'status' => 1,
                'email' => $request->email
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'email' => $request->email
            ]);
        }
    }

    public function login(Request $request)
    {

        // return $request->all();



        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'mathcaptcha' => ['required', 'mathcaptcha'],
        ], [
            'mathcaptcha.required' => 'Captcha is required.',
            'mathcaptcha.mathcaptcha' => 'Answer is incorrect.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()]);
        }

        $email = session()->has('email') ? session('email') : null;

        $user = User::where('email',$email)->first();
        if ($user && is_null($user->password)) {
            return response()->json(['status' => 'incorrect', 'message' => 'Password is incorrect']);
        }
// dd($user->role_id);
        if (Auth::attempt(['email' => $email, 'password' => $request->password])) {
            // $redirectUrl = (auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasAllRoles('dealer'))) ? '/admin/dashboard' : '/profile';
            $redirectUrl = (auth()->check() && auth()->user()->hasAnyRole(['admin', 'editor', 'dealer'])) ? '/admin/dashboard' : '/profile';

             // Get favorites from the cookie (for guest users)
            $cookieFavorites = json_decode(Cookie::get('favourite', '[]'), true); // Get favorite items from the cookie
            $favoriteIds = collect($cookieFavorites)->pluck('id')->toArray(); // Extract favorite inventory IDs

            // Check if there are any favorite items in the cookie
            if (!empty($favoriteIds)) {
                // Fetch the favorite inventory items from the database
                $favorites = Inventory::whereIn('id', $favoriteIds)->get();

                if ($favorites) {
                    foreach ($favorites as $favorite) {
                        // Check if the favorite already exists for the authenticated user in the database
                        $existingFavorite = Favourite::where('user_id', Auth::id())
                            ->where('inventory_id', $favorite->id)
                            ->first();

                        // If the favorite does not exist, save it to the database for the logged-in user
                        if (!$existingFavorite) {
                            $favorite_add = new Favourite();
                            $favorite_add->inventory_id = $favorite->id;
                            $favorite_add->user_id = Auth::id();
                            $favorite_add->ip_address = $request->ip(); // Store the user's IP address
                            $favorite_add->save();
                        }
                    }
                }
            }

            $role = auth()->user()->getRoleNames()->first() ?? null ;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successfully',
                'role' => $role, // Get the first assigned role
                'redirectUrl' => $redirectUrl,
            ]);

            // return response()->json(['status' => 'success', 'message' => 'Login successfully']);
            // return response()->json(['status' => 'success', 'message' => 'Login successfully', 'redirectUrl' => $redirectUrl]);
        } else {
            return response()->json(['status' => 'incorrect', 'message' => 'Password is incorrect']);
        }
    }



    public function store(Request $request)
    {

        // return $request->all();
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',

            ],
            'mathcaptcha' => ['required', 'mathcaptcha'],
        ], [
            'password.required' => 'The :attribute field is required.',
            'password.min' => 'The :attribute must be at least :min characters.',
            'password.regex' => 'The :attribute must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
            'mathcaptcha.required' => 'Captcha is required.',
            'mathcaptcha.mathcaptcha' => 'Answer is incorrect.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()]);
        }

        $user = User::create([
            'email' => $request->email_session,
            'password' => Hash::make($request->password),
        ]);

        if (Auth::attempt(['email' => $request->email_session, 'password' => $request->password])) {

            $data = [
                'name' => 'User',
                'id' => $user->id,
                'password' => $request->password
            ];

            Mail::to($request->email_session)->send(new VerifyEmail($data));
            return response()->json(['status' => 'success', 'message' => 'please check mail and verify your account.']);

        }


    }


    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function forgot_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $check_user = User::where('email', $request->email)->first();
        if ($check_user) {
            $check_user->password_reset_otp = rand(100000, 999999);
            $check_user->save();
            Session::put('email', $request->email);
            $data = [
                'name' => $check_user->name,
                'email' => $check_user->email,
                'otp' =>  $check_user->password_reset_otp,
            ];
            Mail::to($data['email'])->send(new PasswordForget($data));
            return response()->json(['success' => 'OTP sent successfully! check e-mail']);
        } else {
            return response()->json(['error' => 'user not found']);
        }
    }

    public function checkOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|min:6',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ]
        ], [
            'otp.required' => 'The OTP field is required.',
            'otp.min' => 'The OTP must be at least :min characters.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least :min characters.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $email = Session::get('email');
        $user = User::where('email', $email)->first();
        if ($request->otp != $user->password_reset_otp) {
            return response()->json(['error' => 'your otp is invalid']);
        } else {
            $user->password = Hash::make($request->password);
            $user->password_reset_otp = null;
            $user->email_verified_at = now();
            $user->save();
            if (Auth::attempt(['email' => $email, 'password' => $request->password])) {
                // $redirectUrl = (auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasAllRoles('dealer'))) ? 'admin/dashboard' : 'profile';
                // return response()->json(['message' => 'Reset Password Successfully.', 'redirectUrl' => $redirectUrl]);
                return response()->json(['message' => 'Reset Password Successfully.']);
            }
        }
    }



    public function userVerify($id, $password = null)
    {

        $user = User::find($id);
        if ($user) {
            $user->email_verified_at = now();
            // Ensure to hash the password if you are updating it
            // $user->password = bcrypt($request->password);

            $user->save();


            $data = [
                'id' => $user->id,
                'name' => 'user',
            ];

            Mail::to($user->email)->send(new WelcomeEmail($data));

            return redirect('/profile')->with('message', 'Your Email Verified Successfully! welcome to dashboard');
        }
    }


    public function againSendVerify(Request $request)
    {

        $user = $request->user();

        $data = [
            'name' => 'User',
            'id' => $user->id
        ];

        Mail::to($user->email)->send(new VerifyEmail($data));
        return redirect()->back()->with('message','please check mail and verify your account.');

    }
}
