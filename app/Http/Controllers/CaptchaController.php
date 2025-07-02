<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    // CaptchaController.php

public function refreshCaptcha()
{
    try {


        session()->forget('mathcaptcha');

        // Reset and generate a new CAPTCHA
        app('mathcaptcha')->reset();
        $captchaLabel = app('mathcaptcha')->label();


        return response()->json([
            'label' => $captchaLabel
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    } catch (\Exception $e) {
        // Log the error and return a generic error message
        \Log::error('Captcha refresh error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to refresh CAPTCHA'], 500);
    }
}


// public function refreshCaptcha()
// {

//     Log::info('Session ID: ' . session()->getId());
//     Log::info('CAPTCHA Before Reset: ' . session('mathcaptcha'));


//     app('mathcaptcha')->reset();


//     Log::info('CAPTCHA After Reset: ' . session('mathcaptcha'));

//     return response()->json([
//         'label' => app('mathcaptcha')->label()
//     ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
//       ->header('Pragma', 'no-cache')
//       ->header('Expires', '0');
// }


}
