<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('customer.notifications.index', compact('notifications'));
    }

    public function markRead(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        abort_unless($notification, 404);

        $notification->markAsRead();

        return back();
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }

    /**
     * Register/refresh the device's FCM token so push notifications can
     * reach it. Called by the web (if it later adds web-push) and, mainly,
     * the mobile app after login.
     */
    public function registerFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        Auth::user()->update(['fcm_token' => $request->input('fcm_token')]);

        return response()->json(['success' => true]);
    }
}
