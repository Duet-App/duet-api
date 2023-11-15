<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskReminder;

class PushNotificationsController extends Controller
{
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

        return response()->json(['success' => true],200);
    }

    public function send(Request $request) {
        Notification::send(auth()->user(), new TaskReminder);
        return ['success' => true];
    }
}
