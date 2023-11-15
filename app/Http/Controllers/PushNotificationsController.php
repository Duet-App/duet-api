<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;
use App\Notifications\TaskReminder;
// use Notification;

class PushNotificationsController extends Controller
{
    // use Notifiable;

    public function store(Request $request) {
        $this->validate($request,[
            'endpoint'    => 'required',
            'keys.auth'   => 'required',
            'keys.p256dh' => 'required'
        ]);
        $endpoint = $request->endpoint;
        $token = $request->keys['auth'];
        $key = $request->keys['p256dh'];
        auth()->user()->updatePushSubscription($endpoint, $key, $token);

        return ['success' => true];
    }

    public function send(Request $request) {
        Notification::send(auth()->user(), new TaskReminder());
        // auth()->user()->notify(new TaskReminder());
        return ['success' => true];
    }
}
