<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

/**
 * Fortify's default /login is the customer-facing page now — admin,
 * kasir, driver, and preparist each have their own dedicated login
 * page/controller (see RoleLoginController) instead of sharing this
 * one. If a non-customer account authenticates here, undo it and send
 * them to the login page that's actually theirs.
 */
class LoginResponse implements LoginResponseContract
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = $request->user();

        if (!$user->isCustomer()) {
            $routes = [
                'driver' => 'driver.login',
                'preparist' => 'preparist.login',
                'cashier' => 'kasir.login',
            ];
            $loginRoute = $routes[$user->role] ?? 'admin.login';

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Halaman ini khusus untuk customer. Silakan masuk lewat ' . route($loginRoute) . '.',
            ]);
        }

        return redirect()->intended(route('home'));
    }
}
