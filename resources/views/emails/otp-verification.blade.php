@extends('layouts.email')

@section('css')
<style>
  .email-btn:hover {
    background-color: #020C17 !important;
  }
</style>
@endsection

@section('content')
<div style="display: inline-flex; align-items: center; gap: 8px; background-color: rgba(236, 179, 101, 0.1); color: #ECB365; padding-left: 12px; padding-right: 12px; padding-top: 4px; padding-bottom: 4px; border-radius: 9999px; font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;"><span style="height: 6px; width: 6px; border-radius: 9999px; background-color: #ECB365; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>Security Alert</div>
<div style="margin-top: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 12px;">OTP Verification</h1>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625; margin-bottom: 8px;">Hello {{ $user->first_name }} {{ $user->last_name }},</p>
    <p style="color: #cbd5e1; font-size: 14px; line-height: 1.625;">
        Thank you for registering. Please use the following One-Time Password (OTP) to verify your email address:
    </p>
</div>
<div style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 24px; margin-top: 20px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="height: 40px; width: 40px; border-radius: 8px; background-color: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: #94a3b8;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height: 20px; width: 20px;" aria-hidden="true">
                <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
            </svg></div>
        <div>
            <p style="font-size: 10px; color: #62748e; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; margin: 0px; margin-bottom: 2px">Your Code</p>
            <p style="font-size: 24px; font-weight: 700; color: #ffffff;  margin: 0px; letter-spacing: 0.2em; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace">{{ $otp }}</p>
        </div>
    </div>
</div>
<div style="border-color: #ffffff0d; border-top-style: solid; border-top-width: 1px; margin-top: 24px; padding-top: 16px;"><p style="color: #90a1b9; font-size: 12px; line-height: 1.33333; font-style: italic;">This OTP is valid for 10 minutes. If you did not request this, please ignore this email.</p></div>
@endsection
