@extends('layouts.email')

@section('css')
<style>
  .email-btn:hover {
    background-color: #020C17 !important;
  }
</style>
@endsection

@section('content')
<div style="display: inline-flex; align-items: center; gap: 8px; background-color: rgba(236, 179, 101, 0.1); color: #ECB365; padding-left: 12px; padding-right: 12px; padding-top: 4px; padding-bottom: 4px; border-radius: 9999px; font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;"><span style="height: 6px; width: 6px; border-radius: 9999px; background-color: #ECB365; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>Action Required</div>
<div style="margin-top: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 12px;">Reset Your Password</h1>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">Hello {{$name}}</p>

    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">
        We received a request to reset your password for your Chetak account. Click the button below to set a new password.
    </p>
</div>
<div style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 24px; margin-top: 20px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="height: 40px; width: 40px; border-radius: 8px; background-color: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: #94a3b8;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock h-5 w-5" aria-hidden="true">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>
        <div>
            <p style="font-size: 10px; color: #62748e; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin: 0px; margin-bottom: 2px">Secure Link</p>
            <p style="margin: 0; opacity: 70%; max-width: 400px; color: #fff; font-weight: 500; font-size: 14px; line-height: 1.42857;    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{$resetUrl}}</p>
        </div>
    </div>
</div>
<div style="padding-top: 16px; display: flex; justify-content: center;"><a href="{{$resetUrl}}" class="email-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; min-height: 36px; background-color: #041C32; color: #ffffff; border: 1px solid #ffffff1a; padding: 5px 15px; border-radius: 14px; font-size: 14px; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; width: auto;font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif; text-decoration: none;">Reset Password</a></div>
<div style="border-color: #ffffff0d; border-top-style: solid; border-top-width: 1px; margin-top: 24px; padding-top: 16px;">
    <p style="color: #90a1b9; font-size: 12px; line-height: 1.33333; font-style: italic;">If you have any questions or need assistance, our support team is always here to help. Just reply to this email!</p>
</div>
@endsection
