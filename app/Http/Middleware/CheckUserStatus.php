<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only enforce status checks on the 'web' guard (frontend users)
        // Admins should be exempt from this check to prevent lockouts
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            if ($user->status !== 'active') {
                $status = $user->status;
                
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => "Your account is currently {$status}. Please contact support.",
                ]);
            }
        }

        return $next($request);
    }
}
