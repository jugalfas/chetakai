@extends('layouts.email')

@section('css')
<style>
    .email-btn:hover {
        background-color: #020C17 !important;
    }

</style>
@endsection

@section('content')
<div style="display: inline-flex; align-items: center; gap: 8px; background-color: rgba(236, 179, 101, 0.1); color: #ECB365; padding-left: 12px; padding-right: 12px; padding-top: 4px; padding-bottom: 4px; border-radius: 9999px; font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;"><span style="height: 6px; width: 6px; border-radius: 9999px; background-color: #ECB365; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>Account Created</div>
<div style="margin-top: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 12px;">Welcome to Chetak! 🚀</h1>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">Hello {{$name}}</p>

    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">
        We're thrilled to have you on board! Chetak is your all-in-one platform for managing inspirational content and automating your Instagram presence.
    </p>
</div>
<div style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 24px; margin-top: 20px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="height: 40px; width: 40px; border-radius: 8px; background-color: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: #94a3b8;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rocket h-5 w-5" aria-hidden="true">
                <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path>
                <path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path>
                <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path>
                <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path>
            </svg>
        </div>
        <div>
            <p style="font-size: 10px; color: #62748e; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin: 0px; margin-bottom: 2px">Getting Started</p>
            <div class="text-slate-300 text-sm italic border-l-2 border-[#ECB365]/30 pl-4 space-y-3" style="font-style: italic; color: #cad5e2; font-size: 14px; line-height: 1.42857; padding-left: 16px; border-color: #ecb3654d; border-left-style: solid; border-left-width: 2px;">
                <p>1. Set up your Instagram connection</p>
                <p>2. Add your first quotes to the library</p>
                <p>3. Configure your posting schedule</p>
            </div>
        </div>
    </div>
</div>
<div style="padding-top: 16px; display: flex; justify-content: center;"><button class="email-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; min-height: 36px; background-color: #041C32; color: #ffffff; border: 1px solid #ffffff1a; padding: 16px; border-radius: 14px; font-size: 14px; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; width: auto;font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif; ">Go To Dashboard</button></div>
<div style="border-color: #ffffff0d; border-top-style: solid; border-top-width: 1px; margin-top: 24px; padding-top: 16px;">
    <p style="color: #90a1b9; font-size: 12px; line-height: 1.33333; font-style: italic;">If you have any questions or need assistance, our support team is always here to help. Just reply to this email!</p>
</div>
@endsection
