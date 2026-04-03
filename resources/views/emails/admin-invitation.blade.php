@extends('layouts.email')

@section('css')
<style>
    .email-btn:hover {
        background-color: #020C17 !important;
    }

</style>
@endsection

@section('content')
<div style="display: inline-flex; align-items: center; gap: 8px; background-color: rgba(236, 179, 101, 0.1); color: #ECB365; padding-left: 12px; padding-right: 12px; padding-top: 4px; padding-bottom: 4px; border-radius: 9999px; font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;"><span style="height: 6px; width: 6px; border-radius: 9999px; background-color: #ECB365; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>Admin Invitation</div>
<div style="margin-top: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 12px;">Welcome to Chetak Admin! 🚀</h1>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">Hello {{$name}}</p>

    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">
        You have been invited to join the Chetak team as an Administrator. This role gives you access to the admin dashboard where you can help manage the platform.
    </p>
</div>
<div style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 24px; margin-top: 20px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="height: 40px; width: 40px; border-radius: 8px; background-color: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: #94a3b8;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-5 w-5" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2-1 4-2 7-2 2.5 0 4.5 1.2 7 2a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>
        </div>
        <div>
            <p style="font-size: 10px; color: #62748e; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin: 0px; margin-bottom: 2px">Your Role</p>
            <div class="text-slate-300 text-sm italic border-l-2 border-[#ECB365]/30 pl-4 space-y-3" style="font-style: italic; color: #cad5e2; font-size: 14px; line-height: 1.42857; padding-left: 16px; border-color: #ecb3654d; border-left-style: solid; border-left-width: 2px;">
                <p>Role: <span style="font-weight: 700; color: #ECB365; text-transform: capitalize;">{{ str_replace('_', ' ', $role) }}</span></p>
                <p>To get started, please set your password by clicking the button below.</p>
            </div>
        </div>
    </div>
</div>
<div style="padding-top: 16px; display: flex; justify-content: center;"><a href="{{ $resetUrl }}" class="email-btn" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; min-height: 36px; background-color: #041C32; color: #ffffff; border: 1px solid #ffffff1a; padding: 16px; border-radius: 14px; font-size: 14px; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; width: auto;font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif; ">Set Password</a></div>
<div style="border-color: #ffffff0d; border-top-style: solid; border-top-width: 1px; margin-top: 24px; padding-top: 16px;">
    <p style="color: #90a1b9; font-size: 12px; line-height: 1.33333; font-style: italic;">If you're having trouble clicking the "Set Password" button, copy and paste the URL below into your web browser: 
        <br>
        <span style="font-size: 10px; color: #62748e; word-break: break-all;">{{ $resetUrl }}</span>
    </p>
    <p style="color: #90a1b9; font-size: 12px; line-height: 1.33333; font-style: italic; margin-top: 16px;">If you have any questions or need assistance, our support team is always here to help. Just reply to this email!</p>
</div>
@endsection
