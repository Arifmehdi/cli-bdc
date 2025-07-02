<?php

// use App\Models\GeneralSetting;

// if (!function_exists('siteSetting_variable_create')) {
//     function siteSetting_variable_create()
//     {
//         $setting_data = GeneralSetting::select('site_title', 'separator')->first();
//         return $setting_data;
//     }
// }

use App\Models\Invoice;
use Illuminate\Support\Facades\Cookie;

if (!function_exists('queueZipCodeCookie')) {
function queueZipCodeCookie($value, $minutes = 10080) { // 7 days = 7 * 24 * 60 minutes
    if ($value) {

        Cookie::queue('zipcode', $value, $minutes);
    }
}

}


if (!function_exists('generateNewInvoiceId')) {
    function generateNewInvoiceId()
    {
        // Get the last invoice with the highest generated_id
        $lastInvoice = Invoice::withTrashed()->whereNotNull('generated_id')->orderBy('generated_id', 'desc')->first();
        // Initialize the new ID
        $newGeneratedId = 'P00001'; // Default value if no invoices are present

        if ($lastInvoice) {
            // Extract the numeric part of the generated_id using regex
            preg_match('/(\d+)/', $lastInvoice->generated_id, $matches);
            $numericPart = isset($matches[0]) ? (int)$matches[0] : 0;

            // Increment the numeric part
            $newNumericPart = str_pad($numericPart + 1, 5, '0', STR_PAD_LEFT);

            // Concatenate with the prefix 'P' to form the new generated_id
            $newGeneratedId =  'P'.$newNumericPart;
        }

        return $newGeneratedId;
    }
}



if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($number)
    {
        // Remove all non-numeric characters
        $number = preg_replace('/\D/', '', $number);

        // Check if the number has 10 digits (standard US phone number)
        if (strlen($number) === 10) {
            return '(' . substr($number, 0, 3) . ') ' . substr($number, 3, 3) . '-' . substr($number, 6);
        }

        // Return original number if it's not 10 digits
        return $number;
    }
}
