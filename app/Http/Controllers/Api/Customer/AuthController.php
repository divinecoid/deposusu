<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\MdxCustomer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        MdxCustomer::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('customer-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->where('role', 'customer')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi salah.'],
            ]);
        }

        $token = $user->createToken('customer-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $this->formatUser($request->user()),
        ]);
    }

    private function formatUser(User $user): array
    {
        $profile = $user->customerProfile;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $profile->phone ?? null,
            'address' => $profile->address ?? null,
            'is_verified' => (bool) ($profile->is_verified ?? false),
        ];
    }
}
