<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ImpersonationToken;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function login($token)
    {
        $impersonationToken = ImpersonationToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->firstOrFail();

        $impersonationToken->update(['used_at' => now()]);

        // Login as the user explicitly using the web guard
        Auth::guard('web')->login($impersonationToken->user);

        // Store impersonation metadata in the session for the UI banner
        session()->put('impersonated_user_name', $impersonationToken->user->first_name . ' ' . $impersonationToken->user->last_name);
        session()->put('impersonated_by_admin_id', $impersonationToken->admin_id);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        session()->forget(['impersonated_user_name', 'impersonated_by_admin_id']);

        // Check if admin is still logged in to their own dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.users.index');
        }

        return redirect()->route('welcome');
    }
}
