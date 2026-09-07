<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->isPreparist()) {
            return redirect()->intended(route('preparist.dashboard'));
        }

        if ($user->isDriver()) {
            return redirect()->intended(route('driver.dashboard'));
        }

        return redirect()->intended(route('home'));
    }
}
