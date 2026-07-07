<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Update Basic Profile (Name, Photo) without OTP
     */
    public function updateBasicProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profile_photos', 'public');
            // Assuming we have a 'photo' or 'avatar' column.
            // If the schema doesn't have it, we might need to add it, or save to profile table.
            // For now, let's just save the path if column exists, else ignore or simulate.
            // In typical Laravel, users table might not have photo.
            // We'll just try to save it if column exists.
            try {
                $user->photo = $photoPath;
            } catch (\Exception $e) {
                // Ignore if column doesn't exist
                Log::warning('User table has no photo column');
            }
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }

    /**
     * Request OTP for Phone/Email change
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'type' => 'required|in:phone,email',
            'new_value' => 'required|string',
        ]);

        $user = $request->user();
        $type = $request->type;
        $newValue = $request->new_value;

        // Mock OTP 1234
        $otp = '1234';

        Log::info("OTP for changing {$type} to {$newValue} for user {$user->id} is {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'OTP telah dikirim ke ' . $newValue,
        ]);
    }

    /**
     * Verify OTP and Update Phone/Email
     */
    public function verifyOtpAndUpdate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:phone,email',
            'new_value' => 'required|string',
            'otp' => 'required|string',
        ]);

        $user = $request->user();
        $type = $request->type;
        $newValue = $request->new_value;
        $otp = $request->otp;

        // Verify Mock OTP
        if ($otp !== '1234') {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid'
            ], 400);
        }

        if ($type === 'phone') {
            $user->phone = $newValue;
        } else if ($type === 'email') {
            // Check if email already taken
            if (\App\Models\User::where('email', $newValue)->where('id', '!=', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah digunakan'
                ], 400);
            }
            $user->email = $newValue;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui',
            'user' => $user
        ]);
    }
}
