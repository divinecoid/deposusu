<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'data' => $notifications,
        ]);
    }

    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        abort_unless($notification, 404);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function registerFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $request->user()->update(['fcm_token' => $request->input('fcm_token')]);

        return response()->json(['success' => true]);
    }
}
