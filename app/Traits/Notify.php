<?php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
trait Notify
{
    public function saveNewNotification($title, $message, $call_back_url, $to_user, $category)
    {
        $date_time_now = now();
        // $category = 'non-communication';
        $new_notification = Notification::create([
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'call_back_url' => $call_back_url,
            'user_id' => $to_user,
        ]);

        $new_notification->save();

        return $new_notification->id;
    }
}
