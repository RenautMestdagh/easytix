<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        // Get all current session data
        $sessionData = Session::all();

        // Perform logout - this will clear auth-related session data
        Auth::guard('web')->logout();

        // Clear the CSRF token (important for security)
        Session::regenerateToken();

        // Restore all non-auth session data
        // Important to keep current temporary orders on logout
        foreach ($sessionData as $key => $value) {
            // Skip authentication-related session keys

            if ($this->isAuthSessionKey($key)) {
                continue;
            }

            // Restore the session value
            Session::put($key, $value);
        }
        session()->forget('original_user_id');

        return redirect('/');
    }

    /**
     * Determine if a session key is authentication-related
     */
    protected function isAuthSessionKey(string $key): bool
    {
        $authKeys = [
            'password_hash_web',
            'login_web_', // Wildcard for login_web_*
            'auth_',       // Wildcard for auth_*
        ];

        foreach ($authKeys as $authKey) {
            if (str_starts_with($key, $authKey)) {
                return true;
            }
        }

        return false;
    }
}
