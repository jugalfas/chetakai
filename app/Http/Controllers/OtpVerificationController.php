<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerificationMail;
use App\Mail\WelcomeMail;
use Carbon\Carbon;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class OtpVerificationController extends Controller
{
    public function notice(): Response
    {
        return Inertia::render('Auth/VerifyOtp');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = Auth::user();

        if ($user->otp === $request->otp && $user->otp_expires_at > Carbon::now()) {
            $user->email_verified_at = Carbon::now();
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            Mail::to($user->email)->send(new WelcomeMail($user->first_name));
            return redirect()->intended(route('dashboard', false));
        }

        return back()->withErrors(['otp' => 'The provided OTP is invalid or has expired.']);
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));

        return back()->with('status', 'A new OTP has been sent to your email address.');
    }
}
