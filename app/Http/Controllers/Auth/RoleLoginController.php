<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Dedicated login page per role, instead of one shared /login for
 * everyone. Fortify's default /login is customer-only (see
 * LoginResponse); this controller serves the other four.
 */
class RoleLoginController extends Controller
{
    /** @var array<string, array{label: string, roles: string[], redirect: string}> */
    private const ROLES = [
        'admin' => [
            'label' => 'Admin',
            // The admin panel is shared by every back-office role, not just
            // the literal "admin" role — see the sidebar's own isAdmin()-
            // gated sections for the finer-grained UI differences.
            'roles' => ['admin', 'owner', 'adminoperasional', 'staffgudang', 'finance'],
            'redirect' => 'admin.dashboard',
        ],
        'kasir' => [
            'label' => 'Kasir',
            'roles' => ['cashier'],
            'redirect' => 'admin.kasir.index',
        ],
        'driver' => [
            'label' => 'Driver',
            'roles' => ['driver'],
            'redirect' => 'driver.dashboard',
        ],
        'preparist' => [
            'label' => 'Preparist',
            'roles' => ['preparist'],
            'redirect' => 'preparist.dashboard',
        ],
    ];

    public function show(Request $request)
    {
        $meta = $this->meta($request);

        if (Auth::check()) {
            $user = Auth::user();

            // Already logged in as the right kind of account for this page —
            // just go straight to the destination instead of asking again.
            if (in_array($user->role, $meta['roles'], true)) {
                return redirect()->intended(route($meta['redirect']));
            }

            // Logged in as something else entirely (e.g. a customer session
            // still active in this browser) — send them to their own home
            // rather than showing a login form they can't usefully submit.
            return redirect()->route($user->isCustomer() ? 'home' : 'admin.dashboard');
        }

        return view('auth.role-login', [
            'roleKey' => $request->route('role'),
            'meta' => $meta,
        ]);
    }

    public function store(Request $request)
    {
        $roleKey = $request->route('role');
        $meta = $this->meta($request);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Email atau password salah.']);
        }

        if (!in_array($user->role, $meta['roles'], true)) {
            throw ValidationException::withMessages([
                'email' => "Akun ini bukan akun {$meta['label']}.",
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route($meta['redirect']));
    }

    private function meta(Request $request): array
    {
        $roleKey = $request->route('role');

        abort_unless(isset(self::ROLES[$roleKey]), 404);

        return self::ROLES[$roleKey];
    }
}
